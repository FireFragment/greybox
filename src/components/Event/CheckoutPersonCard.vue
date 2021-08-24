<template>
  <q-card class="thin-header-card normal-margin-card">
    <q-card-section class="bg-primary text-white card-header">
      <div class="row items-center no-wrap">
        <div class="col">
          <div class="text-h6">
            {{ person.person.name }} {{ person.person.surname }}
          </div>
        </div>

        <div class="col-auto" v-if="menu">
          <q-btn color="white" round flat icon="fas fa-ellipsis-v">
            <q-menu cover auto-close>
              <q-list class="smaller-margin-menu">
                <q-item clickable @click="removePerson">
                  <q-item-section avatar>
                    <q-icon name="fas fa-trash" />
                  </q-item-section>
                  <q-item-section>{{
                    $tr("general.confirmModal.remove", null, false)
                  }}</q-item-section>
                </q-item>
              </q-list>
            </q-menu>
          </q-btn>
        </div>
      </div>
    </q-card-section>

    <q-card-section>
      <div
        v-for="(value, fieldName) in registration"
        v-bind:key="'registration-fields-' + fieldName"
      >
        <template
          v-if="
            showRegistrationFields.includes(fieldName) &&
              (fieldName !== 'teamName' || value) &&
              (fieldName !== 'role' || roleName)
          "
        >
          <dt>{{ $tr("registrationFields." + fieldName) }}:</dt>

          <dd>
            <template v-if="fieldName === 'role'">
              {{ roleName }}
            </template>
            <template v-else-if="fieldName === 'accommodation'">
              {{
                value ? $tr("checkout.values.yes") : $tr("checkout.values.no")
              }}
            </template>
            <template v-else>
              {{ value }}
            </template>
          </dd>
        </template>
      </div>

      <div v-if="person.person.speaker_status">
        <dt>{{ $tr("fields.speakerStatus") }}:</dt>
        <dd>{{ person.person.speaker_status.toUpperCase() }}</dd>
      </div>

      <div>
        <dt>{{ $tr("registrationFields.meals") }}:</dt>
        <dd>
          {{
            registration.meals
              ? $tr("checkout.values.yes")
              : $tr("checkout.values.no")
          }}
        </dd>
      </div>

      <div
        v-if="registration.meals && person.person.dietary_requirement"
      >
        <dt>{{ $tr("fields.diet") }}:</dt>
        <dd>{{ dietaryRequirement }}</dd>
      </div>

      <div v-if="person.person.email && person.person.email">
        <dt>{{ $tr("auth.fields.email", null, false) }}:</dt>
        <dd>{{ person.person.email }}</dd>
      </div>
    </q-card-section>

    <q-separator v-if="registration.accommodation" inset />

    <q-card-section v-if="registration.accommodation">
      <div
        v-for="(value, fieldName) in person.person"
        v-bind:key="'person-fields-' + fieldName"
      >
        <template
          v-if="
            value &&
              fieldName.substr(-4) !== 'name' &&
              !ignorePersonFields.includes(fieldName)
          "
        >
          <dt>{{ $tr("fields." + fieldName) }}:</dt>
          <dd v-if="fieldName === 'birthdate'">
            {{ getDate(value, "D. M. YYYY") }}
            <!--
            {{ /*(value).format("D. M. Y")*/ }}
          --></dd>
          <dd v-else-if="value != null">
            {{ value }}
          </dd>
        </template>
      </div>
    </q-card-section>
  </q-card>
</template>

<script>
import { date } from 'quasar';
import { mapGetters } from 'vuex';

/* eslint-disable */
export default {
  props: {
    person: Object,
    registration: Object,
    personIndex: Number,
    possibleDiets: Array,
    menu: { type: Boolean, required: false, default: true },
  },
  emits: ['remove'],
  data() {
    return {
      translationPrefix: 'tournament.',
      roles: {},
      showRegistrationFields: ['teamName', 'accommodation', 'role'],
      ignorePersonFields: ['dietary_requirement', 'email', 'id', 'created_at', 'updated_at', 'old_greybox_id'],
    };
  },
  computed: {
    ...mapGetters('roles', [
      'role',
    ]),
    roleName() {
      if (this.person.role) {
        return this.$tr(this.person.role.name);
      }

      const role = this.role(this.person.registration?.role);
      if (!role) {
        return null;
      }

      return this.$tr(role.name);
    },
    dietaryRequirement() {
      const id = this.person.person.dietary_requirement;

      // Get dietary requirement name
      const name = this.$tr(
        this.possibleDiets.filter((item) => item.id === id)[0].name,
      );

      // Capitalize
      return name.charAt(0).toUpperCase() + name.slice(1);
    },
  },
  name: 'CheckoutPersonCard',
  methods: {
    removePerson() {
      this.$confirm({
        confirm: this.$tr('general.confirmModal.remove', null, false),
        message: this.$tr('checkout.actions.removeModal.title'),
      }).onOk(() => {
        this.$emit('remove', this.personIndex);
      });
    },
    getDate: date.formatDate,
  },
};
</script>

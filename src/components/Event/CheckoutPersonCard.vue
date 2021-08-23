<template>
  <div class="col-12 col-sm-6 col-md-4 col-lg-3 q-pa-xs items-stretch">
    <q-card class="thin-header-card normal-margin-card">
      <q-card-section class="bg-primary text-white card-header">
        <div class="row items-center no-wrap">
          <div class="col">
            <div class="text-h6">
              {{ person.person.name }} {{ person.person.surname }}
            </div>
          </div>

          <div class="col-auto">
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
              ['teamName', 'accommodation', 'role'].includes(fieldName) &&
                (fieldName !== 'teamName' || value)
            "
          >
            <dt>{{ $tr("registrationFields." + fieldName) }}:</dt>
            <dd v-if="fieldName === 'role'">
              {{ roleName }}
            </dd>
            <dd v-else-if="fieldName === 'accommodation'">
              {{
                value ? $tr("checkout.values.yes") : $tr("checkout.values.no")
              }}
            </dd>
            <dd v-else>
              {{ value }}
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
                fieldName !== 'dietary_requirement' &&
                fieldName !== 'email'
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
  </div>
</template>

<script>
import { date } from 'quasar';

/* eslint-disable */
export default {
  props: {
    person: Object,
    registration: Object,
    personIndex: Number,
    possibleDiets: Array,
  },
  emits: ['remove'],
  data() {
    return {
      translationPrefix: 'tournament.',
      roles: {},
    };
  },
  computed: {
    roleName() {
      let roleObject = null;
      let isLoading = false;

      /** TO REFACTOR **/
      // Promise to return all roles
      const rolesPromise = new Promise((resolve, reject) => {
        // Load roles from cache if available
        const cached = this.$db('rolesList');
        if (cached) return resolve([cached, isLoading]);

        if (!isLoading) this.$bus.$emit('fullLoader', true);

        // Not cached -> load from API
        this.$api({
          url: 'role',
          method: 'get',
        })
            .then((d) => {
              this.$db('rolesList', d.data);
              resolve([d.data, true]);
            })
            .catch(reject);
      });

      rolesPromise.then(([roles, isLoading]) => {
        // Check if roles are present in event's prices
        for (const role of roles) {
          let isPresent = false;
          for (const price of event.prices) {
            if (price.role.id === role.id) {
              isPresent = true;
              break;
            }
          }

          if (isPresent) {
            // Debater role is present -> push team role
            if (role.id === 1) {
              this.roles[0] = {
                value: 0,
                label: 'tournament.types.team',
                icon: 'users',
              };
            }

            // Individual debater should be hidden on PDS
            if (role.id !== 1 || !this.$isPDS)
                // Push role to role list
            {
              this.roles[role.id] = {
                value: role.id,
                label: role.name,
                icon: role.icon,
              };
            }
          }
        }

        this.$db('rolesList').forEach((item) => {
          if (item.id === this.registration.role) roleObject = item;
        });
        if (roleObject) return this.$tr(roleObject.name);

        if (isLoading) return this.$bus.$emit('fullLoader', false);
      });

      // For Bugsnag to catch
      console.log(this.$db('rolesList'));
      console.log(this.person);
      console.error(
        `Missing role ${this.registration.role}, see logs above`,
      );
      return '';
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
  /*
  created() {
    console.log(this.person);
    console.log(this.registration);
  }
   */
};
</script>

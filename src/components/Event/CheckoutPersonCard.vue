<template>
  <div class="col-12 col-sm-6 col-md-3 q-pa-xs items-stretch">
    <q-card>
      <q-card-section>
        <div class="row items-center no-wrap">
          <div class="col">
            <div class="text-h6">
              {{ person.person.name }} {{ person.person.surname }}
            </div>
          </div>

          <div class="col-auto">
            <q-btn color="grey-7" round flat icon="more_vert">
              <q-menu cover auto-close>
                <q-list class="checkout-person-card-menu">
                  <q-item clickable @click="removePerson">
                    <q-item-section avatar>
                      <q-icon name="fas fa-trash-alt" />
                    </q-item-section>
                    <q-item-section>{{
                      $tr("checkout.actions.remove")
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
          v-for="(value, fieldName) in person.person"
          v-bind:key="'person-fields-' + fieldName"
        >
          <template v-if="value && fieldName.substr(-4) !== 'name'">
            <dt>{{ $tr("fields." + fieldName) }}:</dt>
            <dd v-if="fieldName === 'vegetarian'">
              {{
                value ? $tr("checkout.values.yes") : $tr("checkout.values.yes")
              }}
            </dd>
            <dd v-else-if="fieldName === 'birthdate'">
              {{ value | moment("D. M. Y") }}
            </dd>
            <dd v-else-if="value != null">
              {{ value }}
            </dd>
          </template>
        </div>
      </q-card-section>

      <q-separator inset />

      <q-card-section>
        <div
          v-for="(value, fieldName) in person.registration"
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
      </q-card-section>
    </q-card>
  </div>
</template>

<script>
export default {
  props: {
    person: Object,
    personIndex: Number
  },
  data() {
    return {
      translationPrefix: "tournament."
    };
  },
  computed: {
    roleName() {
      let roleObject;
      this.$db("rolesList").forEach(item => {
        if (item.id === this.person.registration.role) roleObject = item;
      });
      return this.$tr(roleObject.name);
    }
  },
  name: "CheckoutPersonCard",
  methods: {
    removePerson() {
      this.$q
        .dialog({
          class: "simple-confirm-dialog",
          title: this.$tr("general.confirmAction", null, false),
          message: this.$tr("checkout.actions.removeModal.title"),
          cancel: this.$tr("checkout.actions.removeModal.cancel"),
          ok: {
            label: this.$tr("checkout.actions.removeModal.remove")
          }
        })
        .onOk(() => {
          this.$emit("remove", this.personIndex);
        });
    }
  }
};
</script>

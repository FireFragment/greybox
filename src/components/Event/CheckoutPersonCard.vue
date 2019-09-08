<template>
  <div class="col-3 q-pa-xs items-stretch">
    <q-card>
      <q-card-section>
        <div class="text-h6">
          {{ person.person.name }} {{ person.person.surname }}
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
              {{ value ? "Ano" : "Ne" }}
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
              {{ value ? "Ano" : "Ne" }}
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
    person: Object
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
  name: "CheckoutPersonCard"
};
</script>

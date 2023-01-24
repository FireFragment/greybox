<template>
    <q-page padding>
    <h1 class="text-center text-h4">{{ $tr('currentRegistrations.title') }}</h1>
        <template v-for="(entry, id) in events" :key="id">
          <div class="col-12 q-px-sm">
            <h5 class="q-mt-lg q-mb-xs">{{ $tr(entry.event.name) }}</h5>
          </div>
          <checkout-person-card
            v-for="(registration, index) in entry.registrations"
            :key="JSON.stringify(registration)"
            :person="{
              ...registration,
              person: {
                ...registration.person,
                dietary_requirement: registration.person.dietary_requirement?.id,
              },
            }"
            :registration="registration"
            :person-index="index"
            :possible-diets="entry.event.dietaryRequirements"
            :menu="false"
          />
        </template>
    </q-page>
</template>

<script>

</script>

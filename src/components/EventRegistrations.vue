<template>
    <template v-if="entry">
        <div class="col-12 q-px-sm">
            <h5 class="q-mt-lg q-mb-xs">{{ $tr(entry.event.name) }}</h5>
        </div>
        <checkout-person-card v-for="(registration, index) in entry.registrations"
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
</template>

<script lang="ts">
import { EventPersonRegistrations } from 'src/types/event';
import { defineComponent } from 'vue';
import CheckoutPersonCard from 'components/Event/CheckoutPersonCard.vue';

const EventRegistrationsProps = {
  entry: {
    type: Object as () => EventPersonRegistrations,
    required: true,
  },
};

export default defineComponent({
  name: 'EventRegistrations',
  props: EventRegistrationsProps,
  components: {
    CheckoutPersonCard,
  },
});

</script>

<template>
  <q-page padding>
    <h1 class="text-center text-h4">
      {{ $tr('myRegistrations.title') }} -
      {{ event ? $tr(event.name, null, false) : '-' }}
    </h1>
    <EventRegistrations :event-id="$route.params.id" type="user" />
  </q-page>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import EventRegistrations from 'components/EventRegistrations.vue';

export default defineComponent({
  name: 'MyRegistrationsDetail',
  components: {
    EventRegistrations,
  },
  data() {
    return {
      translationPrefix: 'user.',
    };
  },
  computed: {
    eventId() {
      return this.$route.params.id;
    },
    event(): Event {
      // eslint-disable-next-line max-len
      // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
      return <Event> this.$store.getters['events/event'](this.eventId);
    },
  },
  async created() {
    // Not cached -> load from API
    await this.$store.dispatch('events/load', this.eventId);
  },
});
</script>

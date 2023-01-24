<template>
  <q-page padding>
    <h1 class="text-center text-h4">
      {{ $tr('myRegistrations.title') }} -
      {{ event ? $tr(event.name, null, false) : '-' }}
    </h1>
    <EventRegistrations :event-id="eventId" type="user" />
  </q-page>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import EventRegistrations from 'components/EventRegistrations.vue';
import { EventFull } from 'src/types/event';

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
    eventId(): number {
      return parseInt(<string> this.$route.params.id, 10);
    },
    event(): EventFull {
      // eslint-disable-next-line max-len
      // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
      return <EventFull> this.$store.getters['events/fullEvent'](this.eventId);
    },
  },
  async created() {
    // Not cached -> load from API
    await this.$store.dispatch('events/loadFull', this.eventId);
  },
});
</script>

<template>
  <q-page padding>
    <h1 class="text-center text-h4">
      {{ event ? $tr(event.name, null, false) : '-' }} - {{ $tr('viewTypes.editRoles') }}
    </h1>
    <!-- TODO -->
  </q-page>
</template>

<script lang="ts">

import { EventFull } from 'src/types/event';

import { defineComponent } from 'vue';
import { $setTitle } from 'boot/custom';

export default defineComponent({
  name: 'EditEvent',
  computed: {
    event(): EventFull {
      // eslint-disable-next-line max-len
      // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
      return <EventFull> this.$store.getters['events/fullEvent'](this.eventId);
    },
    eventId(): number {
      const idParam: string | string[] = this.$route.params.id;
      if (typeof idParam !== 'string') {
        return 0;
      }
      return parseInt(idParam, 10);
    },
  },
  async created() {
    // Not cached -> load from API
    await Promise.all([
      this.$store.dispatch('events/loadFull', this.eventId),
    ]);
    $setTitle(`${<string> this.$tr(this.event.name)} - ${(<string> this.$tr('titles.admin.editEventRoles', null, false)).toLowerCase()}`);
  },
  data() {
    return {
      translationPrefix: 'admin.events.',
    };
  },
});
</script>

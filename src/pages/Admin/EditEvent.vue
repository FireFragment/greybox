<template>
  <q-page padding>
    <h1 class="text-center text-h4">
      {{ event ? $tr(event.name, null, false) : '-' }} - {{ $tr('viewTypes.edit') }}
    </h1>
    <EventForm @submit="submit" :event="event" v-if="event" />
  </q-page>
</template>

<script lang="ts">

import { EventFull } from 'src/types/event';

import { defineComponent } from 'vue';
import { $setTitle } from 'boot/custom';
import EventForm from 'components/Admin/EventForm.vue';

export default defineComponent({
  name: 'EditEvent',
  components: { EventForm },
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
    $setTitle(`${<string> this.$tr(this.event.name)} - ${(<string> this.$tr('titles.admin.editEvent', null, false)).toLowerCase()}`);
  },
  data() {
    return {
      translationPrefix: 'admin.events.',
    };
  },
  methods: {
    submit(formData: Record<string, string>) {
      this.$bus.$emit('fullLoader', true);
      this.$api({
        url: `event/${this.eventId}`,
        data: formData,
        method: 'PUT',
      }).then(async () => {
        // Reload simple events storage
        await this.$store.dispatch('events/load', [true, true]);
        // TODO - alert
      }).finally(() => {
        this.$bus.$emit('fullLoader', false);
      });
    },
  },
});
</script>

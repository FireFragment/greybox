<template>
  <q-page padding>
    <h1 class="text-center text-h4">{{ $tr("admin.eventRegistrations.chooseEvent") }}</h1>
    <div class="flex column">
      <div
        v-for="event in events"
        v-bind:key="event.id"
        class="text-center"
      >
        <h2 class="text-h5">{{ $tr(event.name) }}</h2>
        <q-btn
          v-for="[label, icon] in [['people', 'user'], ['teams', 'users']]"
          v-bind:key="icon"
          class="q-mx-sm q-mb-lg q-mt-0 q-pa-md"
          :icon="`fas fa-${icon}`"
          color="primary"
          :label="$tr(`admin.eventRegistrations.viewTypes.${label}`)"
          :to="$translatedRouteLink({
            name: 'admin.eventRegistrations',
            params: {
              id: event.id
            },
          })"
        />
      </div>
    </div>
    <div v-if="!Object.keys(events).length" class="empty-info">
      {{ $tr('tournament.empty') }}
    </div>
  </q-page>
</template>

<script lang="ts">

import { defineComponent } from 'vue';
import { organizesEvent } from 'boot/auth';

export default defineComponent({
  name: 'EventRegistrations',
  computed: {
    events() {
      // eslint-disable-next-line max-len
      // eslint-disable-next-line @typescript-eslint/no-unsafe-return,@typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
      return this.$store.getters['events/filteredEvents'](organizesEvent);
    },
  },
});
</script>

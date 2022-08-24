<template>
  <q-page padding>
    <h1 class="text-center text-h4">{{ $tr("admin.eventRegistrations.chooseEvent") }}</h1>
    <div class="flex column">
      <q-btn
        v-for="event in events"
        v-bind:key="event.id"
        class="q-mx-auto q-my-lg q-pa-md"
        icon="fas fa-trophy"
        color="primary"
        :label="$tr(event.name)"
        :to="
          $path('admin.events') +
            '/' +
            event.id +
            '-' +
            $slug($tr(event.name) + ' ' + event.place)
        "
      />
    </div>
    <div v-if="!Object.keys(events).length" class="empty-info">
      {{ $tr('tournament.empty') }}
    </div>
  </q-page>
</template>

<script>

import { mapState } from 'vuex';

export default {
  name: 'EventRegistrations',
  computed: {
    // TODO - filter out those events that the user doesn't have admin permissions for
    ...mapState('events', [
      'events',
    ]),
  },
};
</script>

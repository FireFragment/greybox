<template>
  <q-page padding>
    <h1 class="text-center text-h4">{{ $tr('title') }}</h1>

    <p class="text-center">
      <q-btn color="primary"
             icon="fas fa-clock"
             :label="$tr(isHistorical ? 'showCurrent' : 'showHistorical')"
             :to="isHistorical ? $path('user.myRegistrations') : $translatedRouteLink({
               name: 'user.myRegistrationsDetail',
               params: {
                 id: $tr('paths.user.historicalRegistrations', null, false),
               }
             })" />
    </p>

    <div class="flex column justify-center" v-if="events">
      <NoDataMessage
        v-if="events.length === 0"
        :message="$tr('empty')"
      />
      <template v-else>
        <div v-for="(event, id) in events"
             :key="id"
             class="q-my-sm text-center">
          <q-btn
            color="white"
            text-color="black"
            :to="$translatedRouteLink({
              name: 'user.myRegistrationsDetail',
              params: {
                id: event.id
              },
            })"
            class="my-registration-event-card">
            <div class="flex column q-pa-sm">
              <div class="text-subtitle2">{{ $tr(event.name) }}</div>
              <div class="text-caption text-grey-9">
                {{ getDate(event.beginning, 'D. M. YYYY') }}
              </div>
              <div class="text-caption text-grey text-weight-light">{{ event.place }}</div>
            </div>
          </q-btn>
        </div>
      </template>
    </div>

  </q-page>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { Event } from 'src/types/event';
import NoDataMessage from 'components/NoDataMessage.vue';
import { date } from 'quasar';

export default defineComponent({
  name: 'MyOfRegistrations',
  components: {
    NoDataMessage,
  },
  data() {
    return {
      translationPrefix: 'user.myRegistrations.',
    };
  },
  computed: {
    events(): Event[] {
      // eslint-disable-next-line max-len
      // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
      return <Event[]> this.$store.getters['events/allEvents']
        .filter((e: Event) => e.current === !this.isHistorical);
    },
    isHistorical(): boolean {
      return <boolean> this.$route.meta?.isHistorical ?? false;
    },
  },
  async created() {
    await this.$store.dispatch('events/loadAll');
  },
  methods: {
    getDate: date.formatDate,
  },
});
</script>

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

    <div class="row justify-center" v-if="events">
      <NoDataMessage
        v-if="events.length === 0"
        :message="$tr('empty')"
      />
      <template v-else>
        <q-list bordered separator>
          <q-item
            clickable
            v-ripple
            v-for="(entry, id) in events"
            :key="id"
            :to="$translatedRouteLink({
              name: 'user.myRegistrationsDetail',
              params: {
                id: entry.id
              },
            })">
            <q-item-section>
              <q-item-label>{{ $tr(entry.name) }}</q-item-label>
              <q-item-label caption>{{ entry.place }}</q-item-label>
            </q-item-section>
          </q-item>
        </q-list>
      </template>
    </div>

  </q-page>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { Event } from 'src/types/event';
import NoDataMessage from 'components/NoDataMessage.vue';

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
});
</script>

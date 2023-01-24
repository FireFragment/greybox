<template>
  <q-page padding>
    <h1 class="text-center text-h4">{{ $tr('myRegistrations.title') }}</h1>

    <div class="row justify-center" v-if="events">
      <NoDataMessage
        v-if="Object.keys(events).length === 0"
        :message="$tr('myRegistrations.empty')"
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
      translationPrefix: 'user.',
    };
  },
  computed: {
    // TODO - distinguish current and historical events
    events(): Event {
      // eslint-disable-next-line max-len
      // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
      return <Event> this.$store.getters['events/allEvents'];
    },
  },
  async created() {
    await this.$store.dispatch('events/loadAll');
  },
});
</script>

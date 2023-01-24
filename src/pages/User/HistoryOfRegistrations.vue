<template>
  <q-page padding>
    <h1 class="text-center text-h4">{{ $tr('historyOfRegistrations.title') }}</h1>

    <div class="row justify-center">
      <NoDataMessage
        v-if="Object.keys(events).length === 0"
        :message="$tr('historyOfRegistrations.empty')"
      />
      <template v-else>
        <q-list bordered separator>
          <q-item
            clickable
            v-ripple
            v-for="(entry, id) in events"
            :key="id"
            :to="$translatedRouteLink({
              name: 'user.eventRegistrations',
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
import { DBValue } from 'boot/custom';
import { AxiosResponse } from 'axios';
import { TranslationPrefixData } from 'boot/i18n';
import {
  DetailedEventRegistration, Event,
} from 'src/types/event';
import NoDataMessage from 'components/NoDataMessage.vue';

export const DBkey = 'historical-registrations';

interface historyOfRegistrationsData extends TranslationPrefixData {
  translationPrefix: string;
  events: Event[];
}

export default defineComponent({
  name: 'historyOfRegistrations',
  components: {
    NoDataMessage,
  },
  data(): historyOfRegistrationsData {
    return {
      translationPrefix: 'user.',
      events: [],
    };
  },
  methods: {
    async loadRegistrations() {
      this.$bus.$emit('fullLoader', true);
      await this.$api({
        url: `user/${this.$auth.user()!.id}/registration`,
        method: 'get',
      })
        .then(({ data }: AxiosResponse<DetailedEventRegistration[]>) => {
          if (!data.length) {
            return;
          }

          this.events = data.map((el) => el.event);
          console.log(this.events);
        });
      this.$db(DBkey, <DBValue><unknown> this.events, true);
      this.$bus.$emit('fullLoader', false);
    },
  },
  created() {
    void this.loadRegistrations();
  },
});
</script>

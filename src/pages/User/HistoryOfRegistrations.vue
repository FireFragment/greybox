<template>
  <q-page padding>
    <h1 class="text-center text-h4">{{ $tr('historyOfRegistrations.title') }}</h1>

    <div class="row">
      <NoDataMessage
        v-if="Object.keys(events).length === 0"
        :message="$tr('historyOfRegistrations.empty')"
      />
      <template v-else>
        <template v-for="(entry, id) in events" :key="id">
          <div class="col-12 q-px-sm">
            <h5 class="q-mt-lg q-mb-xs">{{ $tr(entry.event.name) }}</h5>
          </div>
          <checkout-person-card
            v-for="(registration, index) in entry.registrations"
            :key="JSON.stringify(registration)"
            :person="{
              ...registration,
              person: {
                ...registration.person,
                dietary_requirement: registration.person.dietary_requirement?.id,
              },
            }"
            :registration="registration"
            :person-index="index"
            :possible-diets="entry.event.dietaryRequirements"
            :menu="false"
          />
        </template>
      </template>
    </div>

  </q-page>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { DBValue } from 'boot/custom';
import { AxiosResponse } from 'axios';
import CheckoutPersonCard from 'components/Event/CheckoutPersonCard.vue';
import { TranslationPrefixData } from 'boot/i18n';
import { mapGetters } from 'vuex';
import { Event, EventFull, EventRegistration } from 'src/types/event';
import NoDataMessage from 'components/NoDataMessage.vue';

export const DBkey = 'current-registrations';

interface EventPersonRegistrations {
  event: EventFull;
  registrations: EventRegistration[];
}

type EventPersonRegistrationsData = Record<number, EventPersonRegistrations>;

interface historyOfRegistrationsData extends TranslationPrefixData {
  translationPrefix: string;
  events: EventPersonRegistrationsData;
}

export default defineComponent({
  name: 'historyOfRegistrations',
  components: {
    NoDataMessage,
    CheckoutPersonCard,
  },
  data(): historyOfRegistrationsData {
    return {
      translationPrefix: 'user.',
      events: {},
    };
  },
  computed: {
    ...mapGetters('events', [
      'eventsArray',
    ]),
  },
  watch: {
    eventsArray() {
      void this.loadRegistrations();
    },
  },
  methods: {
    async loadRegistrations() {
      const events: Event[] = <Event[]> this.eventsArray;
      if (!events.length) {
        return;
      }

      const cached: DBValue = this.$db(DBkey);
      if (cached) {
        this.events = <EventPersonRegistrationsData><unknown>cached;
        return;
      }

      this.$bus.$emit('fullLoader', true);
      await Promise.all(
        events.map(async (event: Event) => {
          await this.$api({
            url: `event/${event.id}/user/${this.$auth.user()!.id}/registration`,
            method: 'get',
          })
            .then(async ({ data }: AxiosResponse<EventRegistration[]>) => {
              if (!data.length) {
                return;
              }

              await this.$store.dispatch('events/loadFull', event.id);

              this.events[event.id] = {
                // eslint-disable-next-line max-len
                // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
                event: <EventFull> this.$store.getters['events/fullEvent'](event.id),
                registrations: data,
              };
            });
        }),
      );
      this.$db(DBkey, <DBValue><unknown> this.events, true);
      this.$bus.$emit('fullLoader', false);
    },
  },
  created() {
    void this.loadRegistrations();
  },
});
</script>

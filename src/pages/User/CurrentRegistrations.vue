<template>
  <q-page padding>
    <h1 class="text-center text-h4">{{ $tr('currentRegistrations.title') }}</h1>

    <div class="row">
      <NoDataMessage
        v-if="Object.keys(this.events).length === 0"
        :message="$tr('currentRegistrations.empty')"
      />
      <template v-else>
        <template v-for="(entry, key) in events" :key="key">
          <div class="col-12 q-px-sm">
            <h5 class="q-mt-lg q-mb-xs">{{ $tr(entry.event.name) }}</h5>
          </div>
          <checkout-person-card
            v-for="(person, index) in entry.registrations"
            :key="JSON.stringify(person)"
            :person="person"
            :registration="person"
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
import { Event } from 'src/types/event';
import NoDataMessage from 'components/NoDataMessage.vue';
import { Role } from 'src/types/role';

export const DBkey = 'current-registrations';

interface PersonRegistrations {
  id: number;
  person: {
    id: number;
    name: string;
    surname: string;
    birthdate: string;
    // eslint-disable-next-line camelcase
    id_number: string | null;
    street: string;
    city: string;
    zip: string;
    school: string | null;
    note: string | null;
    // eslint-disable-next-line camelcase
    created_at: string;
    // eslint-disable-next-line camelcase
    updated_at: string;
  },
  note: string | null;
  event: string;
  // eslint-disable-next-line camelcase
  event_id: number;
  role: Role,
  accommodation: number;
  confirmed: number;
  team: string | null;
  // eslint-disable-next-line camelcase
  registered_by: number;
  invoice: string | null;
  // eslint-disable-next-line camelcase
  created_at: string;
  // eslint-disable-next-line camelcase
  updated_at: string;
}

interface EventPersonRegistrations {
  event: Event;
  registrations: PersonRegistrations[];
}

interface CurrentRegistrationsData extends TranslationPrefixData {
  translationPrefix: string;
  events: EventPersonRegistrations[];
}

export default defineComponent({
  name: 'CurrentRegistrations',
  components: {
    NoDataMessage,
    CheckoutPersonCard,
  },
  data(): CurrentRegistrationsData {
    return {
      translationPrefix: 'user.',
      events: [],
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
        this.events = <EventPersonRegistrations[]><unknown>cached;
        return;
      }

      this.$bus.$emit('fullLoader', true);
      await Promise.all(
        events.map(async (event: Event) => {
          await this.$api({
            url: `event/${event.id}/user/${this.$auth.user()!.id}/registration`,
            method: 'get',
          })
            .then(({ data }: AxiosResponse<PersonRegistrations[]>) => {
              if (!data.length) {
                return;
              }

              this.events.push({
                event,
                registrations: data,
              });
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

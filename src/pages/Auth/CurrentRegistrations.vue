<template>
  <q-page padding>
    <h1 class="text-center text-h4">{{ $tr('currentRegistrations.title') }}</h1>

    <template v-for="(entry, key) in people" :key="key">
      <div class="row">
        <div class="col-12 q-px-sm">
          <h5 class="q-mt-lg q-mb-xs">{{ entry.name }}</h5>
        </div>
        <checkout-person-card
            v-for="(person, index) in entry.registrations"
            v-bind:key="JSON.stringify(person)"
            :person="person"
            :registration="person"
            :person-index="index"
            menu="false"
        />
      </div>
    </template>

  </q-page>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { DBValue } from 'boot/custom';
import { AxiosResponse } from 'axios';
import CheckoutPersonCard from 'components/Event/CheckoutPersonCard.vue';
import { TranslationPrefixData } from 'boot/i18n';
import internal from 'stream';

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
  role: {
    id: number;
    name: {
      id: number;
      cs: string;
      en: string;
      // eslint-disable-next-line camelcase
      created_at: string;
      // eslint-disable-next-line camelcase
      updated_at: string;
    },
    icon: string | null;
    // eslint-disable-next-line camelcase
    created_at: string;
    // eslint-disable-next-line camelcase
    updated_at: string;
  },
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
  name: string;
  registrations: PersonRegistrations[];
}

interface CurrentRegistrationsData extends TranslationPrefixData {
  translationPrefix: string;
  people: EventPersonRegistrations[];
}

export default defineComponent({
  name: 'CurrentRegistrations',
  components: { CheckoutPersonCard },
  data(): CurrentRegistrationsData {
    return {
      translationPrefix: 'auth.',
      people: [],
    };
  },
  created() {
    const DBkey = 'current-registrations';
    const cached: DBValue = this.$db(DBkey);
    if (cached) {
      this.people = <EventPersonRegistrations[]><unknown>cached;
      return;
    }

    this.$bus.$emit('fullLoader', true);

    setTimeout(() => {
      const events = this.$db('eventsList');

      if (!events) {
        return;
      }

      const eventArray = Object.values(<Record<number, any>>events);
      eventArray.forEach((event) => {
        this.$api({
          url: `event/${event.id}/user/${this.$auth.user()!.id}/registration`,
          method: 'get',
        })
          .then(({ data }: AxiosResponse<PersonRegistrations[]>) => {
            // eslint-disable-next-line max-len
            // eslint-disable-next-line @typescript-eslint/no-unsafe-assignment,@typescript-eslint/no-unsafe-member-access
            this.people.push({ name: event.name.cs, registrations: data });
            this.$db(DBkey, <DBValue><unknown>data, true);
          })
          .finally(() => {
            this.$bus.$emit('fullLoader', false);
          });
      });
    }, 1000);
  },
});
</script>

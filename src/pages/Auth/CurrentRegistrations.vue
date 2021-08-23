<template>
  <q-page padding>
    <h1 class="text-center text-h4">{{ $tr('currentRegistrations.title') }}</h1>

    <div class="row">

    </div>

  </q-page>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { DBValue } from 'boot/custom';
import { AxiosResponse } from 'axios';

interface CurrentRegistrationsData {
  translationPrefix: string;
  // people:
}

interface EventPersonRegistrations {
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

export default defineComponent({
  name: 'CurrentRegistrations',
  data(): CurrentRegistrationsData {
    return {
      translationPrefix: 'auth.',
      // people: [],
    };
  },
  created() {
    const DBkey = 'current-registrations';
    const cached: DBValue = this.$db(DBkey);
    if (cached) {
      // this.people = cached.people;
      return;
    }

    this.$bus.$emit('fullLoader', true);

    setTimeout(() => {
      const events = this.$db('eventsList');

      if (!events) {
        return;
      }

      const eventIds = Object.keys(<Record<number, never>>events);
      eventIds.forEach((eventId) => {
        console.log(eventId);
        this.$api({
          url: `event/${eventId}/user/${this.$auth.user()!.id}/registration`,
          method: 'get',
        })
          .then(({ data }: AxiosResponse<EventPersonRegistrations>) => {
            // TODO - save loaded data
            // this.people = data;
            console.log(data);
            this.$db(DBkey, <DBValue><unknown>data, true);
          })
          .finally(() => {
            console.log('cs');

            this.$bus.$emit('fullLoader', false);
          });
      });
    }, 1000);
  },
});
</script>

<template>
  <q-page padding>
    <h1 class="text-center text-h4">{{ $tr('currentRegistrations.title') }}</h1>

    <div class="row">

    </div>

  </q-page>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { assertDBValue, DBValue } from 'boot/custom';

interface CurrentRegistrationsData {
  translationPrefix: string;
  eventId: number;
}

interface EventPersonRegistrations {
  id: number;
  person: {
    id: number;
    name: string;
    surname: string;
    birthdate: string;
    // eslint-disable-next-line camelcase
    id_number: string|null;
    street: string;
    city: string;
    zip: string;
    school: string|null;
    note: string|null;
    // eslint-disable-next-line camelcase
    created_at: string;
    // eslint-disable-next-line camelcase
    updated_at: string;
  },
  note: string|null;
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
    icon: string|null;
    // eslint-disable-next-line camelcase
    created_at: string;
    // eslint-disable-next-line camelcase
    updated_at: string;
  },
  accommodation: number;
  confirmed: number;
  team: string|null;
  // eslint-disable-next-line camelcase
  registered_by: number;
  invoice: string|null;
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
      people: [],
    };
  },
  created() {
    const DBkey = 'current-registrations';
    const cached: DBValue = this.$db(DBkey);
    if (cached) {
      this.people = cached.people;
      return;
    }

    this.$bus.$emit('fullLoader', true);

    const events = this.$db('eventsList');
    const eventIds = Object.keys(events);
    eventIds.forEach(function (eventId) {
      console.log(eventId);
      this.$api({
        url: `event/${eventId}/user/${this.$auth.user()!.id}/registration`,
        method: 'get',
      })
        .then(({ data }) => {
          // TODO - save loaded data
          // this.people = data;
          console.log(data);

          // assertDBValue(data);
          this.$db(DBkey, data, true);
        })
        .finally(() => {
          console.log('cs');

          this.$bus.$emit('fullLoader', false);
        });
    });

  },
});
</script>

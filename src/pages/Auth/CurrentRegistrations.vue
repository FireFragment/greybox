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

export default defineComponent({
  name: 'CurrentRegistrations',
  data(): CurrentRegistrationsData {
    return {
      translationPrefix: 'auth.',
      eventId: 41,
    };
  },
  created() {
    const DBkey = 'current-registrations';
    const cached: DBValue = this.$db(DBkey);
    if (cached) {
      // TODO - save cached data
      // this.data = cached;
      return;
    }

    this.$bus.$emit('fullLoader', true);
    this.$api({
      url: `event/${this.eventId}/user/${this.$auth.user()!.id}/registration`,
      method: 'get',
    })
      .then(({ data }) => {
        // TODO - save loaded data
        // this.data = data;
        console.log(data);
        // assertDBValue(data);
        this.$db(DBkey, data, true);
      })
      .finally(() => {
        console.log('cs');
        this.$bus.$emit('fullLoader', false);
      });
  },
});
</script>

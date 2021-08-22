<template>
  <q-page padding>
    <h1 class="text-center text-h4">{{ $tr('currentRegistrations.title') }}</h1>

    <div class="row">
      <person-card
          v-for="(person, index) in formData"
          v-bind:key="JSON.stringify(person)"
          :person="person"
          :person-index="index"
          :possible-diets="possibleDiets"
          @remove="removePerson"
      />
    </div>

  </q-page>
</template>

<script lang="ts">
import personCard from './CheckoutPersonCard';

interface CurrentRegistrationsData {
  translationPrefix: string;
  eventId: number;
};

export default {
  name: 'CurrentRegistrations',
  data(): CurrentRegistrationsData {
    return {
      translationPrefix: 'auth.',
      eventId: 41
    };
  },
  created() {
    this.$api({
      url: `event/${this.eventId}/user/${this.$auth.user().id}/registration`,
      sendToken: false,
      method: 'get',
    })
        .then((d) => {
          this.$db('eventsList', this.$makeIdObject(this.events));
        })
        .finally(() => {
          //console.log("cs");
          this.$bus.$emit('fullLoader', false);
        });
  }
  components: {
    personCard,
  },
};
</script>

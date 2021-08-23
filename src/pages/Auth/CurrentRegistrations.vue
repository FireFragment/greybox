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
import { defineComponent } from 'vue';
import PersonCard from '../../components/Event/CheckoutPersonCard.vue';

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
    this.$bus.$emit('fullLoader', true);
    this.$api({
      url: `event/${this.eventId}/user/${this.$auth.user()!.id}/registration`,
      method: 'get',
    })
      .then((d) => {
        // console.log('cs1');
        // console.log(d);
      })
      .finally(() => {
        // console.log(this.eventId);
        // console.log(this.$auth.user()!.id);
        // console.log('cs');
        this.$bus.$emit('fullLoader', false);
      });
  },
  components: {
    PersonCard: <never>PersonCard,
  },
});
</script>

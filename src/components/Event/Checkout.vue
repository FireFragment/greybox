<template>
  <div>
    <div class="row">
      <person-card
        v-for="person in formData"
        v-bind:key="JSON.stringify(person)"
        :person="person"
      />
    </div>

    <div class="q-pt-md">
      <q-btn
        label="Přidat dalšího člověka"
        color="blue-9"
        @click="$emit('goToRolePick')"
      />
      <q-btn
        :loading="loading"
        class="float-right"
        size="lg"
        color="primary"
        @click="sendForm"
      >
        Potvrdit přihlášku
        <template v-slot:loading>
          <q-spinner-hourglass class="on-left" />
          Odesílám
        </template>
      </q-btn>
    </div>
  </div>
</template>

<script>
import personCard from "./CheckoutPersonCard";

export default {
  name: "Checkout",
  props: {
    formData: Array
  },
  data() {
    return {
      loading: false
    };
  },
  methods: {
    sendForm() {
      this.loading = !this.loading;
    }
  },
  components: {
    personCard
  }
};
</script>

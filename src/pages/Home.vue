<template>
  <q-page class="flex flex-center">
    <img alt="Logo ADK" src="../assets/logo_napis.png" @click="apiTest" />
  </q-page>
</template>

<style></style>

<script>
export default {
  name: "Homepage",
  methods: {
    apiTest() {
      this.$auth
        .login({
          username: "testo@va.ci",
          password: "Testovaci69"
        })
        .then(data => {
          console.log(data);
          console.log(this.$auth.token());
        })
        .catch(data => {
          console.log(data);
        });
    }
  }
};
</script>

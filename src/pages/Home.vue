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
      this.api({
        url: "login",
        data: {
          username: "testo@va.ci",
          password: "Testovaci69"
        },
        alerts: {
          error: true
        }
      }).then(data => {
        // TODO - login with data variable
        console.log(data);
      });
    }
  }
};
</script>

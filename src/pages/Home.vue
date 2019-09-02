<template>
  <q-page class="flex flex-center content-center">
    <div class="row full-width text-center">
      <img class="q-mx-auto" alt="Logo ADK" src="../assets/logo_napis.png" />
    </div>
    <div class="row q-mt-xl" v-if="!$auth.check()">
      <q-btn
        class="q-mx-md hidden-link"
        :to="$path('login')"
        icon="fas fa-sign-in-alt"
        label="Přihlásit se"
        color="primary"
        size="lg"
      />
      <q-btn
        class="q-mx-md hidden-link"
        :to="$path('signUp')"
        icon="fas fa-user-plus"
        label="Registrace"
        color="blue-9"
        size="lg"
      />
    </div>
  </q-page>
</template>

<style></style>

<script>
export default {
  name: "Homepage"
};
</script>

<template>
  <q-page class="flex flex-center content-center">
    <div class="row full-width text-center">
      <img class="q-mx-auto" alt="Logo ADK" src="../assets/logo_napis.png" />
    </div>
    <div class="row q-mt-xl flex-center" v-if="!$auth.isLoggedIn()">
      <q-btn
        class="q-mx-md q-mb-md hidden-link"
        :to="$path('auth.login')"
        icon="fas fa-sign-in-alt"
        :label="$tr('auth.login.link')"
        color="primary"
        size="lg"
      />
      <q-btn
        class="q-mx-md q-mb-md hidden-link"
        :to="$path('auth.signUp')"
        icon="fas fa-user-plus"
        :label="$tr('auth.signUp.link')"
        color="blue-9"
        size="lg"
      />
    </div>
  </q-page>
</template>

<style></style>

<script>
export default {
  name: 'Homepage',
};
</script>

<template>
  <q-page class="flex flex-center">
    <div class="row full-width text-center">
      <img class="q-mx-auto" alt="Logo ADK" src="../assets/logo_napis.png" />
    </div>
    <div class="row flex-top">
      <q-btn class="q-mx-md sign-in-button" push>
        <div class="row items-center no-wrap">
          <q-icon left name="fas fa-sign-in-alt" />
          <div class="text-center">
            Přihlásit se
          </div>
        </div>
      </q-btn>
      <q-btn class="q-mx-md register-button" push>
        <div class="row items-center no-wrap">
          <q-icon left name="fas fa-user-plus" />
          <div class="text-center">
            Registrace
          </div>
        </div>
      </q-btn>
    </div>
  </q-page>
</template>

<style></style>

<script>
export default {
  name: "Homepage"
};
</script>

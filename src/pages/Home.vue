<template>
  <q-page class="flex flex-center content-center">
    <div class="row full-width text-center">
      <!-- Template strings cannot be used in [src] attributes -->
      <img class="q-mx-auto" alt="Logo ADK" src="../assets/pride_logo_text.png"
           v-if="$isPride && !$isPDS" />
      <img class="q-mx-auto" alt="Logo ADK" src="../assets/logo_text.png" v-else-if="!$isPDS" />
      <img class="q-mx-auto" alt="PDS Logo" src="../assets/logo_pds_text.svg" v-else />
    </div>
    <div class="row q-mt-xl flex-center" v-if="!$auth.isLoggedIn()">
      <q-card class="home-infocard">
        <p>
          {{ $tr('general.loginNeeded') }}
          <br>
          {{ $tr('general.noAccountYet') }}
        </p>
        <div class="text-center">
          <q-btn
            class="q-ma-md hidden-link"
            :to="$path('auth.login')"
            icon="fas fa-sign-in-alt"
            :label="$tr('auth.login.link')"
            color="primary"
            size="lg"
          />
          <q-btn
            class="q-ma-md hidden-link"
            :to="$path('auth.signUp')"
            icon="fas fa-user-plus"
            :label="$tr('auth.signUp.link')"
            color="blue-9"
            size="lg"
          />
        </div>
      </q-card>
    </div>
  </q-page>
</template>

<style></style>

<script lang="ts">
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'Homepage',
});
</script>

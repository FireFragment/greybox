<template>
  <q-page padding>
    <div class="row q-col-gutter-md">
      <q-form
        @submit="onSubmit"
        @reset="onReset"
        class="col-12 col-sm-6 q-mt-lg offset-sm-3"
      >
        <q-input
          outlined
          type="text"
          v-model="login"
          label="Přihlašovací jméno"
          lazy-rules
          :rules="[
            val => (val !== null && val !== '') || 'Vyplňte prosím jméno'
          ]"
        >
          <template v-slot:prepend>
            <q-icon name="fas fa-user" />
          </template>
        </q-input>

        <q-input
          v-model="password"
          outlined
          :type="isPwd ? 'password' : 'text'"
          label="Heslo"
          lazy-rules
          :rules="[
            val => (val !== null && val !== '') || 'Vyplňte prosím jméno'
          ]"
        >
          <template v-slot:prepend>
            <q-icon name="fas fa-key" />
          </template>
          <template v-slot:append>
            <q-icon
              :name="isPwd ? 'visibility_off' : 'visibility'"
              class="cursor-pointer"
              @click="isPwd = !isPwd"
            />
          </template>
        </q-input>
        <div class="q-mt-lg q-mb-lg text-center">
          Zapomněli jste heslo? <a href="#">Obnovte si jej.</a>
        </div>

        <div class="text-center">
          <q-btn label="Odeslat" type="submit" color="primary" />
        </div>
      </q-form>
    </div>
  </q-page>
</template>

<script>
export default {
  name: "PageSignIn",
  data() {
    return {
      login: null,
      password: null,
      isPwd: true
    };
  }
};
</script>

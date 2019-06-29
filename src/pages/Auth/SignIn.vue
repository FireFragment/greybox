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
          type="email"
          v-model="email"
          label="Přihlašovací e-mail"
          lazy-rules
          :rules="[
            val => (val !== null && val !== '') || 'Vyplňte prosím e-mail'
          ]"
        >
          <template v-slot:prepend>
            <q-icon name="fas fa-at" />
          </template>
        </q-input>

        <q-input
          v-model="password"
          outlined
          :type="isPwd ? 'password' : 'text'"
          label="Heslo"
          class="q-mt-sm"
          lazy-rules
          :rules="[
            val => (val !== null && val !== '') || 'Vyplňte prosím heslo'
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
          <q-btn label="Přihlásit" type="submit" color="primary" />
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
      email: null,
      password: null,
      isPwd: true
    };
  }
};
</script>

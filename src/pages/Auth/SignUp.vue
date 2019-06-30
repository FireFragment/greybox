<template>
  <q-page padding>
    <h1 class="text-center text-h4">Registrace uživatele</h1>
    <div class="row q-col-gutter-md">
      <q-form @submit="signUp" class="col-12 col-sm-6 q-mt-lg offset-sm-3">
        <q-input
          outlined
          type="email"
          v-model="email"
          label="E-mail"
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

        <q-input
          v-model="passwordConfirm"
          outlined
          :type="isPwd2 ? 'password' : 'text'"
          label="Zopakujte heslo"
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
              :name="isPwd2 ? 'visibility_off' : 'visibility'"
              class="cursor-pointer"
              @click="isPwd2 = !isPwd2"
            />
          </template>
        </q-input>

        <div class="text-center">
          <q-btn label="Registrovat" type="submit" color="primary" />
        </div>
      </q-form>
    </div>
  </q-page>
</template>

<script>
export default {
  name: "PageSignUp",
  data() {
    return {
      email: null,
      password: null,
      passwordConfirmation: null,
      isPwd: true,
      isPwd2: true
    };
  },
  methods: {
    signUp() {
      console.log(this.email, this.password, this.passwordConfirmation);
    }
  }
};
</script>

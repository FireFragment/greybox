<template>
  <q-page padding>
    <h1 class="text-center text-h4">{{ $tr("auth.passwordReset") }}</h1>
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
          :label="$tr('auth.yourEmail')"
          lazy-rules
          :rules="[
            val => (val !== null && val !== '') || $tr('auth.emailError')
          ]"
        >
          <template v-slot:prepend>
            <q-icon name="fas fa-at" />
          </template>
        </q-input>

        <div class="text-center q-mt-sm">
          <q-btn :label="$tr('auth.sendLink')" type="submit" color="primary" />
        </div>
      </q-form>
    </div>
  </q-page>
</template>

<script>
export default {
  name: "PasswordReset",
  data() {
    return {
      email: null
    };
  },
  created() {
    if (this.$auth.check()) this.$router.replace({ name: "home" });
  }
};
</script>

<style scoped></style>

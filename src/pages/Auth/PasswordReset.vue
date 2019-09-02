<template>
  <q-page padding>
    <h1 class="text-center text-h4">{{ $tr("auth.passwordReset") }}</h1>
    <div class="row q-col-gutter-md">
      <q-form @submit="submit" class="col-12 col-sm-6 q-mt-lg offset-sm-3">
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
          <q-btn type="submit" color="primary" :loading="loading">
            {{ $tr("auth.sendLink") }}
            <template v-slot:loading>
              <q-spinner-hourglass class="on-left" />
              Odesílám
            </template>
          </q-btn>
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
      email: null,
      loading: false
    };
  },
  created() {
    if (this.$auth.check()) this.$router.replace({ name: "home" });
  },
  methods: {
    submit() {
      this.loading = true;
      this.$api({
        url: "reset",
        sendToken: false,
        data: {
          username: this.email
        },
        alerts: false,
        method: "post"
      })
        .then(data => {
          this.$flash(data.data.message, "success");
          this.$router.replace({ name: "home" });
        })
        .catch(data => {
          this.$flash(
            data.response.data.message
              ? data.response.data.message
              : "An error had occured, please try again.",
            "error"
          );
        })
        .finally(() => {
          this.loading = false;
        });
    }
  }
};
</script>

<style scoped></style>

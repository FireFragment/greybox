<template>
  <q-page padding>
    <h1 class="text-center text-h4">
      {{ $tr("passwordReset.title") }}
    </h1>
    <div class="row q-col-gutter-md">
      <q-form @submit="submit" class="col-12 col-sm-6 q-mt-lg offset-sm-3">
        <q-input
          v-model="password"
          outlined
          :type="isPwd ? 'password' : 'text'"
          :label="$tr('fields.password')"
          class="q-mt-sm"
          lazy-rules
          :rules="[
            val => (val !== null && val !== '') || $tr(`errors.password`)
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
          v-model="passwordConfirmation"
          outlined
          :type="isPwd2 ? 'password' : 'text'"
          :label="$tr('fields.passwordConfirm')"
          class="q-mt-sm"
          lazy-rules
          :rules="[
            val => (val !== null && val !== '') || $tr(`errors.passwordConfirm`)
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

        <div class="text-center q-mt-sm">
          <q-btn type="submit" color="primary" :loading="loading">
            {{ $tr("passwordReset.submit") }}
            <template v-slot:loading>
              <q-spinner-hourglass class="on-left" />
              {{ $tr("passwordReset.loading") }}
            </template>
          </q-btn>
        </div>
      </q-form>
    </div>
  </q-page>
</template>

<script>
/* eslint-disable */
export default {
  name: 'PageNewPassword',
  data() {
    return {
      translationPrefix: 'auth.',
      password: null,
      passwordConfirmation: null,
      isPwd: true,
      isPwd2: true,
      loading: false,
      token: null,
    };
  },
  methods: {
    submit() {
      this.loading = true;

      this.$api({
        url: 'reset',
        sendToken: false,
        data: {
          password: this.password,
          password_confirmation: this.passwordConfirmation,
          token: this.token,
        },
        alerts: false,
        method: 'put',
      })
        .then(() => {
          this.$flash(this.$tr('passwordReset.successReset'), 'success');
          this.$router.replace(this.$path('auth.login'));
        })
        .catch((data) => {
          if (data.response.data) {
            const response = data.response.data;

            // Just one error
            if (response.message) {
              this.$flash(
                this.$tr(`passwordReset.validation.${response.message}`),
                'error',
                false,
                9000,
              );

              // Invalid token -> redirect to generate a new one
              if (response.message === 'tokenNotFound') this.$router.replace(this.$path('auth.passwordReset'));
            } // More errors - same as in sign up
            else {
              for (const index in data.response.data) {
                data.response.data[index].forEach((message) => {
                  this.$flash(
                    this.$tr(
                      `signUp.validation.${
                        index
                      }.${
                        message.substr(11).replace('.', '-')}`,
                    ),
                    'error',
                    false,
                    9000,
                  );
                });
              }
            }
          } else this.$flash(this.$tr('general.error', null, false), 'error');
        })
        .finally(() => {
          this.loading = false;
        });
    },
  },
  created() {
    this.token = this.$route.params.token;

    if (this.$auth.check()) this.$router.replace({ name: 'home' });
  },
};
</script>

<style scoped></style>

<template>
  <q-page padding>
    <h1 class="text-center text-h4">{{ $tr("login.title") }}</h1>
    <div class="row q-col-gutter-md">
      <q-form @submit="login" class="col-12 col-sm-6 q-mt-lg offset-sm-3">
        <q-card class="q-pa-md q-ma-sm">
          <q-input
              outlined
              type="email"
              v-model="email"
              :label="$tr('fields.email')"
              lazy-rules
              :rules="[$validators.nonEmpty, $validators.email]"
          >
            <template v-slot:prepend>
              <q-icon name="fas fa-at" />
            </template>
          </q-input>

          <q-input
              v-model="password"
              outlined
              :type="isPwd ? 'password' : 'text'"
              :label="$tr('fields.password')"
              class="q-mt-sm"
              lazy-rules
              :rules="[
            val => (val !== null && val !== '') || $tr('errors.password')
          ]"
          >
            <template v-slot:prepend>
              <q-icon name="fas fa-key" />
            </template>
            <template v-slot:append>
              <q-icon
                  :name="isPwd ? 'fas fa eye-slash' : 'fas fa-eye'"
                  class="cursor-pointer"
                  @click="isPwd = !isPwd"
              />
            </template>
          </q-input>
          <div class="q-mt-sm q-mb-sm text-center">
            {{ $tr("passwordReset.loginQuestion") }}
            <router-link :to="$path('auth.passwordReset')">{{
                $tr("passwordReset.loginLink")
              }}</router-link>
          </div>
          <div class="q-mt-sm q-mb-lg text-center">
            {{ $tr("signUp.noAccountYet") }}
            <router-link :to="$path('auth.signUp')">{{
                $tr("signUp.cta")
              }}</router-link>
          </div>

          <div class="text-center">
            <q-btn type="submit" color="primary" :loading="loading">
              {{ $tr("login.submit") }}
              <template v-slot:loading>
                <q-spinner-hourglass class="on-left" />
                {{ $tr("login.loading") }}
              </template>
            </q-btn>
          </div>
        </q-card>

      </q-form>
    </div>
  </q-page>
</template>

<script>
/* eslint-disable */
import { switchLocale } from '../../boot/i18n';

export default {
  name: 'PageSignIn',
  data() {
    return {
      translationPrefix: 'auth.',
      email: null,
      password: null,
      isPwd: true,
      loading: false,
    };
  },
  methods: {
    login(userData = null) {
      if (this.loading) return;

      this.loading = true;
      let requestData;
      if (typeof userData === 'string') {
        requestData = JSON.parse(userData);
      } else {
        requestData = {
          username: this.email,
          password: this.password,
          isSignUp: false,
        };
      }

      // These translations don't work inside auth promises for some odd reason
      const invalidCredentials = this.$tr('validation.invalidCredentials', null, false);
      const serverError = this.$tr('validation.serverError', null, false);
      const signupSuccess = this.$tr('signUp.success');
      const loginSuccess = this.$tr('login.success');

      this.$auth
        .login(requestData)
        .then(() => {
          // User was automatically logged in after sign up
          if (
            typeof requestData.isSignUp === 'boolean'
            && requestData.isSignUp
          ) {
            this.$bus.$emit('fullLoader', false);
            this.$router.replace(this.$path('home'));
            this.$flash(signupSuccess, 'done');
          } else {
            this.$router.push(this.$path('home'));
            this.$flash(loginSuccess, 'done');
          }

          setTimeout(() => {
            if(typeof this.$i18n === 'undefined' || this.$auth.user().preferred_locale !== this.$i18n.locale) {
              switchLocale(this.$auth.user().preferred_locale);
            }
          }, 500);

        })
        .catch((e) => {
          if (e.response.status == 500) this.$flash(serverError, 'error');
          else this.$flash(invalidCredentials, 'error');
          this.password = null;
        })
        .finally(() => {
          this.loading = false;
        });
    },
  },
  created() {
    const loginData = localStorage.getItem('loginData');

    // Auto login user with passed data (from registration page)
    if (loginData) {
      localStorage.removeItem('loginData');
      this.login(loginData);
    }
  },
};
</script>

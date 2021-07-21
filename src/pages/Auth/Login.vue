<template>
  <q-page padding>
    <h1 class="text-center text-h4">{{ $tr("login.title") }}</h1>
    <div class="row q-col-gutter-md">
      <q-form @submit="login" class="col-12 col-sm-6 q-mt-lg offset-sm-3">
        <q-input
          outlined
          type="email"
          v-model="email"
          :label="$tr('fields.email')"
          lazy-rules
          :rules="[val => (val !== null && val !== '') || $tr(`errors.email`)]"
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
              :name="isPwd ? 'visibility_off' : 'visibility'"
              class="cursor-pointer"
              @click="isPwd = !isPwd"
            />
          </template>
        </q-input>
        <div class="q-mt-sm q-mb-lg text-center">
          {{ $tr("passwordReset.loginQuestion") }}
          <router-link :to="$path('auth.passwordReset')">{{
            $tr("passwordReset.loginLink")
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
      </q-form>
    </div>
  </q-page>
</template>

<script>
/* eslint-disable */

export default {
  name: 'PageSignIn',
  props: {
    loginData: Object,
  },
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
      let requestData = userData;
      if (
        typeof requestData !== 'object'
        || typeof requestData.username === 'undefined'
      ) {
        requestData = {
          username: this.email,
          password: this.password,
          isSignUp: false,
        };
      }

      // These translations don't work inside auth promises for some odd reason
      const invalidCredentials = this.$tr('login.validation.invalidCredentials');
      const loginLink = this.$path('auth.login');
      const signupSuccess = this.$tr('signUp.success');
      const loginSuccess = this.$tr('login.success');

      this.$auth
        .login(requestData)
        .then((data) => {
          this.$bus.$emit('fullLoader', true);
          // this.$auth.options.fetchData.url = `${this.apiSettings.baseURL}user/${data.data.id}`;

          this.$auth
            .fetchUser()
            .then(() => {
              // User was automatically logged in after sign up
              if (
                typeof requestData.isSignUp === 'boolean'
                && requestData.isSignUp
              ) {
                this.$router.replace({ name: 'home' });
                this.$flash(signupSuccess, 'done');
              } else {
                this.$router.push({ name: 'home' });
                this.$flash(loginSuccess, 'done');
              }
            })
            .catch((data) => {
              this.$flash(data.response.statusText, 'error');
            })
            .finally(() => {
              this.$bus.$emit('fullLoader', false);
            });
        })
        .catch((a, b) => {
          console.log(a, b);
          // Redirect is necessary because auth plugin automatically redirects to home
          this.$router.replace(loginLink);
          this.$bus.$emit('fullLoader', false);
          this.$flash(invalidCredentials, 'error');
        })
        .finally(() => {
          this.loading = false;
        });
    },
  },
  created() {
    // User already logged -> redirect to homepage
    if (this.$auth.check()) this.$router.replace({ name: 'home' });

    // Auto login user with passed data (from registration page)
    if (this.loginData) {
      this.$bus.$emit('fullLoader', false);
      this.login(this.loginData);
    }
  },
};
</script>

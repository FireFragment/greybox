<template>
  <q-page padding>
    <h1 class="text-center text-h4">{{ $tr("auth.login") }}</h1>
    <div class="row q-col-gutter-md">
      <q-form @submit="login" class="col-12 col-sm-6 q-mt-lg offset-sm-3">
        <q-input
          outlined
          type="email"
          v-model="email"
          :label="$tr('auth.loginEmail')"
          lazy-rules
          :rules="[
            val => (val !== null && val !== '') || $tr(`auth.emailError`)
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
          :label="$tr('auth.password')"
          class="q-mt-sm"
          lazy-rules
          :rules="[
            val => (val !== null && val !== '') || $tr('auth.passwordError')
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
          {{ $tr("auth.recoverPasswordQuestion") }}
          <a href="$path('passwordReset')">{{
            $tr("auth.recoverPasswordLink")
          }}</a>
        </div>

        <div class="text-center">
          <q-btn type="submit" color="primary" :loading="loading">
            {{ $tr("auth.toLogin") }}
            <template v-slot:loading>
              <q-spinner-hourglass class="on-left" />
              Přihlašuji
            </template>
          </q-btn>
        </div>
      </q-form>
    </div>
  </q-page>
</template>

<script>
import { EventBus } from "../../event-bus";

export default {
  name: "PageSignIn",
  props: {
    loginData: Object
  },
  data() {
    return {
      email: null,
      password: null,
      isPwd: true,
      loading: false
    };
  },
  methods: {
    login(userData = null) {
      if (this.loading) return;

      this.loading = true;
      let requestData = userData;
      if (!requestData)
        requestData = {
          username: this.email,
          password: this.password
        };

      this.$auth
        .login(requestData)
        .then(data => {
          EventBus.$emit("fullLoader", true);
          this.$auth.options.fetchData.url =
            this.apiSettings.baseURL + "user/" + data.data.id;

          this.$auth
            .fetchUser()
            .then(() => {
              if (userData) {
                this.$router.replace({ name: "home" });
                this.$flash("Registrace úspěšná", "done");
              } else {
                this.$router.push({ name: "home" });
                this.$flash(this.$tr("auth.loginSuccess"), "done");
              }
            })
            .catch(data => {
              this.$flash(data.response.statusText, "error");
            })
            .finally(() => {
              EventBus.$emit("fullLoader", false);
            });
        })
        .catch(data => {
          this.$router.replace(this.$path("login"));
          EventBus.$emit("fullLoader", false);
          this.$flash(data.response.data.message, "error");
        })
        .finally(() => {
          this.loading = false;
        });
    }
  },
  created() {
    if (this.$auth.check()) this.$router.replace({ name: "home" });

    // Auto login user with passed data
    if (this.loginData) {
      EventBus.$emit("fullLoader", false);
      this.login(this.loginData);
    }
  }
};
</script>

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
          v-model="passwordConfirmation"
          outlined
          :type="isPwd2 ? 'password' : 'text'"
          :label="$tr('auth.confirmPassword')"
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

        <div class="text-center q-mt-sm">
          <q-btn :label="$tr('general.save')" type="submit" color="primary" />
        </div>
      </q-form>
    </div>
  </q-page>
</template>

<script>
import { EventBus } from "../../event-bus";

export default {
  name: "PageNewPassword",
  data() {
    return {
      password: null,
      passwordConfirmation: null,
      isPwd: true,
      isPwd2: true
    };
  },
  methods: {
    changePassword() {
      this.$api({
        url: "user",
        sendToken: false,
        data: {
          username: this.email,
          password: this.password,
          password_confirmation: this.passwordConfirmation
        },
        alerts: false
      })
        .then(() => {
          EventBus.$emit("fullLoader", true);
          this.$router.push({
            name: "login",
            params: {
              loginData: {
                username: this.email,
                password: this.password
              }
            }
          });
        })
        .catch(data => {
          if (data.response.data)
            for (let index in data.response.data)
              data.response.data[index].forEach(message => {
                this.$flash(message, "error", false, 5000);
              });
          else this.$flash("An error had occured, please try again.", "error");
        });
    }
  },
  created() {
    if (this.$auth.check()) this.$router.replace({ name: "home" });
  }
};
</script>

<style scoped></style>

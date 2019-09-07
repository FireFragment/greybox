<template>
  <q-page padding>
    <h1 class="text-center text-h4">{{ $tr("signUp.title") }}</h1>
    <div class="text-center close-paragraphs q-p-1">
      <p>
        <a href="javascript:void(0)" @click="whySignUpModal = true">{{
          $tr("signUp.modal.link")
        }}</a>

        <q-dialog v-model="whySignUpModal">
          <q-card class="dialog-medium">
            <q-card-section class="row items-center">
              <div class="text-h6">
                {{ $tr("signUp.modal.title") }}
              </div>
              <q-space />
              <q-btn icon="close" flat round dense v-close-popup />
            </q-card-section>

            <q-card-section>
              <div v-html="$tr('signUp.modal.text')"></div>
            </q-card-section>
          </q-card>
        </q-dialog>
      </p>
    </div>

    <div class="row q-col-gutter-md">
      <q-form @submit="signUp" class="col-12 col-sm-6 q-mt-lg offset-sm-3">
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

        <div class="text-center">
          <q-btn type="submit" color="primary" :loading="loading">
            {{ $tr("signUp.submit") }}
            <template v-slot:loading>
              <q-spinner-hourglass class="on-left" />
              {{ $tr("signUp.loading") }}
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
  name: "PageSignUp",
  data() {
    return {
      translationPrefix: "auth.",
      email: null,
      password: null,
      passwordConfirmation: null,
      isPwd: true,
      isPwd2: true,
      loading: false,
      whySignUpModal: false
    };
  },
  created() {
    if (this.$auth.check()) this.$router.replace({ name: "home" });
  },
  methods: {
    signUp() {
      if (this.loading) return;

      this.loading = true;
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
                this.$flash(
                  this.$tr(
                    "signUp.validation." +
                      index +
                      "." +
                      message.substr(11).replace(".", "-")
                  ),
                  "error",
                  false,
                  9000
                );
              });
          else this.$flash(this.$tr("general.error", null, false), "error");
        })
        .finally(() => {
          this.loading = false;
        });
    }
  }
};
</script>

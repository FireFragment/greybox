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
              <q-btn icon="fas fa-times" flat round dense v-close-popup />
            </q-card-section>

            <q-card-section>
              <ul>
                <li v-for="item in $tr('signUp.modal.list')" v-bind:key="item">
                  {{ item }}
                </li>
              </ul>
            </q-card-section>
          </q-card>
        </q-dialog>
      </p>
    </div>

    <div class="row q-col-gutter-md">
      <q-form @submit="signUp" class="col-12 col-sm-6 q-mt-lg offset-sm-3">
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
              val => (val !== null && val !== '') || $tr(`errors.password`)
            ]"
          >
            <template v-slot:prepend>
              <q-icon name="fas fa-key" />
            </template>
            <template v-slot:append>
              <q-icon
                :name="isPwd ? 'fas fa-eye-slash' : 'fas fa-eye'"
                class="cursor-pointer q-pr-sm"
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
                :name="isPwd2 ? 'fas fa-eye-slash' : 'fas fa-eye'"
                class="cursor-pointer q-pr-sm"
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
        </q-card>
      </q-form>
      <div class="col-12">
        <div class="row q-col-gutter-md">
          <div class="col-12 col-sm-6 tw-mx-auto">
            <div class="q-mx-sm">
              <q-banner class="bg-primary text-white">
                <template v-slot:avatar>
                  <q-icon name="fas fa-key" color="white" class="!tw-text-2xl tw-h-10"/>
                </template>
                <div class="tw-py-2">
                  {{ $tr('signUp.password.title') }}
                  <ul class="tw-pl-6">
                    <li v-for="item in $tr('signUp.password.list')" v-bind:key="item">
                      {{ item }}
                    </li>
                  </ul>
                </div>
              </q-banner>
            </div>
          </div>
        </div>
      </div>
      <SupportBanner />
    </div>
  </q-page>
</template>

<script>
/* eslint-disable */
import { $tr, $flash } from '../../boot/custom';
import SupportBanner from 'components/SupportBanner.vue';

export const outputValidationErrors = (data) => {
  if (!data) {
    $flash($tr('general.error', null, false), 'error');
    return;
  }

  for (const index in data) {
    let messages = data[index];
    if (typeof messages === 'string') {
      messages = [messages];
    }

    // Invalid response, not an array of strings
    if (!Array.isArray(messages) || !messages.every(i => typeof i === "string")) {
      continue;
    }

    messages.forEach((message) => {
      // Convert message path to actual error message
      const messageId = message.includes('validation.')
        ? message.substring('validation.'.length).replace('.', '-')
        : message;

      const translated = $tr(`validation.${index}.${messageId}`, null, false);

      $flash(
        typeof translated === 'string' ? translated : $tr('general.error', null, false),
        'error',
        false,
        9000,
      );
    });
  }
};

export default {
  name: 'PageSignUp',
  components: { SupportBanner },
  data() {
    return {
      translationPrefix: 'auth.',
      email: null,
      password: null,
      passwordConfirmation: null,
      isPwd: true,
      isPwd2: true,
      loading: false,
      whySignUpModal: false,
    };
  },
  methods: {
    signUp() {
      if (this.loading) return;

      this.loading = true;
      this.$api({
        url: 'user',
        sendToken: false,
        data: {
          username: this.email,
          password: this.password,
          password_confirmation: this.passwordConfirmation,
          preferred_locale: this.$i18n.locale,
        },
        alerts: false,
      })
        .then(() => {
          localStorage.setItem('loginData', JSON.stringify({
            username: this.email,
            password: this.password,
            isSignUp: true,
          }));
          this.$bus.$emit('fullLoader', true);
          this.$router.push(this.$path('auth.login'));
        })
        .catch((data) => outputValidationErrors(data.response.data))
        .finally(() => {
          this.loading = false;
        });
    },
  },
};
</script>

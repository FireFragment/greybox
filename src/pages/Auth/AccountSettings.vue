<template>
  <q-page padding>
    <h1 class="text-center text-h4">{{ $tr('accountSettings.title') }}</h1>

    <div class="row q-col-gutter-md">
      <q-form @submit="submit" class="col-12 col-sm-6 q-mt-lg offset-sm-3">
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
          v-model="oldPassword"
          outlined
          :type="isPwd ? 'password' : 'text'"
          :label="$tr('accountSettings.oldPswd')"
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
          v-model="newPassword"
          outlined
          :type="isPwd2 ? 'password' : 'text'"
          :label="$tr('accountSettings.newPswd')"
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
              :name="isPwd2 ? 'fas fa-eye-slash' : 'fas fa-eye'"
              class="cursor-pointer q-pr-sm"
              @click="isPwd2 = !isPwd2"
            />
          </template>
        </q-input>

        <q-input
          v-model="passwordConfirmation"
          outlined
          :type="isPwd3 ? 'password' : 'text'"
          :label="$tr('accountSettings.newPswd')"
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
              :name="isPwd3 ? 'fas fa-eye-slash' : 'fas fa-eye'"
              class="cursor-pointer q-pr-sm"
              @click="isPwd3 = !isPwd3"
            />
          </template>
        </q-input>

        <div class="text-center q-mt-sm">
          <q-btn type="submit" color="primary" :loading="loading">
            {{ $tr('accountSettings.submit') }}
            <template v-slot:loading>
              <q-spinner-hourglass class="on-left" />
              {{ $tr('accountSettings.loading') }}
            </template>
          </q-btn>
        </div>
      </q-form>
    </div>
  </q-page>
</template>

<script>
export default {
  name: 'AccountSettings',
  data() {
    return {
      translationPrefix: 'auth.',
      email: null,
      newPassword: null,
      oldPassword: null,
      isPwd: true,
      isPwd2: true,
      isPwd3: true,
      passwordConfirmation: null,
      loading: false,
    };
  },
  methods: {
    submit() {
      this.loading = true;
    },
  },
};
</script>

<style scoped></style>

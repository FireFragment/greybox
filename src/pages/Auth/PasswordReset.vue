<template>
  <q-page padding>
    <h1 class="text-center text-h4">{{ $tr("passwordReset.title") }}</h1>
    <div class="text-center close-paragraphs q-p-1">
      <p>
        {{ $tr("passwordReset.instructions") }}
      </p>
    </div>

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
export default {
  name: 'PasswordReset',
  data() {
    return {
      translationPrefix: 'auth.',
      email: null,
      loading: false,
    };
  },
  created() {
    if (this.$auth.check()) this.$router.replace({ name: 'home' });
  },
  methods: {
    submit() {
      this.loading = true;
      this.$api({
        url: 'reset',
        sendToken: false,
        data: {
          username: this.email,
        },
        alerts: false,
        method: 'post',
      })
        .then(() => {
          this.$flash(this.$tr('passwordReset.successEmail'), 'success');
          this.$router.replace({ name: 'home' });
        })
        .catch((data) => {
          this.email = null;
          if (data.response.data) {
            for (const index in data.response.data) {
              const msg = data.response.data[index];

              if (typeof msg === 'object') {
                msg.forEach((message) => {
                  this.$flash(this.$tr(`passwordReset.${message}`), 'error');
                });
              } else {
                this.$flash(
                  this.$tr(`passwordReset.validation.${msg}`),
                  'error',
                );
              }
            }
          } else this.$flash(this.$tr('general.error', null, false), 'error');
        })
        .finally(() => {
          this.loading = false;
        });
    },
  },
};
</script>

<style scoped></style>

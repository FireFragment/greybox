<template>
  <q-page padding>
    <p>
      Sign out (just programmatic stuff)
    </p>
  </q-page>
</template>

<script>
/* eslint-disable */
export default {
  name: 'PageSignOut',
  async mounted() {
    await this.$auth.logout();
    this.$flash(this.$tr('auth.logout.success'));

    // Remove cached personal data on logout
    this.$dbFlushPersonal();
  },
};
</script>

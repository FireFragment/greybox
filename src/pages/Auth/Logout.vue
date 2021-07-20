<template>
  <q-page padding>
    <p>
      Sign out (just programmatic stuff)
    </p>
  </q-page>
</template>

<script>
import Vue from 'vue';

export default {
  name: 'PageSignOut',
  mounted() {
    this.$auth.logout();
    this.$flash(this.$tr('auth.logout.success'));

    // Remove cached personal data on logout
    Vue.prototype.dbPersonal = {};
  },
};
</script>

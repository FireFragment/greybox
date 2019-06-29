<template>
  <q-page padding>
    <p>
      Sign out (just programmatic stuff)
    </p>
  </q-page>
</template>

<script>
export default {
  name: "PageSignOut",
  mounted() {
    localStorage.removeItem("loggedUserData");
    this.$auth.logout();
    this.$flash("Odhlášení úspěšné");
  }
};
</script>

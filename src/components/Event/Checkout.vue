<template>
  <div>Čus, já jsem košík {{ formData }}</div>
</template>

<script>
export default {
  name: "Checkout",
  props: {
    formData: Array
  },
  created() {
    console.log(this.formData);
  }
};
</script>

<style scoped></style>

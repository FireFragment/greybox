<template>
  <q-page padding>
    <p>
      This is a tournament page with ID <i>{{ $route.params.id }}</i> and slug
      <i>{{ $route.params.slug }}</i>
    </p>
  </q-page>
</template>

<script>
export default {
  name: "PageTournament"
};
</script>

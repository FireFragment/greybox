<template>
  <q-page class="flex flex-center content-center row">
    <h1 class="flex-center row col-12 text-h4">{{ $tr("title") }}</h1>

    <p class="flex-center row col-12">{{ $tr("text") }}</p>
    <div class="emoji"><i class="far fa-frown-open"></i></div>
    <div class="flex-center row col-12 q-mt-xl">
      <q-btn
        class="hidden-link"
        :to="{ name: 'home' }"
        icon="fas fa-angle-left"
        :label="$tr('button')"
        color="primary"
        size="lg"
      />
    </div>
  </q-page>
</template>

<style></style>

<script>
export default {
  name: "Homepage",
  data() {
    return {
      translationPrefix: "general.404."
    };
  }
};
</script>

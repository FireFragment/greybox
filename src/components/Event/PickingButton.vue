<template>
  <div class="btn-container col-12 col-sm-6 col-md q-mb-md text-center">
    <q-btn
      :icon="'fas fa-' + icon"
      size="xl"
      :label="$tr(label)"
      stack
      :color="color"
      :no-caps="true"
    />
  </div>
</template>

<script>
export default {
  props: {
    label: [String, Object],
    icon: String,
    color: String,
  },
  name: 'PickingButton',
};
</script>

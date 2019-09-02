<template>
  <div class="col text-center">
    <q-btn
      :icon="'fas fa-' + icon"
      size="xl"
      :label="typeof label === 'object' ? $tr(label) : label"
      stack
      :color="color"
      :no-caps="true"
      @click="$emit('click')"
    />
  </div>
</template>

<script>
export default {
  props: {
    label: [String, Object],
    icon: String,
    color: String
  },
  name: "PickingButton"
};
</script>

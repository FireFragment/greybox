<template>
  <div class="row justify-center picking-buttons items-stretch q-mt-lg">
    <picking-button
      v-for="btn in options"
      v-bind:key="btn.value"
      :label="btn.label"
      :icon="btn.icon"
      :color="btn.color"
      @click="$emit('selected', name, btn.value)"
      :auto-size="options.length > 4"
    />
  </div>
</template>

<script>
/* eslint-disable */
import pickingButton from './PickingButton';

export default {
  props: {
    values: [Array, Object],
    name: String,
    hideFirst: Boolean,
  },
  emits: ['selected'],
  components: {
    pickingButton,
  },
  computed: {
    options() {
      return Object.values(this.values).filter((item) => !this.hideFirst || item.value !== 0);
    },
  },
  name: 'PickType',
};
</script>

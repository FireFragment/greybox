<template>
  <div>
    <div class="row picking-buttons items-stretch q-mt-lg">
      <div class="col" v-if="values.length < 4"></div>
      <picking-button
        v-for="btn in options"
        v-bind:key="btn.value"
        :label="btn.label"
        :icon="btn.icon"
        :color="btn.color"
        @click="$emit('selected', name, btn.value)"
      />
      <div class="col" v-if="values.length < 4"></div>
    </div>
  </div>
</template>

<script>
import pickingButton from "./PickingButton";

export default {
  props: {
    values: [Array, Object],
    name: String,
    hideFirst: Boolean
  },
  components: {
    pickingButton
  },
  computed: {
    options() {
      return Object.values(this.values).filter(item => {
        return !this.hideFirst || item.value !== 0;
      });
    }
  },
  name: "PickType"
};
</script>

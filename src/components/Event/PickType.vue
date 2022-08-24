<template>
  <div class="row justify-center picking-buttons items-stretch q-mt-lg">
    <div class="col" v-if="values.length < 4"></div>
    <picking-button
      v-for="btn in options"
      v-bind:key="btn.value"
      :label="`event.${name}s.${btn.value}`"
      :icon="btn.icon"
      :color="btn.color"
      :auto-size="options.length > 4"
      :to="{
        /* TODO - translate route to use alias ($tr() on paths maybe) */
        name: this.nextRoute,
        params: {
          ...this.$route.params,
          [this.name]: $tr(`paths.eventParams.${name}.${btn.value}`),
        }
      }"
    />
    <div class="col" v-if="values.length < 4"></div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import pickingButton from './PickingButton.vue';

export default defineComponent({
  props: {
    values: [Array, Object],
    name: {
      type: String,
      required: true,
    },
    nextRoute: {
      type: String,
      required: true,
    },
    hideFirst: Boolean,
  },
  components: {
    pickingButton,
  },
  computed: {
    options() {
      return Object.values(this.values).filter((item) => !this.hideFirst || item.value !== 0);
    },
  },
  name: 'PickType',
});
</script>

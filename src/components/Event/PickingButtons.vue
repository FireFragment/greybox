<template>
  <div class="row justify-center picking-buttons items-stretch q-mt-lg">
    <div class="col" v-if="values.length < 4"></div>
    <picking-button
      v-for="btn in options"
      v-bind:key="btn.value"
      :label="`event.${name}s.${btn.label ?? btn.value}`"
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
import type { PropType } from 'vue';
import pickingButton from './PickingButton.vue';

export interface PickingButtonOptions {
  icon: string;
  color: string;
  value: string | number;
  label?: string;
}

export default defineComponent({
  props: {
    values: {
      type: Array as PropType<PickingButtonOptions[]>,
      required: true,
    },
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
    options(): PickingButtonOptions[] {
      return Object.values(this.values).filter((item) => !this.hideFirst || item.value !== 0);
    },
  },
  name: 'PickingButtons',
});
</script>

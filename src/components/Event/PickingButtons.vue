<template>
  <div class="row justify-center picking-buttons items-stretch q-mt-lg">
    <div class="col" v-if="values.length < 4"></div>
    <picking-button
      v-for="btn in values"
      v-bind:key="btn.value"
      :label="btn.label ? btn.label : `event.${name}s.${btn.value}`"
      :note="btn.note"
      :icon="btn.icon"
      :color="btn.color"
      :auto-size="values.length > 4"
      :to="$translatedRouteLink({
        name: nextRoute,
        params: {
          ...$route.params,
          [name]: btn.routeParam ?? $tr(`paths.eventParams.${name}.${btn.value}`),
        }
      })"
    />
    <div class="col" v-if="values.length < 4"></div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import type { PropType } from 'vue';
import { TranslatedString } from 'boot/i18n';
import pickingButton from './PickingButton.vue';

export interface PickingButtonOptions {
  icon: string;
  color?: string;
  value: string | number;
  label?: TranslatedString;
  note?: TranslatedString;
  routeParam?: string;
}

export default defineComponent({
  props: {
    values: {
      type: Array as PropType<PickingButtonOptions[]>,
      required: true,
    },
    name: {
      type: String as PropType<'type' | 'role'>,
      required: true,
    },
    nextRoute: {
      type: String,
      required: true,
    },
  },
  components: {
    pickingButton,
  },
  name: 'PickingButtons',
});
</script>

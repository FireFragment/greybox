<template>
  <q-item :clickable="!!link" :to="link">
    <q-item-section avatar>
      <q-icon :color="iconColor" :name="icon" />
    </q-item-section>

    <q-item-section>
      <q-item-label caption v-if="label">{{ label }}</q-item-label>
      <q-item-label class="text-dark">{{ value }}</q-item-label>
    </q-item-section>
  </q-item>
</template>

<script>
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'DebateCardRow',
  props: {
    label: {
      type: String,
      default: undefined,
      required: false,
    },
    value: {
      type: String,
      required: true,
    },
    icon: {
      type: String,
      required: true,
    },
    iconColor: {
      type: String,
      default: 'dark',
      required: false,
    },
    link: {
      type: String,
      default: undefined,
      required: false,
    },
  },
});
</script>

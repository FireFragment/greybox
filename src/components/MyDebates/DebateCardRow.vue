<template>
  <q-item
    :tag="externalLink ? 'a' : 'div'"
    :clickable="!!link"
    :href="externalLink ? link : null"
    :target="externalLink ? '_blank' : null"
    :rel="externalLink ? 'noopener' : null"
  >
    <q-item-section avatar class="items-center">
      <q-icon :color="iconColor" :name="icon" />
    </q-item-section>

    <q-item-section>
      <q-item-label caption v-if="label">{{ label }}</q-item-label>
      <q-item-label>{{ value }}</q-item-label>
    </q-item-section>
  </q-item>
</template>

<script>
import { defineComponent } from 'vue';

const DebateCardRowProps = {
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
    type: [String, Boolean],
    default: undefined,
    required: false,
  },
};

export default defineComponent({
  name: 'DebateCardRow',
  props: DebateCardRowProps,
  computed: {
    externalLink() {
      return typeof this.link === 'string';
    },
  },
});
</script>

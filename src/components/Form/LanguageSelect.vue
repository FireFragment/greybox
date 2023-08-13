<template>
  <div>
    <q-btn-toggle
      :modelValue="modelValue"
      @update:modelValue="newValue => $emit('update:modelValue', newValue)"
      toggle-color="primary"
      color="white"
      text-color="black"
      unelevated
      outline
      :size="small ? 'sm' : 'md'"
      :options="
        langs.map((lang) => ({
          label: lang,
          value: lang,
          class: `!tw-m-0 tw-leading-none before:tw-border-none ${ small ? 'tw-min-h-0' : '' }`,
        }))
      "
    />
  </div>
</template>

<script lang="ts">
import { defineComponent, PropType } from 'vue';
import { Lang, langs } from 'src/translation/config';

export default defineComponent({
  name: 'LanguageSelect',
  props: {
    modelValue: {
      type: String as PropType<Lang>,
      required: true,
    },
    small: {
      type: Boolean,
      required: false,
      default: false,
    },
  },
  emits: [
    'update:modelValue',
  ],
  data() {
    return {
      langs,
    };
  },
});
</script>

<template>
  <q-input
    v-if="type === 'text'"
    v-bind="$attrs"
    :modelValue="modelValue[currentLanguage]"
    @update:modelValue="newValue => $emit('update:modelValue', {
        ...modelValue,
        [currentLanguage]: newValue,
      })">
    <template v-slot:append>
      <LanguageSelect v-model="currentLanguage"/>
    </template>
  </q-input>

  <q-editor
    v-else
    v-bind="$attrs"
    min-height="5rem"
    :toolbar="[
        [{
          label: $q.lang.editor.formatting,
          icon: $q.iconSet.editor.formatting,
          list: 'no-icons',
          options: ['p', 'h2', 'h3']
        }],
        ['bold', 'italic', 'link'],
        ['hr', 'quote', 'unordered', 'ordered', 'outdent', 'indent'],
        ['fullscreen'],
        ['lang'],
      ]"
    :definitions="{
        hr: { icon: 'fas fa-minus' }
      }"
    :modelValue="modelValue[currentLanguage]"
    @update:modelValue="newValue => $emit('update:modelValue', {
        ...modelValue,
        [currentLanguage]: newValue,
      })">
    <template v-slot:lang>
      <LanguageSelect v-model="currentLanguage" :small="true"/>
    </template>
  </q-editor>
</template>

<script lang="ts">
import { defineComponent, PropType } from 'vue';
import { defaultLocale, Lang } from 'src/translation/config';
import LanguageSelect from 'components/Form/LanguageSelect.vue';

export default defineComponent({
  name: 'TranslatableInput',
  components: { LanguageSelect },
  props: {
    modelValue: {
      type: Object as PropType<Record<Lang, string>>,
      required: true,
    },
    type: {
      type: String as PropType<'text' | 'wysiwyg'>,
      required: false,
      default: 'text',
    },
  },
  emits: [
    'update:modelValue',
  ],
  data() {
    return {
      currentLanguage: defaultLocale,
    };
  },
});
</script>

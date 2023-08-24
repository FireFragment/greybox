<template>
  <q-input
    v-if="type === 'text'"
    v-bind="$attrs"
    :modelValue="modelValue[currentLanguage]"
    @update:modelValue="updateModel"
    :label="`${label}${isCurrentLocaleRequired ? ' *' : ''}`"
    lazy-rules
    :rules="[validate]"
  >
    <template v-slot:append>
      <LanguageSelect v-model="currentLanguage"/>
    </template>
  </q-input>

  <q-field
    v-else
    v-bind="$attrs"
    :modelValue="modelValue[currentLanguage]"
    @update:modelValue="updateModel"
    :label="`${label}${isCurrentLocaleRequired ? ' *' : ''}`"
    lazy-rules
    :rules="[validate]"

    ref="fieldRef"
    stack-label
    borderless
  >
    <template #control>
      <q-editor
        :modelValue="modelValue[currentLanguage]"
        @update:modelValue="updateModel"

        :style="
          $refs['fieldRef']?.hasError ? 'border-color: var(--q-negative)' : ''
        "
        min-height="30rem"
        class="tw-mt-2 -tw-mb-2 tw-w-full"
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
        :definitions="{ hr: { icon: 'fas fa-minus' } }"
      >
        <template v-slot:lang>
          <LanguageSelect v-model="currentLanguage" :small="true"/>
        </template>
      </q-editor>
    </template>
  </q-field>
</template>

<script lang="ts">
import { defineComponent, PropType } from 'vue';
import {
  defaultLocale, Lang, langs, primaryLocale,
} from 'src/translation/config';
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
    required: {
      type: String as PropType<'none' | 'primaryLanguageOnly' | 'currentLanguageOnly' | 'all'>,
      required: false,
      default: 'none',
    },
    label: {
      type: String,
      required: false,
      default: null,
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
  computed: {
    isCurrentLocaleRequired() {
      return this.required === 'all'
        || (this.required === 'currentLanguageOnly' && this.currentLanguage === defaultLocale)
        || (this.required === 'primaryLanguageOnly' && this.currentLanguage === primaryLocale);
    },
  },
  methods: {
    updateModel(newValue: string) {
      this.$emit('update:modelValue', {
        ...this.modelValue,
        [this.currentLanguage]: newValue,
      });
    },
    validate(): boolean | string {
      if (this.required === 'none') {
        return true;
      }

      if (this.required === 'currentLanguageOnly') {
        return this.modelValue[defaultLocale].length > 0 || <string> this.$tr('general.form.errors.nonEmptyLocale', { locale: defaultLocale }, false);
      }

      if (this.required === 'primaryLanguageOnly') {
        return this.modelValue[primaryLocale].length > 0 || <string> this.$tr('general.form.errors.nonEmptyLocale', { locale: primaryLocale }, false);
      }

      const langWithError = langs.find((lang) => this.modelValue[lang].length === 0);

      return !langWithError || <string> this.$tr('general.form.errors.nonEmptyLocale', { locale: langWithError }, false);
    },
  },
});
</script>

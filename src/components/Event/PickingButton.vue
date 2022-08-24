<template>
  <div :class="`
    btn-container col-12 col-sm-6 q-mb-md text-center
    ${autoSize ? 'col-md' : 'col-md-3'}
  `">
    <q-btn
      :icon="'fas fa-' + icon"
      size="xl"
      :label="$tr(label)"
      stack
      :color="color"
      :no-caps="true"
      :to="to"
    />
  </div>
</template>

<script lang="ts">
import { TranslatedString } from 'boot/i18n';
import { defineComponent } from 'vue';

export default defineComponent({
  props: {
    label: [String, Object as () => TranslatedString],
    icon: String,
    color: String,
    autoSize: Boolean,
    to: Object,
  },
  name: 'PickingButton',
});
</script>

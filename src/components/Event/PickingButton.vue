<template>
  <div :class="`
    btn-container col-12 col-sm-6 q-mb-md text-center
    ${autoSize ? 'col-md' : 'col-md-3'}
  `">
    <q-btn
      size="xl"
      stack
      :color="color"
      :no-caps="true"
      :to="to">
      <q-icon :name="'fas fa-' + icon" />
      <span class="block">
        {{ $tr(label) }}
      </span>
      <template v-if="note">
        <q-icon name="fas fa-info-circle touch-hide btn-tooltip-icon">
          <q-tooltip anchor="bottom middle" self="center middle">
            {{ note ? $tr(note) : "-" }} lorem ipsum dolor sit amet lorem ipsum,
            tato role je jen pro zkušené debatéry
          </q-tooltip>
        </q-icon>
        <p class="touch-only text-body2 q-mb-none q-mt-sm">
          {{ note ? $tr(note) : "-" }} lorem ipsum dolor sit amet lorem ipsum,
          tato role je jen pro zkušené debatéry
        </p>
      </template>
    </q-btn>
  </div>
</template>

<script lang="ts">
import { TranslatedString } from 'boot/i18n';
import { defineComponent } from 'vue';

export default defineComponent({
  props: {
    label: [String, Object as () => TranslatedString],
    note: {
      type: [Object as () => TranslatedString],
      required: false,
    },
    icon: String,
    color: String,
    autoSize: Boolean,
    to: Object,
  },
  name: 'PickingButton',
});
</script>

<template>
  <div class="text-center">
    <q-btn color="white" text-color="black" class="float-left q-mb-md go-back-button"
      @click="goBack()">
      <q-icon size="1em" name="fas fa-arrow-left " />
    </q-btn>
    <q-card
      class="inline-block event-header smaller"
    >
      <h1 class="text-center text-h4">
        {{ $tr('title') }} {{ $tr(event.name) }}
      </h1>
    </q-card>
  </div>
</template>

<script lang="ts">
import { TranslationPrefixData } from 'boot/i18n';
import { Event } from 'src/types/event';
import { defineComponent } from 'vue';

const HeaderCardProps = {
  event: {
    type: Object as () => Event,
    required: true,
  },
};

export default defineComponent({
  name: 'HeaderCard',
  props: HeaderCardProps,
  data(): TranslationPrefixData {
    return {
      translationPrefix: 'event.',
    };
  },
  methods: {
    goBack: () => window.history.back(),
  },
});
</script>

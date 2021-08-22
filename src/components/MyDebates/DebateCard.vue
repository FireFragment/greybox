<template>
  <q-card class="my-card full-height column">
    <q-card-section :class="{
      'bg-negative': victory === false,
      'bg-positive': victory === true,
      'text-white': victory !== null,
    }">
      <div class="text-h6">
        Žena potřebuje muže jako ryba velociped
        {{ victory ? '- jsou ženy více ryby, nebo ryby více ženy?' : '' }}
      </div>
    </q-card-section>

    <q-separator class="q-mb-xs" />

    <q-list class="col-grow column">
      <DebateCardRow
        icon="far fa-calendar-alt"
        label="Datum"
        value="5. 6. 2020"
      />
      <DebateCardRow
        icon="fas fa-check-circle"
        icon-color="positive"
        label="Afirmace"
        value="√1764"
      />
      <DebateCardRow
        icon="fas fa-times-circle"
        icon-color="negative"
        label="Negace"
        value="Tři vepři"
      />
      <DebateCardRow
        icon="fas fa-gavel"
        icon-color="warning"
        label="Výsledek"
        value="AFF 3:0"
        class="q-mb-xs"
      />
      <!-- TODO - hide if no ballot has been uploaded yet -->
      <template v-if="!adjudicator || !victory">
        <q-separator class="q-mt-auto" />
        <DebateCardRow
          icon="fas fa-file-download"
          icon-color="primary"
          value="Stáhnout ballot"
          link="#"
        />
      </template>
      <template v-if="adjudicator">
        <q-separator />
        <DebateCardRow
          icon="fas fa-file-upload"
          icon-color="primary"
          value="Nahrát ballot"
          link="#"
        />
      </template>
    </q-list>
  </q-card>
</template>

<script lang="ts">
import { defineComponent, DefineComponent } from 'vue';
import DebateCardRow from './DebateCardRow';

export default defineComponent({
  name: 'DebateCard',
  components: { DebateCardRow: <DefineComponent>DebateCardRow },
  props: {
    victory: [Boolean, null],
    adjudicator: Boolean,
  },
  data() {
    return {
      uploading: false,
    };
  },
});
</script>

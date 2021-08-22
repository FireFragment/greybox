<template>
  <q-card class="my-card full-height column">
    <!--
    TODO - colorful header based on debate result
    <q-card-section :class="{
      'bg-negative': victory === false,
      'bg-positive': victory === true,
      'text-white': victory !== null,
    }">
    -->
    <q-card-section>
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
      <DebateCardRow
        icon="fas fa-external-link-alt"
        icon-color="indigo"
        label="Greybox v1.0"
        value="Zobrazit detail debaty"
        link="https://debatovani.cz"
        class="q-mb-xs"
      />
      <!-- TODO - hide if no ballot has been uploaded (ensure only first separator has mt-auto) -->
      <q-separator class="q-mt-auto" />
      <template v-if="!adjudicator || !victory">
        <DebateCardRow
          icon="fas fa-file-download"
          icon-color="primary"
          value="Stáhnout ballot (Jakub Svíčka)"
          link="#"
        />
      </template>
      <template v-if="victory">
        <q-separator v-if="!adjudicator" />
        <DebateCardRow
          icon="fas fa-file-download"
          icon-color="primary"
          value="Stáhnout ballot (Ladislav Borgir)"
          link="#"
        />
      </template>
      <template v-if="adjudicator">
        <q-separator />
        <DebateCardRow
          icon="fas fa-file-upload"
          icon-color="primary"
          value="Nahrát ballot"
          :link="true"
          @click="uploading = true"
        />
      </template>
    </q-list>
    <q-inner-loading :showing="uploading">
      <q-spinner size="50px" color="primary" />
      <div class="q-pt-md">
        Ballot se nahrává...
      </div>
    </q-inner-loading>
  </q-card>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import DebateCardRow from './DebateCardRow.vue';

const DebateCardProps = {
  victory: Boolean,
  adjudicator: Boolean,
};

interface DebateCardData {
  uploading: boolean;
}

export default defineComponent({
  name: 'DebateCard',
  components: {
    DebateCardRow: <never>DebateCardRow,
  },
  props: DebateCardProps,
  data(): DebateCardData {
    return {
      uploading: false,
    };
  },
});
</script>

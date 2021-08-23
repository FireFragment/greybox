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
        {{ debate.motion }}
      </div>
    </q-card-section>

    <q-separator class="q-mb-xs" />

    <q-list class="col-grow column">
      <DebateCardRow
        icon="far fa-calendar-alt"
        label="Datum"
        :value="getDate(debate.date, 'D. M. YYYY')"
      />
      <DebateCardRow
        icon="fas fa-check-circle"
        icon-color="positive"
        label="Afirmace"
        :value="debate.affirmativeTeam"
      />
      <DebateCardRow
        icon="fas fa-times-circle"
        icon-color="negative"
        label="Negace"
        :value="debate.negativeTeam"
      />
      <DebateCardRow
        icon="fas fa-gavel"
        icon-color="warning"
        label="Výsledek"
        :value="debate.result"
        value-class="text-uppercase"
      />
      <DebateCardRow
        icon="fas fa-external-link-alt"
        icon-color="indigo"
        label="Greybox v1.0"
        value="Zobrazit detail debaty"
        :link="debate.link"
        class="q-mb-xs"
      />
      <!-- TODO - hide if no ballot has been uploaded (ensure only first separator has mt-auto) -->
      <q-separator class="q-mt-auto" />
      <template v-if="true">
        <DebateCardRow
          icon="fas fa-file-download"
          icon-color="primary"
          value="Stáhnout ballot (Jakub Svíčka)"
          link="#"
        />
      </template>
      <template v-if="true">
        <q-separator v-if="true" />
        <DebateCardRow
          icon="fas fa-file-download"
          icon-color="primary"
          value="Stáhnout ballot (Ladislav Borgir)"
          link="#"
        />
      </template>
      <template v-if="true">
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
import { date } from 'quasar';
import DebateCardRow from './DebateCardRow.vue';

export interface Debate {
  affirmativeTeam: string;
  date: string;
  link: string;
  motion: string;
  negativeTeam: string;
  result: string;
  role: string;
  score: string;
}

const DebateCardProps = {
  debate: {
    type: Object as () => Debate,
    required: true,
  },
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
  methods: {
    getDate: date.formatDate,
  },
});
</script>

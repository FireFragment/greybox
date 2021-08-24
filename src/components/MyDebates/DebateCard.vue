<template>
  <q-card class="my-card full-height column">
    <q-card-section :class="{
      'bg-negative': debate.win === false,
      'bg-positive': debate.win === true,
      'text-white': debate.win !== null,
    }">
      <div class="text-h6">
        {{ debate.motion }}
      </div>
    </q-card-section>

    <q-separator class="q-mb-xs" />

    <q-list class="col-grow column">
      <DebateCardRow
        icon="far fa-calendar-alt"
        :label="$tr('cardRows.date')"
        :value="getDate(debate.date, 'D. M. YYYY')"
      />
      <DebateCardRow
        icon="fas fa-check-circle"
        icon-color="positive"
        :label="$tr('cardRows.affirmatives')"
        :value="debate.affirmativeTeam"
      />
      <DebateCardRow
        icon="fas fa-times-circle"
        icon-color="negative"
        :label="$tr('cardRows.negatives')"
        :value="debate.negativeTeam"
      />
      <DebateCardRow
        icon="fas fa-gavel"
        icon-color="warning"
        :label="$tr('cardRows.result')"
        :value="debate.result"
        value-class="text-uppercase"
      />
      <DebateCardRow
        icon="fas fa-external-link-alt"
        icon-color="indigo"
        label="Greybox v1.0"
        :value="$tr('cardRows.showDetail')"
        :link="debate.link"
        class="q-mb-xs"
      />

      <template
        v-for="(ballot, index) in debate.ballots"
        :key="ballot.url"
      >
        <q-separator :class="{ 'q-mt-auto': index === 0 }" />
        <DebateCardRow
          icon="fas fa-file-download"
          icon-color="green-9"
          :value="`${$tr('cardButtons.downloadBallot')} (${ballot.adjudicator})`"
          :link="ballot.url"
        />
      </template>
      <template v-if="canUpload">
        <q-separator :class="{ 'q-mt-auto': !debate.ballots.length }" />
        <DebateCardRow
          icon="fas fa-file-upload"
          icon-color="primary"
          :value="$tr('cardButtons.uploadBallot')"
          link="#"
          @click="uploading = true"
        />
      </template>
    </q-list>
    <q-inner-loading :showing="uploading">
      <q-spinner size="50px" color="primary" />
      <div class="q-pt-md">
        {{ $tr('uploadBallot.uploading') }}...
      </div>
    </q-inner-loading>
  </q-card>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { date } from 'quasar';
import { TranslationPrefixData } from 'boot/i18n';
import { Debate } from 'src/types/debate';
import DebateCardRow from './DebateCardRow.vue';

const DebateCardProps = {
  debate: {
    type: Object as () => Debate,
    required: true,
  },
};

interface DebateCardData extends TranslationPrefixData {
  uploading: boolean;
  canUpload: boolean,
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
      canUpload: true,
      translationPrefix: 'myDebates.',
    };
  },
  methods: {
    getDate: date.formatDate,
  },
});
</script>

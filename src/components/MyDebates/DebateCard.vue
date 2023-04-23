<template>
  <drag-and-drop
    :overlay-text="$tr('cardButtons.uploadBallot')"
    :active="debate.canUploadBallot"
  >
    <q-card class="my-card full-height full-width column">
      <q-card-section :class="{
        'bg-lose': debate.win === false,
        'bg-win': debate.win === true,
        'text-white': debate.win !== null,
      }">
        <div class="text-h6">
          {{ debate.motion }}
          <q-badge outline color="primary"
                   :label="debate.role.length > 3 ? debate.role.substring(0, 1) : debate.role"
                   :title="debate.role.length > 3 ? debate.role : ''"
          />
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
        <template v-if="debate.canUploadBallot">
          <q-separator :class="{ 'q-mt-auto': !debate.ballots.length }" />
          <q-file
            v-model="file"
            ref="fileInput"
            style="display: none"
            :max-file-size="String(this.maxSizeMB * 1024 * 1024)"
            @rejected="validationFailed"
          />
          <DebateCardRow
            icon="fas fa-file-upload"
            icon-color="primary"
            :value="$tr('cardButtons.uploadBallot')"
            :link="true"
            @click="$refs.fileInput.pickFiles()"
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
  </drag-and-drop>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { date } from 'quasar';
import { TranslationPrefixData } from 'boot/i18n';
import { Debate } from 'src/types/debate';
import { AxiosError } from 'axios';
import DragAndDrop from 'components/DragAndDrop.vue';
import DebateCardRow from './DebateCardRow.vue';

const DebateCardProps = {
  debate: {
    type: Object as () => Debate,
    required: true,
  },
};

interface DebateCardData extends TranslationPrefixData {
  uploading: boolean;
  file: null | File;
  maxSizeMB: number;
}

export default defineComponent({
  name: 'DebateCard',
  components: {
    DragAndDrop: <never>DragAndDrop,
    DebateCardRow: <never>DebateCardRow,
  },
  props: DebateCardProps,
  emits: [
    'reload-data',
  ],
  data(): DebateCardData {
    return {
      uploading: false,
      translationPrefix: 'myDebates.',
      file: null,
      maxSizeMB: 10,
    };
  },
  methods: {
    validationFailed() {
      this.$flash(this.errorMessageTooLarge, 'error');
    },

    getDate: date.formatDate,

    uploadFile(file: File) {
      this.uploading = true;

      // Clear input
      this.file = null;

      const formData = new FormData();
      formData.append('ballot', file);
      formData.append('oldGreyboxId', this.debate?.oldGreyboxId ?? '');

      // $tr doesn't work inside promises for some weird reason
      const successMessage = this.$tr('uploadBallot.success');
      const errorMessageGeneral = this.$tr('uploadBallot.error');

      this.$api({
        url: 'ballot',
        data: formData,
        headers: {
          'Content-Type': 'multipart/form-data',
        },
        alerts: false,
      })
        .then(() => {
          this.$flash(successMessage, 'success');
          this.$emit('reload-data');
        })
        .catch(({ response }: AxiosError<{
          ballot: string[],
        }>) => {
          if (
            response
            && response.status === 422
            && response.data.ballot.length
            && response.data.ballot[0] === 'validation.max.file'
          ) {
            // File too large
            this.$flash(this.errorMessageTooLarge, 'error');
          } else {
            // General error
            this.$flash(errorMessageGeneral, 'error');
          }
        })
        .finally(() => {
          this.uploading = false;
        });
    },
  },
  watch: {
    file(file: File | null) {
      if (file === null) {
        return;
      }

      this.uploadFile(file);
    },
  },
  computed: {
    errorMessageTooLarge(): string {
      return <string> this.$tr('uploadBallot.errorTooLarge', {
        size: `${this.maxSizeMB} MB`,
      });
    },
  },
});
</script>

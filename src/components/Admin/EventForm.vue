<template>
  <q-form @submit="sendForm">
    <div class="row q-col-gutter-md q-pb-sm">
      <TranslatableInput
        class="col-12 col-md-8"
        v-model="name"
        outlined
        :label="<string> $tr('fields.name')"
        required="defaultLanguageOnly"
        hide-bottom-space
      />

      <q-input
        v-model="place"
        outlined
        class="col-12 col-md-4"
        :label="$tr('fields.place') + ' *'"
        lazy-rules
        :rules="[$validators.nonEmpty]"
        hide-bottom-space
      />

      <q-input
        v-model="beginning"
        outlined
        class="col-12 col-sm-6 col-md-3"
        type="date"
        :min="nowDate"
        :label="$tr('fields.beginning') + ' *'"
        lazy-rules
        :rules="[$validators.nonEmpty]"
        hide-bottom-space
      />

      <q-input
        v-model="end"
        outlined
        class="col-12 col-sm-6 col-md-3"
        type="date"
        :min="nowDate"
        :label="$tr('fields.end') + ' *'"
        lazy-rules
        :rules="[$validators.nonEmpty]"
        hide-bottom-space
      />

      <q-input
        v-model="soft_deadline"
        outlined
        class="col-12 col-sm-6 col-md-3"
        type="datetime-local"
        :min="nowTime"
        :label="$tr('fields.soft_deadline') + ' *'"
        lazy-rules
        :rules="[$validators.nonEmpty]"
        hide-bottom-space
      />

      <q-input
        v-model="hard_deadline"
        outlined
        class="col-12 col-sm-6 col-md-3"
        type="datetime-local"
        :min="nowTime"
        :label="$tr('fields.hard_deadline') + ' *'"
        lazy-rules
        :rules="[$validators.nonEmpty]"
        hide-bottom-space
      />

      <TranslatableInput
        class="col-12 col-md-7 col-lg-8"
        v-model="invoice"
        outlined
        :label="<string> $tr('fields.invoice')"
        required="none"
        hide-bottom-space
      />

      <q-input
          v-model="reply_email"
          outlined
          class="col-12 col-md-5 col-lg-4"
          :label="<string> $tr('fields.reply_email')"
      />

      <div class="col-12 col-lg-6 row q-col-gutter-md">
        <q-select
          outlined
          v-for="field in Object.keys(selectOptions)"
          :key="field"
          v-model="$data[field]"
          :options="selectOptions[field]"
          :label="<string> $tr(`fields.${field}`)"
          class="col-12 col-sm-6"
          emit-value
          map-options
          :multiple="field === 'dietary_requirements'"
        >
          <template v-slot:prepend>
            <q-icon :name="selectIcons[field]" />
          </template>
        </q-select>
      </div>

      <div class="col-12 col-lg-6 row q-col-gutter-md">
        <q-checkbox
          v-for="field in ['novices', 'email_required', 'membership_required', 'finals']"
          :key="field"
          v-model="$data[field]"
          class="col-12 col-sm-6"
          :true-value="true"
          :false-value="false"
          :label="<string> $tr(`fields.${field}`)"
        />
      </div>

      <div class="col-12">
        <TranslatableInput
          v-model="note"
          type="wysiwyg"
          :label="<string> $tr('fields.note')"
          required="none"
          hide-bottom-space
        />
      </div>

      <div class="text-center col-12">
        <q-btn
            :label="<string> $tr('general.save', null, false)"
            class="q-my-sm"
            type="submit"
            color="primary"
        />
      </div>
    </div>
  </q-form>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import TranslatableInput from 'components/Form/TranslatableInput.vue';
import { Competition, DietaryRequirement, eventOptionalSelectValues } from 'src/types/event';

export default defineComponent({
  name: 'EventForm',
  components: { TranslatableInput },
  data() {
    return {
      translationPrefix: 'admin.newEvent.',
      selectIcons: {
        accommodation: 'fas fa-home',
        meals: 'fas fa-utensils',
        dietary_requirements: 'fas fa-seedling',
        competition: 'fas fa-trophy',
      },

      // Form data below
      pds: this.$isPDS,
      name: {
        cs: '',
        en: '',
      },
      beginning: '',
      end: '',
      place: '',
      soft_deadline: '',
      hard_deadline: '',
      note: {
        cs: '',
        en: '',
      },
      invoice: {
        cs: '',
        en: '',
      },
      novices: false,
      email_required: false,
      membership_required: false,
      finals: false,
      accommodation: 'none',
      meals: 'none',
      competition: null,
      dietary_requirements: [],
      reply_email: '',
    };
  },
  async created() {
    await Promise.all([
      this.$store.dispatch('diets/load'),
      this.$store.dispatch('competitions/load'),
    ]);
  },
  mounted() {
    this.beginning = this.nowDate;
    this.end = this.nowDate;
    this.soft_deadline = this.nowTime;
    this.hard_deadline = this.nowTime;
  },
  computed: {
    nowTime(): string {
      return (new Date()).toISOString().substring(0, 'YYYY-MM-DDTHH:MM'.length);
    },
    nowDate(): string {
      return this.nowTime.substring(0, 'YYYY-MM-DD'.length);
    },
    diets(): DietaryRequirement[] {
      // eslint-disable-next-line max-len
      // eslint-disable-next-line @typescript-eslint/no-unsafe-member-access,@typescript-eslint/no-explicit-any
      return <DietaryRequirement[]> (<any> this.$store.state).diets.diets;
    },
    competitions(): Competition[] {
      // eslint-disable-next-line max-len
      // eslint-disable-next-line @typescript-eslint/no-unsafe-member-access,@typescript-eslint/no-explicit-any
      return <Competition[]> (<any> this.$store.state).competitions.competitions;
    },
    selectOptions(): Record<string, Record<'value' | 'label', string | number | null>[]> {
      const mealsAndAccommodation = eventOptionalSelectValues.map(
        (value) => ({
          value,
          label: <string> this.$tr(`event.optionalSelectValues.${value}`, null, false),
        }),
      );
      return {
        competition: [
          {
            value: null,
            label: 'Žádný',
          },
          ...this.competitions.map((competition) => ({
            value: competition.old_greybox_id,
            label: competition.name,
          })),
        ],
        accommodation: mealsAndAccommodation,
        meals: mealsAndAccommodation,
        dietary_requirements: this.diets.map((diet) => ({
          value: diet.id,
          label: <string> this.$tr(diet.name),
        })),
      };
    },
  },
  methods: {
    sendForm() {
      // TODO - emit
    },
  },
});
</script>

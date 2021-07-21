<template>
  <div>
    <q-card flat bordered class="my-card bg-grey-1 q-mb-md">
      <q-card-section class="header" @click="toggleCard">
        <div class="row items-center no-wrap">
          <div class="col">
            <q-btn
              color="grey-7"
              round
              flat
              :icon="'fas fa-' + (visible ? 'minus' : 'plus')"
              class="float-left"
              size="sm"
            />
            <div class="text-h6">
              <template v-if="formData.name || formData.surname">
                {{ formData.name }} {{ formData.surname }}
              </template>
              <template v-else>
                {{ $tr("types.debater") }} {{ index + 1 }}
              </template>
            </div>
          </div>

          <div class="col-auto">
            <q-icon
              name="fas fa-exclamation-circle"
              class="text-negative"
              v-if="error"
            />
            <q-btn
              color="grey-7"
              round
              flat
              icon="fas fa-trash"
              size="sm"
              @click="$emit('delete', id)"
            />
          </div>
        </div>
      </q-card-section>
      <transition-group
          appear
          enter-active-class="animated slideInDown"
          leave-active-class="animated slideOutUp"
      >
        <q-card-section v-if="showCard">
          <form-fields
              ref="form-fields"
              @input="catchInput"
              :autofill="autofill"
              :is-team="true"
              :accommodationType="accommodationType"
              :mealType="mealType"
              :possibleDiets="possibleDiets"
              :role="1"
              :requireEmail="requireEmail"
          />
        </q-card-section>
      </transition-group>
    </q-card>
  </div>
</template>

<script>
/* eslint-disable */
import formFields from './FormFields';

export default {
  name: 'TeamPersonCard',
  components: {
    formFields,
  },
  props: {
    autofill: Object,
    id: [String, Number],
    index: Number,
    visible: Boolean,
    error: Boolean,
    accommodationType: String,
    mealType: String,
    possibleDiets: Array,
    requireEmail: Boolean,
  },
  data() {
    return {
      showCard: true,
      translationPrefix: 'tournament.',
      formData: {},
    };
  },
  methods: {
    catchInput(data) {
      this.formData = data;
      this.$emit('input', data, this.id);
    },
    toggleCard() {
      this.showCard = ! this.showCard;
      this.$emit('toggleVisibility', this.id);
    }
  },
};
</script>

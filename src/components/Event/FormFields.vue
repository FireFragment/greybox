<template>
  <q-form @submit="sendForm" @reset="resetForm" ref="q-form">
    <div class="row q-col-gutter-md q-pb-sm">
      <q-input
        outlined
        v-model="values.name"
        :label="$tr('fields.name') + ' *'"
        class="col-12 col-sm-6"
        lazy-rules
        :rules="[
          val =>
            (val && val.length > 0) ||
            $tr('general.form.fieldError', null, false)
        ]"
      />

      <q-input
        outlined
        v-model="values.surname"
        :label="$tr('fields.surname') + ' *'"
        class="col-12 col-sm-6"
        lazy-rules
        :rules="[
          val =>
            (val && val.length > 0) ||
            $tr('general.form.fieldError', null, false)
        ]"
      />
    </div>

    <div
      class="block"
      v-if="accommodationType !== 'required' && accommodationType !== 'none'"
    >
      <q-checkbox
        v-model="values.accommodation"
        v-if="accommodationType !== 'opt-in'"
        :true-value="false"
        :false-value="true"
        :label="$tr('fields.accommodation')"
      />
      <q-checkbox
        v-model="values.accommodation"
        v-else
        :true-value="true"
        :false-value="false"
        :label="$tr('fields.accommodationOptIn')"
      />
      <q-icon name="fas fa-info-circle" class="q-pl-sm">
        <q-tooltip anchor="top middle" self="bottom middle" :offset="[0, 0]">
          {{ $tr("fieldNotes.accommodation") }}
        </q-tooltip>
      </q-icon>
    </div>

    <!-- details needed for accommodation -->
    <div
      class="block"
      :class="{ 'form-conditional-block': accommodationType !== 'required' }"
      v-if="accommodationType !== 'none' && values.accommodation === true"
    >
      <div class="row q-col-gutter-sm">
        <div class="col-12 q-field" style="color: rgba(0,0,0,0.54);">
          {{ $tr("fields.birthdate") }} *
        </div>
        <q-select
          outlined
          v-model="values.birthDay"
          :options="days"
          option-value="label"
          :label="$tr('fields.birthDay')"
          class="q-pt-sm q-mb-sm col-12 col-md-4"
          data-select-value="birthDay"
          data-select-options="days"
          lazy-rules
          :rules="[val => val || $tr('general.form.fieldError', null, false)]"
        >
          <template v-slot:prepend>
            <q-icon name="event" />
          </template>
        </q-select>

        <q-select
          outlined
          v-model="values.birthMonth"
          :options="months"
          option-value="label"
          :option-label="item => $tr(item.label, null, false)"
          :label="$tr('fields.birthMonth')"
          class="q-pt-sm q-mb-sm col-12 col-md-4"
          data-select-value="birthMonth"
          data-select-options="months"
          lazy-rules
          :rules="[val => val || $tr('general.form.fieldError', null, false)]"
        >
          <template v-slot:prepend>
            <q-icon name="event" />
          </template>
        </q-select>

        <q-select
          outlined
          v-model="values.birthYear"
          :options="years"
          :label="$tr('fields.birthYear')"
          class="q-pt-sm q-mb-sm col-12 col-md-4"
          data-select-value="birthYear"
          data-select-options="years"
          lazy-rules
          :rules="[val => val || $tr('general.form.fieldError', null, false)]"
        >
          <template v-slot:prepend>
            <q-icon name="event" />
          </template>
        </q-select>
      </div>

      <mask-input
        outlined
        v-model="values.id_number"
        :label="$tr('fields.id_number')"
        class="q-pt-sm"
        mask="#########"
        fill-mask="_"
        :hint="$tr('fieldNotes.example') + ' 123456789'"
        lazy-rules
        :rules="[
          val =>
            !val ||
            val === '#########' ||
            val.toString().match(/\d{9}/) ||
            $tr('general.form.fieldError', null, false)
        ]"
      >
        <template v-slot:prepend>
          <q-icon name="fas fa-id-card" />
        </template>
        <template v-slot:append>
          <q-icon name="fas fa-info-circle" />
          <q-tooltip
            anchor="top middle"
            self="bottom middle"
            :offset="[0, -10]"
          >
            {{ $tr("fieldNotes.id_number") }}
          </q-tooltip>
        </template>
      </mask-input>

      <q-input
        outlined
        v-model="values.street"
        :label="$tr('fields.street') + ' *'"
        class="q-pt-sm"
        :input-class="
          'smartform-street-and-number ' + 'smartform-instance-' + _uid
        "
        lazy-rules
        :rules="[
          val =>
            (val && val.length > 0) ||
            $tr('general.form.fieldError', null, false)
        ]"
      >
        <template v-slot:prepend>
          <q-icon name="fas fa-home" />
        </template>
      </q-input>

      <q-input
        outlined
        v-model="values.city"
        :label="$tr('fields.city') + ' *'"
        class="q-pt-sm"
        :input-class="'smartform-city ' + 'smartform-instance-' + _uid"
        lazy-rules
        :rules="[
          val =>
            (val && val.length > 0) ||
            $tr('general.form.fieldError', null, false)
        ]"
      >
        <template v-slot:prepend>
          <q-icon name="fas fa-city" />
        </template>
      </q-input>

      <mask-input
        outlined
        v-model="values.zip"
        :label="$tr('fields.zip') + ' *'"
        class="q-pt-sm"
        :input-class="'smartform-zip ' + 'smartform-instance-' + _uid"
        mask="### ##"
        fill-mask="_"
        :hint="$tr('fieldNotes.example') + ' 796 01'"
        lazy-rules
        :rules="[
          val =>
            (val && val.toString().match(/\d{3} ?\d{2}/)) ||
            $tr('general.form.fieldError', null, false)
        ]"
      >
        <template v-slot:prepend>
          <q-icon name="fas fa-file-archive" />
        </template>
      </mask-input>
    </div>

    <!-- -SCHOOL FIELD-
    <q-input
      outlined
      v-model="values.school"
      :label="$tr('fields.school') + ' *'"
      class="q-mt-sm"
      lazy-rules
      :rules="[
        val =>
          (val && val.length > 0) || $tr('general.form.fieldError', null, false)
      ]"
    >
      <template v-slot:prepend>
        <q-icon name="fas fa-school" />
      </template>
    </q-input>
    -->
    <!--
        <q-input
          outlined
          v-model="values.phone"
          :label="$tr('fields.phone')"
          mask="+### #########"
          fill-mask="#"
          class="q-pt-sm"
          hint="Např.: +420 123456789"
        >
          <template v-slot:prepend>
            <q-icon name="fas fa-phone-alt" />
          </template>
        </q-input>
        -->

    <div
      class="block q-mt-sm"
      v-if="mealType !== 'required' && mealType !== 'none'"
    >
      <q-checkbox
        v-model="values.meals"
        v-if="mealType !== 'opt-in'"
        :true-value="false"
        :false-value="true"
        :label="$tr('fields.meals')"
      />
      <q-checkbox
        v-model="values.meals"
        v-else
        :true-value="true"
        :false-value="false"
        :label="$tr('fields.mealsOptIn')"
      />
      <!--
      <q-icon name="fas fa-info-circle" class="q-pl-sm">
        <q-tooltip anchor="top middle" self="bottom middle" :offset="[0, 0]">
          {{ $tr("fieldNotes.meals") }}
        </q-tooltip>
      </q-icon>
      -->
    </div>

    <div
      class="block"
      :class="{ 'form-conditional-block': mealType !== 'required' }"
      v-if="
        mealType !== 'none' &&
          values.meals === true &&
          Object.keys(possibleDietsOptions).length !== 0
      "
    >
      <q-select
        outlined
        v-model="values.dietary_requirement"
        :options="possibleDietsOptions"
        option-value="label"
        :label="$tr('fields.diet')"
        class="q-pt-sm q-mb-sm col-12"
        data-select-value="diet"
        data-select-options="possibleDiets"
        lazy-rules
        :rules="[val => val || $tr('general.form.fieldError', null, false)]"
      >
        <template v-slot:prepend>
          <q-icon name="fas fa-utensils" />
        </template>
      </q-select>
    </div>

    <!-- International tournament (PDS) option: -->
    <q-select
      v-if="requireSpeakerStatus"
      outlined
      v-model="values.speaker_status"
      :options="speakerOptions"
      option-value="label"
      :label="$tr('fields.speakerStatus') + ' *'"
      class="q-pt-sm q-mb-sm col-12 col-md-4"
      lazy-rules
      :rules="[val => val || $tr('general.form.fieldError', null, false)]"
    >
    </q-select>

    <!--
        <template>
          <div class="q-mr-lg">
            <q-separator class="q-pl-md q-mt-sm q-pb-none" />
          </div>
          <h2 class="text-h6 q-pa-xs q-mt-sm">{{ $tr('fields.legalGuardian') }}</h2>
          <q-input
            outlined
            v-model="values.parentName"
            :label="$tr('fields.parentName') + ' *'"
            class="col-12 col-sm-6"
            lazy-rules
            :rules="[
              val => (val && val.length > 0) || $tr('general.form.fieldError', null, false)
            ]"
          >
            <template v-slot:prepend>
            </template>
          </q-input>

          <q-input
            outlined
            v-model="values.parentPhone"
            :label="$tr('fields.parentPhone') + ' *'"
            mask="+### #########"
            fill-mask="#"
            class="q-pt-sm"
            hint="Např.: +420 123456789"
            lazy-rules
            :rules="[
              val => (val && val.length > 0) || $tr('general.form.fieldError', null, false)
            ]"
          >
            <template v-slot:prepend>
              <q-icon name="fas fa-phone-alt" />
            </template>
          </q-input>
          <q-input
            outlined
            type="email"
            v-model="values.parentEmail"
            :label="$tr('fields.parentEmail') + ' *'"
            class="q-pt-sm"
            lazy-rules
            :rules="[
              val => (val !== null && val !== '') || $tr('general.form.fieldError', null, false)
            ]"
          >
            <template v-slot:prepend>
              <q-icon name="fas fa-at" />
            </template>
          </q-input>
          <div class="q-mr-lg">
            <q-separator class="q-pl-md q-mt-none q-mb-md q-pb-none" />
          </div>
        </template>
-->
    <q-input
      v-model="values.note"
      class="q-mt-sm"
      outlined
      autogrow
      :label="$tr('fields.note')"
    >
      <template v-slot:prepend>
        <q-icon name="fas fa-sticky-note" />
      </template>
    </q-input>

    <template v-if="!isTeam">
      <g-d-p-r-checkbox v-model="values.accept" :error="acceptError" />

      <div class="text-center">
        <q-btn
          :label="$tr('buttons.continue')"
          class="q-my-sm"
          type="submit"
          color="primary"
        />
        <q-btn
          :label="$tr('buttons.clear')"
          type="reset"
          color="negative"
          flat
          class="q-ml-sm q-my-sm"
        />
      </div>
    </template>
  </q-form>
</template>

<script>
import { EventBus } from "../../event-bus";
import GDPRCheckbox from "./GDPRCheckbox";
import MaskInput from "./MaskInput";

export default {
  name: "FormFields",
  components: { GDPRCheckbox, MaskInput },
  props: {
    autofill: Object,
    isTeam: Boolean,
    accommodationType: String,
    mealType: String,
    possibleDiets: Array,
    role: Number
  },

  data() {
    return {
      translationPrefix: "tournament.",
      values: {
        name: null,
        surname: null,
        id_number: null,
        street: null,
        city: null,
        zip: null,
        // phone: "+420",
        dietary_requirement: null,
        meals: this.mealType !== "opt-in" && this.mealType !== "none",
        accommodation:
          this.accommodationType !== "opt-in" &&
          this.accommodationType !== "none",
        // parentName: null,
        // parentPhone: "+420",
        // parentEmail: null,
        note: null,
        accept: false,
        birthDay: null,
        birthMonth: null,
        birthYear: null,
        speaker_status: null
        // -SCHOOL FIELD- school: null
      },
      acceptError: false,
      selectSearch: null,
      days: [],
      months: [],
      years: [],
      possibleDietsOptions: [],
      requireSpeakerStatus: this.$isPDS && this.role === 1, // only for PDS debaters
      speakerOptions: [
        {
          label: this.$tr("tournament.fields.EFL"),
          value: "efl"
        },
        {
          label: this.$tr("tournament.fields.ESL"),
          value: "esl"
        },
        {
          label: this.$tr("tournament.fields.ENL"),
          value: "enl"
        }
      ]
    };
  },

  created() {
    // Load date select options
    for (let i = 1; i <= 31; i++) {
      this.days.push({
        label: i + ".",
        value: ("0" + i).substr(-2)
      });
    }
    for (let i = new Date().getFullYear() - 10; i >= 1900; i--) {
      this.years.push(i);
    }
    for (let i = 0; i < 12; i++) {
      this.months[i] = {
        label: "general.months." + i,
        value: ("0" + (i + 1)).substr(-2),
        searchable: i + 1
      };
    }

    for (let i in this.possibleDiets) {
      this.possibleDietsOptions[i] = {
        label:
          this.$tr(this.possibleDiets[i].name)
            .charAt(0)
            .toUpperCase() + this.$tr(this.possibleDiets[i].name).slice(1),
        value: this.possibleDiets[i].id
      };
      //if last:
      if (i == this.possibleDiets.length - 1) {
        this.values.dietary_requirement = {
          label: this.possibleDietsOptions[0].label,
          value: this.possibleDietsOptions[0].value
        };
      }
    }

    // Smartform autocomplete select
    EventBus.$on("smartform", data => {
      // If instance ID is this form
      if (data.instance.substr(-(this._uid + "").length) == this._uid)
        this.values[data.field] = data.value;
    });
  },

  mounted() {
    // Renitialize smartform
    window.smartform.rebindAllForms(true, () => {
      // Loop through instances
      window.smartform.getInstanceIds().forEach(id => {
        let instance = window.smartform.getInstance(id);

        // Set limit to 3 results for every field
        [
          "smartform-street-and-number",
          "smartform-city",
          "smartform-zip"
        ].forEach(input => {
          instance.getBox(input).setLimit(3);
        });

        // Run this callback on selection
        instance.setSelectionCallback((element, value, fieldType) => {
          let field = fieldType.substr("10");

          let varName = field !== "street-and-number" ? field : "street";

          // Emit global event so other form instances can receive it
          EventBus.$emit("smartform", {
            instance: id,
            field: varName,
            value: value
          });
        });
      });
    });
  },

  methods: {
    sendForm() {
      if (!this.isTeam && !this.values.accept)
        return !(this.acceptError = true);

      let formData = this.submitData;

      // Check if autofill data have changed or not
      let autofillData = false;
      if (this.autofill) {
        autofillData = {
          id: this.autofill.id,
          edited: false
        };

        for (let index in formData)
          if (
            formData[index] != this.autofill[index] &&
            index !== "accommodation" &&
            index !== "meals"
          )
            autofillData.edited = true;
      }

      this.$emit("submit", formData, autofillData);
      return {
        formData: formData,
        autofillData: autofillData
      };
    },

    resetForm() {
      this.$emit("goToRolePick");
    },

    // Key pressed inside select
    // -> Search for corresponding value
    /*selectKeyPress(a) {
      let selectOptions = a.target.getAttribute("data-select-options");
      let selectValue = a.target.getAttribute("data-select-value");
      let pressedKey = a.key;

      // String to search for
      this.selectSearch = this.selectSearch
        ? this.selectSearch + pressedKey.toString()
        : pressedKey;

      // Search label/whole value
      let match = false;
      this[selectOptions].forEach(option => {
        if (match) return;
        let value =
          typeof option === "object" ? option.label : option.toString();
        if (value.startsWith(this.selectSearch)) match = option;
      });

      // Search value
      if (!match && typeof this[selectOptions][0] == "object")
        this[selectOptions].forEach(option => {
          if (match) return;
          let value = option.value.toString();
          if (value.startsWith(this.selectSearch)) match = option;
        });

      // Search searchable option
      if (
        !match &&
        typeof this[selectOptions][0] == "object" &&
        this[selectOptions][0].searchable
      )
        this[selectOptions].forEach(option => {
          if (match) return;
          let value = option.searchable.toString();
          if (value.startsWith(this.selectSearch)) match = option;
        });

      if (match) this.values[selectValue] = match;
      else this.selectResetSearch();
    },
    selectResetSearch() {
      this.selectSearch = null;
    },*/
    birthdateFormatter(year, month, day) {
      if (!year && !month && !day) return null;
      else
        return (
          (year ? year : "0000") +
          "-" +
          (month ? month.value : "00") +
          "-" +
          (day ? day.value : "00")
        );
    }
  },

  watch: {
    autofill() {
      let data = this.autofill;

      // Move values from autofill prop to value variable
      for (let key in data) {
        // Birth date -> split day, month and year
        if (key === "birthdate") {
          // Birthday is not null
          if (data[key]) {
            let value = data[key].split("-");
            this.values.birthYear = parseInt(value[0]);
            this.values.birthMonth = this.months[parseInt(value[1]) - 1];
            this.values.birthDay = this.days[parseInt(value[2]) - 1];
          } else
            this.values.birthDay = this.values.birthMonth = this.values.birthYear = null;
        }
        // Diet -> pick correct value object
        else if (key === "dietary_requirement") {
          this.values[key] = this.possibleDietsOptions.filter(
            item => item.value === data[key]
          )[0];
        }
        // Speaker status -> pick correct value object
        else if (key === "speaker_status") {
          this.values[key] = this.speakerOptions.filter(
            item => item.value === data[key]
          )[0];
        }
        // Any other field -> pass raw value
        else this.values[key] = data[key];
      }
    },

    values: {
      handler() {
        this.$emit("input", this.submitData);
      },
      deep: true
    }
  },

  computed: {
    submitData() {
      let returnObject = {
        name: this.values.name ? this.values.name.trim() : null,
        surname: this.values.surname ? this.values.surname.trim() : null,
        note: this.values.note,
        meals: this.values.meals,
        dietary_requirement: this.values.dietary_requirement
          ? this.values.dietary_requirement.value
          : null
      };

      // Include accommodation data if it is requred or user wants it
      if (
        this.accommodationType === "required" ||
        (this.accommodationType !== "none" &&
          this.values.accommodation === (this.accommodationType === "opt-in"))
      ) {
        returnObject.accommodation = true;
        returnObject.birthdate = this.birthdateFormatter(
          this.values.birthYear,
          this.values.birthMonth,
          this.values.birthDay
        );
        returnObject.id_number =
          this.values.id_number === "_________" ? null : this.values.id_number;
        returnObject.street = this.values.street;
        returnObject.city = this.values.city;
        returnObject.zip = this.values.zip
          ? this.values.zip.replace(" ", "")
          : "";
      } else returnObject.accommodation = false;

      // PDS -> include speaker status
      if (this.requireSpeakerStatus)
        returnObject.speaker_status = this.values.speaker_status
          ? this.values.speaker_status.value
          : null;

      return returnObject;
    }
  }
};
</script>

<style scoped></style>

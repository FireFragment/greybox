<template>
  <q-form @submit="sendForm">
    <div class="row q-col-gutter-md q-pb-sm">
      <q-input
        outlined
        v-model="name"
        label="Jméno *"
        class="col-12 col-sm-6"
        lazy-rules
        :rules="[val => (val && val.length > 0) || 'Vyplňte prosím toto pole']"
      />

      <q-input
        outlined
        v-model="surname"
        label="Příjmení *"
        class="col-12 col-sm-6"
        lazy-rules
        :rules="[val => (val && val.length > 0) || 'Vyplňte prosím toto pole']"
      />
    </div>

    <div class="row q-col-gutter-sm" @keydown="selectKeyPress">
      <div class="col-12 q-field" style="color: rgba(0,0,0,0.54);">
        Zadejte datum narození *:
      </div>
      <q-select
        outlined
        v-model="birthDay"
        :options="days"
        option-value="label"
        label="Den"
        class="q-pt-sm q-mb-sm col-4"
        data-select-value="birthDay"
        data-select-options="days"
        @focus="selectResetSearch"
        lazy-rules
        :rules="[val => val || 'Vyplňte prosím toto pole']"
      >
        <template v-slot:prepend>
          <q-icon name="event" />
        </template>
      </q-select>

      <q-select
        outlined
        v-model="birthMonth"
        :options="months"
        option-value="label"
        label="Měsíc"
        class="q-pt-sm q-mb-sm col-4"
        data-select-value="birthMonth"
        data-select-options="months"
        @focus="selectResetSearch"
        lazy-rules
        :rules="[val => val || 'Vyplňte prosím toto pole']"
      >
        <template v-slot:prepend>
          <q-icon name="event" />
        </template>
      </q-select>

      <q-select
        outlined
        v-model="birthYear"
        :options="years"
        label="Rok"
        class="q-pt-sm q-mb-sm col-4"
        data-select-value="birthYear"
        data-select-options="years"
        @focus="selectResetSearch"
        lazy-rules
        :rules="[val => val || 'Vyplňte prosím toto pole']"
      >
        <template v-slot:prepend>
          <q-icon name="event" />
        </template>
      </q-select>
    </div>

    <q-input
      outlined
      v-model="id_number"
      label="Číslo občanského průkazu"
      class="q-pt-sm"
      mask="#########"
      fill-mask="_"
      hint="Vzor: 123456789"
      lazy-rules
      :rules="[
        val =>
          !val ||
          val === '#########' ||
          val.toString().match(/\d{9}/) ||
          'Vyplňte prosím toto pole'
      ]"
    >
      <template v-slot:prepend>
        <q-icon name="fas fa-id-card" />
      </template>
      <template v-slot:append>
        <q-icon name="fas fa-info-circle" />
        <q-tooltip anchor="top middle" self="bottom middle" :offset="[0, -10]">
          Vyplňte prosím, pokud již občanský průkaz máte.
        </q-tooltip>
      </template>
    </q-input>

    <q-input
      outlined
      v-model="street"
      label="Ulice a číslo *"
      class="q-pt-sm"
      input-class="smartform-street-and-number"
      lazy-rules
      :rules="[val => (val && val.length > 0) || 'Vyplňte prosím toto pole']"
    >
      <template v-slot:prepend>
        <q-icon name="fas fa-home" />
      </template>
    </q-input>

    <q-input
      outlined
      v-model="city"
      label="Město *"
      class="q-pt-sm"
      input-class="smartform-city"
      lazy-rules
      :rules="[val => (val && val.length > 0) || 'Vyplňte prosím toto pole']"
    >
      <template v-slot:prepend>
        <q-icon name="fas fa-city" />
      </template>
    </q-input>

    <q-input
      outlined
      v-model="zip"
      label="Číslo PSČ *"
      class="q-pt-sm"
      input-class="smartform-zip"
      mask="### ##"
      fill-mask="_"
      hint="Vzor: 796 01"
      lazy-rules
      :rules="[
        val =>
          (val && val.toString().match(/\d{3} ?\d{2}/)) ||
          'Vyplňte prosím toto pole'
      ]"
    >
      <template v-slot:prepend>
        <q-icon name="fas fa-file-archive" />
      </template>
    </q-input>
    <!--
        <q-input
          outlined
          v-model="phone"
          label="Telefonní číslo"
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
    <div class="block">
      <q-checkbox v-model="vegetarian" label="Vegetariánská strana" />
    </div>
    <!--
        <div class="block">
          <q-checkbox
            v-model="accomodation"
            label="Nechci zařídit ubytování"
          />
          <q-icon name="fas fa-info-circle" class="q-pl-sm">
            <q-tooltip
              anchor="top middle"
              self="bottom middle"
              :offset="[0, 0]"
            >
              Pro individuální požadavky k ubytování vyplňte prosím poznámku.
            </q-tooltip>
          </q-icon>
        </div>

        <template>
          <div class="q-mr-lg">
            <q-separator class="q-pl-md q-mt-sm q-pb-none" />
          </div>
          <h2 class="text-h6 q-pa-xs q-mt-sm">Zákonný zástupce</h2>
          <q-input
            outlined
            v-model="parentName"
            label="Celé jméno *"
            class="col-12 col-sm-6"
            lazy-rules
            :rules="[
              val => (val && val.length > 0) || 'Vyplňte prosím toto pole'
            ]"
          >
            <template v-slot:prepend>
              <q-icon name="fas fa-signature" />
            </template>
          </q-input>

          <q-input
            outlined
            v-model="parentPhone"
            label="Telefonní číslo *"
            mask="+### #########"
            fill-mask="#"
            class="q-pt-sm"
            hint="Např.: +420 123456789"
            lazy-rules
            :rules="[
              val => (val && val.length > 0) || 'Vyplňte prosím toto pole'
            ]"
          >
            <template v-slot:prepend>
              <q-icon name="fas fa-phone-alt" />
            </template>
          </q-input>
          <q-input
            outlined
            type="email"
            v-model="parentEmail"
            label="E-mailová adresa *"
            class="q-pt-sm"
            lazy-rules
            :rules="[
              val => (val !== null && val !== '') || 'Vyplňte prosím e-mail'
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
    <q-input v-model="note" class="q-mt-sm" outlined autogrow label="Poznámka">
      <template v-slot:prepend>
        <q-icon name="fas fa-sticky-note" />
      </template>
    </q-input>

    <q-checkbox
      v-model="accept"
      :class="{ 'q-field--error': acceptError && !accept }"
    >
      {{ $tr("tournament.gdpr.label") }}
      <a @click="showGDPRModal = true">{{ $tr("tournament.gdpr.link") }}</a>
    </q-checkbox>
    <q-dialog v-model="showGDPRModal">
      <q-card class="dialog-medium">
        <q-card-section class="row items-center">
          <div class="text-h6">
            {{ $tr("tournament.gdpr.modal.title") }}
          </div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-card-section>
          {{ $tr("tournament.gdpr.modal.opening") }}
          <ul>
            <li
              v-for="item in $tr('tournament.gdpr.modal.list')"
              v-bind:key="item"
            >
              {{ item }}
            </li>
          </ul>
          {{ $tr("tournament.gdpr.modal.closing") }}
        </q-card-section>
      </q-card>
    </q-dialog>

    <div class="text-center">
      <q-btn label="Pokračovat" type="submit" color="primary" />
      <q-btn
        label="Vymazat"
        type="reset"
        color="primary"
        flat
        class="q-pt-sm q-ml-sm"
      />
    </div>
  </q-form>
</template>

<script>
import { EventBus } from "../../event-bus";

export default {
  name: "FormFields",

  props: {
    autofill: Object
  },

  data() {
    return {
      name: null,
      surname: null,
      id_number: null,
      street: null,
      city: null,
      zip: null,
      phone: "+420",
      vegetarian: false,
      accomodation: true,
      parentName: null,
      parentPhone: "+420",
      parentEmail: null,
      note: null,
      accept: false,
      acceptError: false,
      showGDPRModal: false,
      birthDay: null,
      birthMonth: null,
      birthYear: null,
      days: [],
      selectSearch: null,
      months: [
        "leden",
        "únor",
        "březen",
        "duben",
        "květen",
        "červen",
        "červenec",
        "srpen",
        "září",
        "říjen",
        "listopad",
        "prosinec"
      ],
      years: []
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
    for (let i = 0; i < this.months.length; i++) {
      let monthName = this.months[i];
      this.months[i] = {
        label: monthName,
        value: ("0" + (i + 1)).substr(-2),
        searchable: i + 1
      };
    }

    // Listen to SmartForm address autocomplet
    EventBus.$on("smartFormAutocomplete", data => {
      let varName = data.field !== "street-and-number" ? data.field : "street";
      this[varName] = data.value;
    });
  },

  mounted() {
    // Remove label toggling on inner link click
    document.querySelectorAll(".q-checkbox__label a").forEach(link => {
      link.addEventListener("click", e => {
        e.stopPropagation();
        e.preventDefault();
      });
    });
  },

  beforeDestroy() {
    document.querySelectorAll(".q-checkbox__label a").forEach(link => {
      link.removeEventListener("click", e => {
        e.stopPropagation();
        e.preventDefault();
      });
    });
  },

  methods: {
    sendForm() {
      if (!this.accept) return (this.acceptError = true);

      this.$emit("submit", {
        name: this.name,
        surname: this.surname,
        birthdate:
          this.birthYear +
          "-" +
          this.birthMonth.value +
          "-" +
          this.birthDay.value,
        id_number: this.id_number === "_________" ? null : this.id_number,
        street: this.street,
        vegetarian: this.vegetarian,
        city: this.city,
        zip: this.zip.replace(" ", ""),
        note: this.note
      });
    },

    // Key pressed inside select
    // -> Search for corresponding value
    selectKeyPress(a) {
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

      if (match) this[selectValue] = match;
      else this.selectResetSearch();
    },
    selectResetSearch() {
      this.selectSearch = null;
    }
  },

  watch: {
    autofill() {
      let data = this.autofill;
      for (let key in data) {
        if (key === "birthdate") {
          let value = data[key].split("-");
          this.birthYear = parseInt(value[0]);
          this.birthMonth = this.months[parseInt(value[1]) - 1];
          this.birthDay = this.days[parseInt(value[2]) - 1];
        } else this[key] = data[key];
      }
    }
  }
};
</script>

<style scoped></style>

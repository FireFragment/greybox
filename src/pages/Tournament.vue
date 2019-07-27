<template>
  <q-page padding>
    <h1 class="text-center text-h4">Registrace na turnaj</h1>
    <p class="text-center">
      This is a tournament page with ID <i>{{ $route.params.id }}</i> and slug
      <i>{{ $route.params.slug }}</i>
    </p>
    <div class="row q-col-gutter-md reverse">
      <div class="col-12 col-sm-4 col-md-6">
        <debatersCard />
      </div>
      <q-form class="col-12 col-sm-8 col-md-6" @submit="sendForm">
        <div class="row q-col-gutter-md q-pb-sm">
          <q-input
            outlined
            v-model="name"
            label="Jméno *"
            class="col-12 col-sm-6"
            lazy-rules
            :rules="[
              val => (val && val.length > 0) || 'Vyplňte prosím toto pole'
            ]"
          />

          <q-input
            outlined
            v-model="surname"
            label="Příjmení *"
            class="col-12 col-sm-6"
            lazy-rules
            :rules="[
              val => (val && val.length > 0) || 'Vyplňte prosím toto pole'
            ]"
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
          fill-mask="#"
          hint="Vzor: 123456789"
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
              Vyplňte prosím, pokud již občanský průkaz máte.
            </q-tooltip>
          </template>
        </q-input>

        <q-input
          outlined
          v-model="street"
          label="Ulice a číslo *"
          class="q-pt-sm"
          lazy-rules
          :rules="[
            val => (val && val.length > 0) || 'Vyplňte prosím toto pole'
          ]"
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
          lazy-rules
          :rules="[
            val => (val && val.length > 0) || 'Vyplňte prosím toto pole'
          ]"
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
          mask="### ##"
          fill-mask="#"
          hint="Vzor: 796 01"
          lazy-rules
          :rules="[
            val => (val && val.length > 0) || 'Vyplňte prosím toto pole'
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
          <q-checkbox
            v-model="vegetarian"
            label="Vegetariánská strana"
            true-value="1"
            false-value="0"
          />
        </div>
        <!--
        <div class="block">
          <q-checkbox
            v-model="noAccomodation"
            label="Nechci zařídit ubytování"
            true-value="yes"
            false-value="no"
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
        <q-input
          v-model="note"
          class="q-mt-sm"
          outlined
          autogrow
          label="Poznámka"
        >
          <template v-slot:prepend>
            <q-icon name="fas fa-sticky-note" />
          </template>
        </q-input>

        <q-checkbox
          v-model="accept"
          label="Souhlasím s podmínkami přihlášení"
          true-value="1"
          false-value="0"
          lazy-rules
          :rules="[val => val == '352' || 'Vyplňte prosím toto pole']"
        />

        <div class="text-center">
          <q-btn label="Odeslat" type="submit" color="primary" />
          <q-btn
            label="Vymazat"
            type="reset"
            color="primary"
            flat
            class="q-pt-sm q-ml-sm"
          />
        </div>
      </q-form>
    </div>
  </q-page>
</template>

<script>
import debatersCard from "../components/DebatersCard";

export default {
  name: "PageTournament",

  components: {
    debatersCard
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
      noAccomodation: false,
      parentName: null,
      parentPhone: "+420",
      parentEmail: null,
      note: null,
      accept: false,
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
  },
  mounted() {
    this.$el.querySelectorAll("input[type=checkbox]").forEach(el => {
      el.click();
    });
  },

  methods: {
    sendForm() {
      this.$api({
        url: "person",
        sendToken: false,
        data: {
          name: this.name,
          surname: this.surname,
          birthdate:
            this.birthYear +
            "-" +
            this.birthMonth.value +
            "-" +
            this.birthDay.value,
          id_number: this.id_number,
          street: this.street,
          vegetarian: this.vegetarian,
          city: this.city,
          zip: this.zip.replace(" ", ""),
          note: this.note
        },
        alerts: false
      })
        .then(data => {
          console.log(data);
        })
        .catch(data => {
          /*if (data.response.data)
              for (let index in data.response.data)
                data.response.data[index].forEach(message => {
                  this.$flash(message, "error", false, 5000);
                });
            else this.$flash("An error had occured, please try again.", "error");*/
          console.log(data);
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
        if (value.startsWith(this.selectSearch))
          match = option;
      });

      // Search value
      if (!match && typeof this[selectOptions][0] == "object")
        this[selectOptions].forEach(option => {
          if (match) return;
          let value = option.value.toString();
          if (value.startsWith(this.selectSearch))
            match = option;
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
          if (value.startsWith(this.selectSearch))
            match = option;
        });

      if (match)
        this[selectValue] = match;
      else
        this.selectResetSearch();
    },
    selectResetSearch() {
      this.selectSearch = null;
    }
  }
};
</script>

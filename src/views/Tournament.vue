<template>
  <q-page padding>
    <q-form @submit="onSubmit" @reset="onReset" class="q-gutter-md">
      <q-input
        outlined
        v-model="firstname"
        label="Jméno *"
        lazy-rules
        :rules="[val => (val && val.length > 0) || 'Vyplňte prosím toto pole']"
      />

      <q-input
        outlined
        v-model="lastname"
        label="Příjmení *"
        lazy-rules
        :rules="[val => (val && val.length > 0) || 'Vyplňte prosím toto pole']"
      />

      <q-input
        outlined
        type="text"
        v-model="birthdate"
        label="Vaše datum narození *"
        lazy-rules
        :rules="[val => (val !== null && val !== '') || 'Vyplňte prosím číslo']"
      >
        <template v-slot:prepend>
          <q-icon name="event" />
        </template>
      </q-input>

      <q-input
        outlined
        v-model="idnumber"
        label="Číslo občanského průkazu *"
        mask="#########"
        fill-mask="#"
        hint="Vzor: 123456789"
        lazy-rules
        :rules="[val => (val !== null && val !== '') || 'Vyplňte prosím číslo']"
      >
        <template v-slot:prepend>
          <q-icon name="fas fa-id-card" />
        </template>
      </q-input>

      <q-input
        outlined
        v-model="street"
        label="Ulice a číslo *"
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
        lazy-rules
        :rules="[val => (val && val.length > 0) || 'Vyplňte prosím toto pole']"
      >
        <template v-slot:prepend>
          <q-icon name="fas fa-city" />
        </template>
      </q-input>

      <q-input
        outlined
        v-model="zipcode"
        label="Číslo PSČ *"
        mask="### ##"
        fill-mask="#"
        hint="Vzor: 796 01"
        lazy-rules
        :rules="[val => (val && val.length > 0) || 'Vyplňte prosím toto pole']"
      >
        <template v-slot:prepend>
          <q-icon name="fas fa-file-archive" />
        </template>
      </q-input>

      <q-input v-model="note" outlined autogrow label="Poznámka" />

      <q-toggle v-model="accept" label="Souhlasím s podmínkami přihlášení" />

      <div>
        <q-btn label="Odeslat" type="submit" color="primary" />
        <q-btn
          label="Vymazat"
          type="reset"
          color="primary"
          flat
          class="q-ml-sm"
        />
      </div>
    </q-form>
  </q-page>
</template>

<script>
export default {
  name: "PageTournament",
  data() {
    return {
      firstname: null,
      lastname: null,
      birthdate: null,
      idnumber: null,
      street: null,
      city: null,
      zipcode: null,
      note: null,
      accept: null
    };
  }
};
</script>

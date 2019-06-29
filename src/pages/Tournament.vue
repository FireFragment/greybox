<template>
  <q-page padding>
    <h1 class="text-center text-h4">Registrace na turnaj</h1>
    <p class="text-center">
      This is a tournament page with ID <i>{{ $route.params.id }}</i> and slug
      <i>{{ $route.params.slug }}</i>
    </p>
    <div class="row q-col-gutter-md reverse">
      <div class="col-12 col-sm-4 col-md-6">
        <q-list
          class="rounded-borders shadow-2 q-pb-sm bg-white debaters-sticky-card"
        >
          <p class="text-center q-pb- q-pt-md q-pl-sm q-pr-sm">
            Převyplnit údaje dle dříve zadaných osob:
          </p>
          <q-scroll-area style="height: calc(100vh - 130px);">
            <q-item
              clickable
              v-ripple
              dense
              v-for="(pastLogin, index) in pastLogins"
              v-bind:key="index"
              class="q-pt-sm q-pb-sm"
            >
              <q-item-section>{{ pastLogin }}</q-item-section>
              <q-item-section avatar>
                <q-avatar
                  :style="
                    'background-color: ' + stringToHslColor(pastLogin, 50, 60)
                  "
                  size="30px"
                >
                  <img
                    src="https://cdn.quasar.dev/img/avatar.png"
                    v-if="!true"
                  />
                  <template v-else>{{ pastLogin.substr(0, 1) }}</template>
                </q-avatar>
              </q-item-section>
            </q-item>
          </q-scroll-area>
        </q-list>
      </div>
      <q-form
        @submit="onSubmit"
        @reset="onReset"
        class="col-12 col-sm-8 col-md-6"
      >
        <div class="row q-col-gutter-md q-pb-sm">
          <q-input
            outlined
            v-model="firstname"
            label="Jméno *"
            class="col-12 col-sm-6"
            lazy-rules
            :rules="[
              val => (val && val.length > 0) || 'Vyplňte prosím toto pole'
            ]"
          />

          <q-input
            outlined
            v-model="lastname"
            label="Příjmení *"
            class="col-12 col-sm-6"
            lazy-rules
            :rules="[
              val => (val && val.length > 0) || 'Vyplňte prosím toto pole'
            ]"
          />
        </div>

        <q-input
          outlined
          type="text"
          v-model="birthdate"
          label="Vaše datum narození *"
          lazy-rules
          :rules="[
            val => (val !== null && val !== '') || 'Vyplňte prosím číslo'
          ]"
        >
          <template v-slot:prepend>
            <q-icon name="event" />
          </template>
        </q-input>

        <q-input
          outlined
          v-model="idnumber"
          label="Číslo občanského průkazu"
          class="q-pt-sm"
          mask="#########"
          fill-mask="#"
          hint="Vzor: 123456789"
        >
          <template v-slot:prepend>
            <q-icon name="fas fa-id-card" />
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
          v-model="zipcode"
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

        <q-input
          v-model="note"
          class="q-mt-sm"
          outlined
          autogrow
          label="Poznámka"
        />

        <q-toggle v-model="accept" label="Souhlasím s podmínkami přihlášení" />

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
      accept: null,
      pastLogins: [
        "Adam Karásek",
        "Jakub Szymsze",
        "Ladislav Burgr",
        "Vašek",
        "Zbyněk",
        "Jakub Szymsze",
        "Ladislav Burgr",
        "Vašek",
        "Zbyněk",
        "Jakub Szymsze",
        "Ladislav Burgr",
        "Vašek",
        "Zbyněk",
        "Jakub Szymsze",
        "Ladislav Burgr",
        "Vašek",
        "Zbyněk",
        "Jakub Szymsze",
        "Ladislav Burgr",
        "Vašek",
        "Zbyněk",
        "Jakub Szymsze",
        "Ladislav Burgr",
        "Vašek",
        "Zbyněk"
      ]
    };
  }
};
</script>

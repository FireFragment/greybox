<template>
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
        @click="selectDebater(index)"
        class="q-pt-sm q-pb-sm"
      >
        <q-item-section>{{
          pastLogin.name + " " + pastLogin.surname
        }}</q-item-section>
        <q-item-section avatar>
          <q-avatar
            :style="
              'background-color: ' + $stringToHslColor(pastLogin.name, 50, 60)
            "
            size="30px"
          >
            <img src="https://cdn.quasar.dev/img/avatar.png" v-if="!true" />
            <template v-else>{{ pastLogin.name.substr(0, 1) }}</template>
          </q-avatar>
        </q-item-section>
      </q-item>
    </q-scroll-area>
  </q-list>
</template>
<script>
export default {
  data() {
    return {
      pastLogins: []
    };
  },

  created() {
    this.pastLogins = [
      {
        name: "Ladislav",
        surname: "Burgr",
        id_number: "123456789",
        street: "Testovací 54",
        city: "Praha",
        zip: "32426",
        phone: "+420456781234",
        vegetarian: 1,
        accomodation: 0,
        birthDay: {
          label: "2.",
          value: "02"
        },
        birthMonth: {
          label: "duben",
          value: "04",
          searchable: 4
        },
        birthYear: 1902,
        note: "bezlepková strava"
      },
      {
        name: "Jakub",
        surname: "Szymsza",
        id_number: "321654987",
        street: "Domácí 13",
        city: "Prostějov",
        zip: "79604",
        phone: "+420777888999",
        vegetarian: 0,
        accomodation: 1,
        birthDay: {
          label: "1.",
          value: "01"
        },
        birthMonth: {
          label: "červen",
          value: "06",
          searchable: 6
        },
        birthYear: 2001,
        note: ""
      }
    ];
  },

  methods: {
    selectDebater(index) {
      this.$emit("debaterSelected", this.pastLogins[index]);
    }
  }
};
</script>

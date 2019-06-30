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
        class="q-pt-sm q-pb-sm"
      >
        <q-item-section>{{ pastLogin }}</q-item-section>
        <q-item-section avatar>
          <q-avatar
            :style="'background-color: ' + stringToHslColor(pastLogin, 50, 60)"
            size="30px"
          >
            <img src="https://cdn.quasar.dev/img/avatar.png" v-if="!true" />
            <template v-else>{{ pastLogin.substr(0, 1) }}</template>
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

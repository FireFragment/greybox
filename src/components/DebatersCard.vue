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
            :style="'background-color: ' + $stringToHslColor(pastLogin.name)"
            size="30px"
          >
            <img src="https://cdn.quasar.dev/img/avatar.png" v-if="!true" />
            <template v-else>{{
              pastLogin.name.substr(0, 1).toUpperCase()
            }}</template>
          </q-avatar>
        </q-item-section>
      </q-item>
    </q-scroll-area>
    <q-inner-loading :showing="showLoading">
      <q-spinner color="primary" size="3em" />
    </q-inner-loading>
  </q-list>
</template>
<script>
export default {
  data() {
    return {
      pastLogins: [],
      showLoading: true
    };
  },

  methods: {
    selectDebater(index) {
      this.$emit("debaterSelected", this.pastLogins[index]);
    }
  },

  created() {
    this.$api({
      url: "user/" + this.$auth.user().id + "/person",
      method: "get"
    })
      .then(d => {
        this.pastLogins = d.data;
      })
      .finally(() => {
        this.showLoading = false;
      });
  }
};
</script>

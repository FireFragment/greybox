<template>
  <q-page class="flex flex-center content-center">
    <div class="row full-width text-center">
      <img class="q-mx-auto" alt="Logo ADK" src="../assets/logo_napis.png" />
      Renamed: {{ drawerStateRenamed }}<br>
      State: {{ drawerState }}<br>
      Getter: {{ drawerStateGetter }}<br>
      <button @click="drawerStateRenamed = 'changed 1'">CHANGE 1</button>
      <button @click="this.$store.commit('test/updateDrawerState', 'changed 2');">CHANGE 2</button>
    </div>
    <div class="row q-mt-xl flex-center" v-if="!$auth.isLoggedIn()">
      <q-btn
        class="q-mx-md q-mb-md hidden-link"
        :to="$path('auth.login')"
        icon="fas fa-sign-in-alt"
        :label="$tr('auth.login.link')"
        color="primary"
        size="lg"
      />
      <q-btn
        class="q-mx-md q-mb-md hidden-link"
        :to="$path('auth.signUp')"
        icon="fas fa-user-plus"
        :label="$tr('auth.signUp.link')"
        color="blue-9"
        size="lg"
      />
    </div>
  </q-page>
</template>

<style></style>

<script lang="ts">
import { mapGetters, mapState } from 'vuex';
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'Homepage',
  async created() {
    await this.$store.dispatch('test/testAction');
  },
  computed: {
    ...mapGetters('test', [
      'drawerStateGetter',
    ]),
    ...mapState('test', [
      'drawerState',
    ]),
    drawerStateRenamed: {
      get(): string {
        return this.$store.state.test.drawerState;
      },
      set(value: boolean) {
        this.$store.commit('test/updateDrawerState', value);
      },
    },
  },
});
</script>

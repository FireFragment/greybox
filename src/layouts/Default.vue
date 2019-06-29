<template>
  <q-layout view="lHh Lpr lFf" :class="'page-' + $router.currentRoute.name">
    <q-header elevated>
      <q-toolbar>
        <q-btn
          flat
          dense
          round
          @click="leftDrawerOpen = !leftDrawerOpen"
          aria-label="Menu"
          icon="menu"
        />

        <q-toolbar-title>
          Greybox 2.0
        </q-toolbar-title>

        <q-btn-dropdown stretch flat>
          <template v-slot:label>
            <q-avatar color="orange">
              <img src="https://cdn.quasar.dev/img/avatar.png" v-if="true">
              <template v-else>V</template>
            </q-avatar>
            Vašek
          </template>
          <q-list>
            <q-item-label header>Folders</q-item-label>
            <q-item
              v-for="n in 3"
              :key="`x.${n}`"
              clickable
              v-close-popup
              tabindex="0"
            >
              <q-item-section avatar>
                <q-avatar icon="folder" color="secondary" text-color="white" />
              </q-item-section>
              <q-item-section>
                <q-item-label>Photos</q-item-label>
                <q-item-label caption>February 22, 2016</q-item-label>
              </q-item-section>
              <q-item-section side>
                <q-icon name="info" />
              </q-item-section>
            </q-item>
            <q-separator inset spaced />
            <q-item-label header>Files</q-item-label>
            <q-item
              v-for="n in 3"
              :key="`y.${n}`"
              clickable
              v-close-popup
              tabindex="0"
            >
              <q-item-section avatar>
                <q-avatar
                  icon="assignment"
                  color="primary"
                  text-color="white"
                />
              </q-item-section>
              <q-item-section>
                <q-item-label>Vacation</q-item-label>
                <q-item-label caption>February 22, 2016</q-item-label>
              </q-item-section>
              <q-item-section side>
                <q-icon name="info" />
              </q-item-section>
            </q-item>
          </q-list>
        </q-btn-dropdown>
      </q-toolbar>
    </q-header>

    <q-drawer v-model="leftDrawerOpen" bordered content-class="bg-grey-2">
      <q-list>
        <q-item to="/" exact>
          <q-item-section avatar>
            <q-icon name="home" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Hlavní stránka</q-item-label>
          </q-item-section>
        </q-item>
        <q-item-label header>Turnaje</q-item-label>
        <q-item to="/turnaj" exact>
          <q-item-section avatar>
            <q-icon name="flag" />
          </q-item-section>
          <q-item-section>
            <q-item-label>1. český turnaj</q-item-label>
          </q-item-section>
        </q-item>
        <q-item to="/turnaj" exact>
          <q-item-section avatar>
            <q-icon name="flag" />
          </q-item-section>
          <q-item-section>
            <q-item-label>2. anglický turnaj</q-item-label>
          </q-item-section>
        </q-item>

        <q-item-label header>Důležité odkazy</q-item-label>
        <q-item
          clickable
          tag="a"
          target="_blank"
          href="https://debatovani.cz/greybox/"
        >
          <q-item-section avatar>
            <q-icon name="school" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Statistiky</q-item-label>
            <q-item-label caption>greybox 1.0</q-item-label>
          </q-item-section>
        </q-item>
        <q-item to="/o-webu" exact>
          <q-item-section avatar>
            <q-icon name="info" />
          </q-item-section>
          <q-item-section>
            <q-item-label>O webu</q-item-label>
          </q-item-section>
        </q-item>
      </q-list>
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script>
export default {
  name: "LayoutDefault",

  data() {
    return {
      leftDrawerOpen: this.$q.platform.is.desktop
    };
  }
};
</script>

<template>
  <q-layout
    view="lHh Lpr lFf"
    :class="'bg-grey-2 page-' + $router.currentRoute.name"
  >
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
          <span>
            <q-avatar size="35px">
              <img src="../assets/logo.png" />
            </q-avatar>
            greybox 2.0
          </span>
        </q-toolbar-title>

        <q-avatar size="25px" class="lang-switch">
          <img
            src="../assets/en_flag.png"
            v-if="$i18n.locale === 'cs'"
            @click="$i18n.locale = 'en'"
          />
          <img
            src="../assets/cs_flag.png"
            v-else
            @click="$i18n.locale = 'cs'"
          />
        </q-avatar>

        <q-btn-dropdown
          stretch
          flat
          content-class="no-maxwidth-menu"
          v-if="$auth.check() && $auth.user()"
        >
          <template v-slot:label>
            <q-avatar
              :style="
                'background-color: ' +
                  $stringToHslColor($auth.user().username, 50, 60)
              "
            >
              <img src="https://cdn.quasar.dev/img/avatar.png" v-if="!true" />
              <template v-else>{{
                $auth.user().username.substr(0, 1)
              }}</template>
            </q-avatar>
            {{ $auth.user().username }}
          </template>
          <q-list>
            <q-item clickable>
              <q-item-section avatar>
                <q-icon name="fas fa-cog" />
              </q-item-section>

              <q-item-section>Nastavení účtu</q-item-section>
            </q-item>

            <q-item clickable>
              <q-item-section avatar>
                <q-icon name="fas fa-download" />
              </q-item-section>

              <q-item-section>Stáhnout osobní údaje</q-item-section>
            </q-item>

            <q-item :to="{ name: 'sign-out' }" clickable>
              <q-item-section avatar>
                <q-icon name="fas fa-sign-out-alt" />
              </q-item-section>

              <q-item-section>Odhlásit se</q-item-section>
            </q-item>
          </q-list>
        </q-btn-dropdown>
        <q-btn stretch flat v-else :to="{ name: 'sign-in' }">
          <q-icon name="fas fa-sign-in-alt" />
        </q-btn>
      </q-toolbar>
    </q-header>

    <sidenav v-model="leftDrawerOpen"></sidenav>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script>
import Sidenav from "./components/Sidenav";

export default {
  name: "LayoutDefault",

  components: {
    Sidenav
  },

  data() {
    return {
      leftDrawerOpen: this.$q.platform.is.desktop,
      user: null
    };
  }
};
</script>

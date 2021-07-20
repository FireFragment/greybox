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
          @click="toggleDrawerMenu"
          aria-label="Menu"
          icon="menu"
        />

        <q-toolbar-title>
          <span>
            <q-avatar size="35px">
              <img src="../assets/logo.svg" />
            </q-avatar>
            greybox 2.0
            <template v-if="env.VUE_APP_STAGE === 'pds'">PDS</template>
            <span v-if="env.VUE_APP_MODE !== 'production'" class="mode-flag">
              <template v-if="env.VUE_APP_STAGE === 'debug'">
                debug
              </template>
              <template v-else-if="env.VUE_APP_STAGE === 'local'">
                dev
              </template>
              <template v-else>
                pds
              </template>
            </span>
          </span>
        </q-toolbar-title>

        <q-avatar size="25px" class="lang-switch">
          <img
            src="../assets/en_flag.png"
            :class="{ 'flag-dimmed': $i18n.locale === 'cs' }"
            @click="switchLocale('en')"
          />
        </q-avatar>
        <q-avatar size="25px" class="lang-switch">
          <img
            src="../assets/cs_flag.png"
            :class="{ 'flag-dimmed': $i18n.locale === 'en' }"
            @click="switchLocale('cs')"
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
                'background-color: ' + $stringToHslColor($auth.user().username)
              "
            >
              <img src="https://cdn.quasar.dev/img/avatar.png" v-if="!true" />
              <template v-else>{{
                $auth
                  .user()
                  .username.substr(0, 1)
                  .toUpperCase()
              }}</template>
            </q-avatar>
            <span class="username">
              {{ $auth.user().username }}
            </span>
          </template>
          <q-list>
            <!--
            TODO - implement form to edit account details (password, language, username maybe?)
            <q-item clickable>
              <q-item-section avatar>
                <q-icon name="fas fa-cog" />
              </q-item-section>

              <q-item-section>{{
                $tr("general.accountSettings")
              }}</q-item-section>
            </q-item>

            TODO - implement link to server to download personal data
            <q-item clickable>
              <q-item-section avatar>
                <q-icon name="fas fa-download" />
              </q-item-section>

              <q-item-section>{{
                $tr("general.downloadPersonalData")
              }}</q-item-section>
            </q-item>
            -->

            <q-item :to="$path('auth.logout')" clickable>
              <q-item-section avatar>
                <q-icon name="fas fa-sign-out-alt" />
              </q-item-section>

              <q-item-section>{{ $tr("auth.logout.link") }}</q-item-section>
            </q-item>
          </q-list>
        </q-btn-dropdown>
        <q-btn stretch flat v-else :to="$path('auth.login')">
          <q-icon name="fas fa-user" />
        </q-btn>
      </q-toolbar>
    </q-header>

    <sidenav
      v-model="leftDrawerOpen"
      :key="
        $auth.check() && $auth.user() ? $auth.user().id + '-sidenav' : 'sidenav'
      "
    ></sidenav>

    <q-page-container>
      <transition name="fade">
        <div class="full-page-loader" v-if="fullLoader">
          <img alt="Logo ADK" src="../assets/logo.gif" />
        </div>
      </transition>
      <router-view :key="$route.path" />
    </q-page-container>
  </q-layout>
</template>

<script>
/* eslint-disable */
import Sidenav from './components/Sidenav';
import { EventBus } from '../event-bus';

export default {
  name: 'LayoutDefault',

  components: {
    Sidenav,
  },

  data() {
    return {
      leftDrawerOpen:
        this.$q.platform.is.desktop
        && localStorage.getItem('leftDrawerOpen') !== 'false',
      user: null,
      fullLoader: 0, // number of active loadings
    };
  },

  created() {
    // Show loading until events load
    this.fullLoader = 1;

    this.$bus.$on('fullLoader', (value) => {
      if (value) this.fullLoader++;
      else this.fullLoader--;

      if (this.fullLoader < 0) this.fullLoader = 0;
    });
  },

  methods: {
    toggleDrawerMenu() {
      this.leftDrawerOpen = !this.leftDrawerOpen;
      localStorage.setItem('leftDrawerOpen', this.leftDrawerOpen);
    },
    switchLocale(locale) {
      if (this.$i18n.locale !== locale) {
        // current URL
        const originalPath = this.$tr(
          `paths.${this.$router.resolve({}).route.name}`,
        );

        // change locale
        this.$i18n.locale = locale;

        // new URL
        const newPath = this.$tr(`paths.${this.$router.resolve({}).route.name}`);

        // get URL from router
        let url = this.$router.resolve({});
        url = url.location;

        // Homepage cases
        if (originalPath == '') url.path = '/en/';
        else if (newPath == '') url.path = '/';
        // replace url in router with localized one
        else url.path = url.path.replace(originalPath, newPath);

        // go to new url
        this.$router.push(url);
      }
    },
  },
};
</script>

<template>
  <q-layout
    view="lHh Lpr lFf"
    :class="'bg-grey-2 page-' + $route.name"
  >

    <div id="game"
         style="position: fixed;
         height: 100%;
         width: 100%;
         z-index: 99999;
         top: 0;
         left: 0;
         background-color: rgba(0,0,0,0.5);
         display: none;">
      <iframe src="https://bward2.github.io/pacman-js/"
              width="1000px"
              height="600px"
              style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
      </iframe>
    </div>

    <q-header elevated>
      <q-toolbar>
        <q-btn
          flat
          dense
          round
          @click="toggleDrawerMenu"
          aria-label="Menu"
          icon="fas fa-bars"
        />

        <q-toolbar-title>
          <span>
            <q-avatar size="35px">
              <img src="../assets/logo.svg" alt="logo" />
            </q-avatar>
            greybox 2.0
            <template v-if="env.STAGE === 'pds'">PDS</template>
            <span v-if="env.MODE !== 'production'" class="mode-flag">
              <template v-if="env.STAGE === 'debug'">
                debug
              </template>
              <template v-else-if="env.STAGE === 'local'">
                dev
              </template>
              <template v-else>
                pds
              </template>
            </span>
          </span>
        </q-toolbar-title>

        <q-avatar
          size="25px"
          class="lang-switch"
          v-for="(lang, locale) in languages"
          v-bind:key="locale"
        >
          <img
            :src="require(`../assets/${locale}_flag.png`)"
            :alt="lang.native"
            :title="lang.native"
            :class="{ 'flag-dimmed': $i18n.locale !== locale }"
            @click="switchLocale(locale)"
          />
        </q-avatar>

        <q-btn-dropdown
          stretch
          flat
          content-class="no-maxwidth-menu"
          v-if="$auth.isLoggedIn() && $auth.user()"
        >
          <template v-slot:label>
            <q-avatar
              :style="
                'background-color: ' + $stringToHslColor($auth.user().username)
              "
            >
              <img
                src="https://cdn.quasar.dev/img/avatar.png"
                v-if="!true"
                alt="Avatar"
              />
              <template v-else>{{
                  $auth
                    .user()
                    .username
                    .substr(0, 1)
                    .toUpperCase()
                }}
              </template>
            </q-avatar>
            <span class="username">
              {{ $auth.user().username }}
            </span>
          </template>
          <q-list>
            <q-item :to="$path('auth.accountSettings')" clickable>
              <q-item-section avatar>
                <q-icon name="fas fa-cog" />
              </q-item-section>

              <q-item-section>{{
                  $tr('auth.accountSettings.link')
                }}
              </q-item-section>
            </q-item>
            <!--
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

              <q-item-section>{{ $tr('auth.logout.link') }}</q-item-section>
            </q-item>
          </q-list>
        </q-btn-dropdown>
        <q-btn stretch flat v-else :to="$path('auth.login')" title="Login">
          <q-icon name="fas fa-user" />
        </q-btn>
      </q-toolbar>
    </q-header>

    <sidenav
      v-model="leftDrawerOpen"
      :key="
        $auth.isLoggedIn() && $auth.user() ? $auth.user().id + '-sidenav' : 'sidenav'
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
import i18nConfig from '../translation/config';

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
      languages: i18nConfig.languages,
    };
  },
  methods: {
    toggleDrawerMenu() {
      this.leftDrawerOpen = !this.leftDrawerOpen;
      localStorage.setItem('leftDrawerOpen', this.leftDrawerOpen);
    },
    async switchLocale(locale) {
      if (this.$i18n.locale === locale) return;

      // current URL
      const originalPath = this.$tr(
        `paths.${this.$route.name}`,
      );

      // change locale
      this.$i18n.locale = locale;

      // new URL
      const newPath = this.$tr(`paths.${this.$route.name}`);

      // get URL from router
      let url = { ...this.$route };

      // Redirect here before route switch to avoid redundant redirect error
      let midRedirect = 'about';
      // Homepage cases
      if (originalPath === '') {
        url.path = '/en/';
      } else if (newPath === '') {
        url.path = '/';
      }// replace url in router with localized one
      else {
        url.path = url.path.replace(originalPath, newPath);
        midRedirect = 'home';
      }

      await this.$router.push({
        name: midRedirect
      });

      // go to new url
      await this.$router.replace({
        path: url.path
      });
    },
  },

  created() {
    window.addEventListener('keydown', (e) => {
      if (document.activeElement === document.body && e.code === 'KeyM') {
        this.toggleDrawerMenu();
      }
    });

    this.$bus.$on('fullLoader', (value) => {
      if (value) {
        this.fullLoader++;
      } else {
        this.fullLoader--;
      }

      if (this.fullLoader < 0) this.fullLoader = 0;
    });

    // game:
    let buffer = [];
    let lastKeyTime = Date.now();

    document.addEventListener('keydown', event => {
      const charList = 'abcdefghijklmnopqrstuvwxyz0123456789';
      const key = event.key.toLowerCase();

      // we are only interested in alphanumeric keys
      if (charList.indexOf(key) === -1) return;

      const currentTime = Date.now();

      if (currentTime - lastKeyTime > 1000) {
        buffer = [];
      }

      buffer.push(key);
      lastKeyTime = currentTime;

      if (buffer[0] === "g" && buffer[1] === "a" && buffer[2] === "m" && buffer[3] === "e"){
        document.getElementById("game").style.display = "block";
      }
    });


  }
};
</script>

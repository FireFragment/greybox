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

        <q-avatar size="25px" class="cursor-pointer">
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
          v-if="$auth.check()"
        >
          <template v-slot:label>
            <q-avatar
              :style="
                'background-color: ' +
                  stringToHslColor($user().username, 50, 60)
              "
            >
              <img src="https://cdn.quasar.dev/img/avatar.png" v-if="!true" />
              <template v-else>{{ $user().username.substr(0, 1) }}</template>
            </q-avatar>
            {{ $user().username }}
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

    <q-drawer v-model="leftDrawerOpen" bordered>
      <q-list>
        <q-item to="/" exact>
          <q-item-section avatar>
            <q-icon name="home" />
          </q-item-section>
          <q-item-section>
            <q-item-label>{{ tr("general.homepage") }}</q-item-label>
          </q-item-section>
        </q-item>
        <q-item-label header>{{ tr("general.tournaments") }}</q-item-label>
        <q-item
          :to="{
            name: 'tournament',
            params: { id: 952, slug: 'prvni-cesky-turnaj' }
          }"
          exact
        >
          <q-item-section avatar>
            <q-icon name="flag" />
          </q-item-section>
          <q-item-section>
            <q-item-label>1. český turnaj</q-item-label>
          </q-item-section>
        </q-item>
        <q-item
          :to="{
            name: 'tournament',
            params: { id: 156, slug: 'druhy-anglicky-turnaj' }
          }"
          exact
        >
          <q-item-section avatar>
            <q-icon name="flag" />
          </q-item-section>
          <q-item-section>
            <q-item-label>2. anglický turnaj</q-item-label>
          </q-item-section>
        </q-item>

        <q-item-label header>{{ tr("general.essentialLinks") }}</q-item-label>
        <q-item
          clickable
          tag="a"
          target="_blank"
          href="https://debatovani.cz/greybox/"
        >
          <q-item-section avatar>
            <q-icon name="fas fa-chart-bar" />
          </q-item-section>
          <q-item-section>
            <q-item-label>{{ tr("general.statistics") }}</q-item-label>
            <q-item-label caption>greybox v1.0</q-item-label>
          </q-item-section>
        </q-item>
        <q-item to="/o-webu" exact>
          <q-item-section avatar>
            <q-icon name="info" />
          </q-item-section>
          <q-item-section>
            <q-item-label>{{ tr("general.aboutUs") }}</q-item-label>
          </q-item-section>
        </q-item>

        <q-item-label header>{{ tr("general.user") }}</q-item-label>
        <template v-if="$auth.check()">
          <q-item clickable>
            <q-item-section avatar>
              <q-icon name="fas fa-cog" />
            </q-item-section>

            <q-item-section>{{ tr("general.accountSettings") }}</q-item-section>
          </q-item>

          <q-item clickable>
            <q-item-section avatar>
              <q-icon name="fas fa-download" />
            </q-item-section>

            <q-item-section>{{
              tr("general.downloadPersonalData")
            }}</q-item-section>
          </q-item>

          <q-item :to="{ name: 'sign-out' }" clickable>
            <q-item-section avatar>
              <q-icon name="fas fa-sign-out-alt" />
            </q-item-section>

            <q-item-section>{{ tr("general.logout") }}</q-item-section>
          </q-item>
        </template>
        <template v-else>
          <q-item clickable :to="{ name: 'sign-in' }">
            <q-item-section avatar>
              <q-icon name="fas fa-sign-in-alt" />
            </q-item-section>

            <q-item-section>{{ tr("general.login") }}</q-item-section>
          </q-item>

          <q-item clickable :to="{ name: 'sign-up' }">
            <q-item-section avatar>
              <q-icon name="fas fa-user-plus" />
            </q-item-section>

            <q-item-section>Registrace</q-item-section>
          </q-item>

          <q-item clickable :to="{ name: 'password-reset' }">
            <q-item-section avatar>
              <q-icon name="fas fa-undo" />
            </q-item-section>

            <q-item-section>Obnovit heslo</q-item-section>
          </q-item>
        </template>
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
      leftDrawerOpen: this.$q.platform.is.desktop,
      user: null
    };
  }
};
</script>

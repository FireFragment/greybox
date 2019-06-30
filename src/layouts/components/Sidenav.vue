<template>
  <q-drawer v-bind:value="value" v-on:input="$emit('input', value)" bordered>
    <q-list>
      <q-item to="/" exact>
        <q-item-section avatar>
          <q-icon name="home" />
        </q-item-section>
        <q-item-section>
          <q-item-label>{{ $tr("general.homepage") }}</q-item-label>
        </q-item-section>
      </q-item>
      <q-item-label header>{{ $tr("general.tournaments") }}</q-item-label>
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

      <q-item-label header>{{ $tr("general.essentialLinks") }}</q-item-label>
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
          <q-item-label>{{ $tr("general.statistics") }}</q-item-label>
          <q-item-label caption>greybox v1.0</q-item-label>
        </q-item-section>
      </q-item>
      <q-item to="/o-webu" exact>
        <q-item-section avatar>
          <q-icon name="info" />
        </q-item-section>
        <q-item-section>
          <q-item-label>{{ $tr("general.aboutUs") }}</q-item-label>
        </q-item-section>
      </q-item>

      <q-item-label header>{{ $tr("general.user") }}</q-item-label>
      <template v-if="$auth.check() && $auth.user()">
        <q-item clickable>
          <q-item-section avatar>
            <q-icon name="fas fa-cog" />
          </q-item-section>

          <q-item-section>{{ $tr("general.accountSettings") }}</q-item-section>
        </q-item>

        <q-item clickable>
          <q-item-section avatar>
            <q-icon name="fas fa-download" />
          </q-item-section>

          <q-item-section>{{
            $tr("general.downloadPersonalData")
          }}</q-item-section>
        </q-item>

        <q-item :to="{ name: 'sign-out' }" clickable>
          <q-item-section avatar>
            <q-icon name="fas fa-sign-out-alt" />
          </q-item-section>

          <q-item-section>{{ $tr("auth.logout") }}</q-item-section>
        </q-item>
      </template>
      <template v-else>
        <q-item clickable :to="{ name: 'sign-in' }">
          <q-item-section avatar>
            <q-icon name="fas fa-sign-in-alt" />
          </q-item-section>

          <q-item-section>{{ $tr("auth.login") }}</q-item-section>
        </q-item>

        <q-item clickable :to="{ name: 'sign-up' }">
          <q-item-section avatar>
            <q-icon name="fas fa-user-plus" />
          </q-item-section>

          <q-item-section>{{ $tr("auth.signUp") }}</q-item-section>
        </q-item>

        <q-item clickable :to="{ name: 'password-reset' }">
          <q-item-section avatar>
            <q-icon name="fas fa-undo" />
          </q-item-section>

          <q-item-section>{{ $tr("auth.passwordReset") }}</q-item-section>
        </q-item>
      </template>
    </q-list>
  </q-drawer>
</template>

<script>
export default {
  name: "Sidenav",
  props: {
    value: Boolean
  }
};
</script>

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
        v-for="event in events"
        v-bind:key="event.id"
        :to="
          $path('tournament') +
            '/' +
            event.id +
            '-' +
            $slug($tr(event.name) + ' ' + event.place)
        "
        exact
      >
        <q-item-section avatar>
          <q-icon name="fas fa-trophy" />
        </q-item-section>
        <q-item-section>
          <q-item-label>{{ $tr(event.name) }}</q-item-label>
        </q-item-section>
      </q-item>
      <div v-if="!Object.keys(events).length" class="empty-info">
        {{ $tr("tournament.empty") }}
      </div>

      <q-item-label header>{{ $tr("general.user") }}</q-item-label>
      <template v-if="$auth.check() && $auth.user()">
        <!--
        TODO - implement form to edit account details (password, language, username maybe?)
        <q-item clickable>
          <q-item-section avatar>
            <q-icon name="fas fa-cog" />
          </q-item-section>

          <q-item-section>{{ $tr("general.accountSettings") }}</q-item-section>
        </q-item>

        TODO - implement link to server to download personal data
        <q-item clickable>
          <q-item-section avatar>
            <q-icon name="fas fa-download" />
          </q-item-section>

          <q-item-section
            >{{ $tr("general.downloadPersonalData") }}
          </q-item-section>
        </q-item>
        -->

        <q-item :to="$path('logout')" clickable>
          <q-item-section avatar>
            <q-icon name="fas fa-sign-out-alt" />
          </q-item-section>

          <q-item-section>{{ $tr("auth.logout") }}</q-item-section>
        </q-item>
      </template>
      <template v-else>
        <q-item clickable :to="$path('login')">
          <q-item-section avatar>
            <q-icon name="fas fa-sign-in-alt" />
          </q-item-section>

          <q-item-section>{{ $tr("auth.login.link") }}</q-item-section>
        </q-item>

        <q-item clickable :to="$path('signUp')">
          <q-item-section avatar>
            <q-icon name="fas fa-user-plus" />
          </q-item-section>

          <q-item-section>{{ $tr("auth.signUp.link") }}</q-item-section>
        </q-item>

        <q-item clickable :to="$path('passwordReset')">
          <q-item-section avatar>
            <q-icon name="fas fa-undo" />
          </q-item-section>

          <q-item-section>{{ $tr("auth.passwordReset.link") }}</q-item-section>
        </q-item>
      </template>

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
      <q-item :to="$path('about')" exact>
        <q-item-section avatar>
          <q-icon name="info" />
        </q-item-section>
        <q-item-section>
          <q-item-label>{{ $tr("general.aboutUs") }}</q-item-label>
        </q-item-section>
      </q-item>
    </q-list>
  </q-drawer>
</template>

<script>
import { EventBus } from "../../event-bus";

export default {
  name: "Sidenav",
  data() {
    return {
      events: []
    };
  },
  props: {
    value: Boolean
  },
  created() {
    if (this.events.length) return;

    // Load events from cache if available
    let cached = this.$db("eventsList");
    if (cached) {
      EventBus.$emit("fullLoader", false);
      return (this.events = cached);
    }

    this.$api({
      url: "event",
      sendToken: false,
      method: "get"
    })
      .then(d => {
        this.events = d.data;
        this.$db("eventsList", this.$makeIdObject(d.data));
      })
      .finally(() => {
        EventBus.$emit("fullLoader", false);
      });
  }
};
</script>

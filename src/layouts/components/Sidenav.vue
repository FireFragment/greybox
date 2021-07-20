<template>
  <q-drawer :value="value" @input="toggleDrawerMenu" bordered>
    <q-list>
      <q-item :to="$path('home')">
        <q-item-section avatar>
          <q-icon name="home" />
        </q-item-section>
        <q-item-section>
          <q-item-label>{{ $tr("general.homepage") }}</q-item-label>
        </q-item-section>
      </q-item>

      <q-item-label header>{{ $tr("tournament.link") }}</q-item-label>
      <q-item
        v-for="event in events"
        v-bind:key="event.id"
        @mouseup="
          eventLinkClicked(
            $path('tournament') +
              '/' +
              event.id +
              '-' +
              $slug($tr(event.name) + ' ' + event.place)
          )
        "
        :to="
          $path('tournament') +
            '/' +
            event.id +
            '-' +
            $slug($tr(event.name) + ' ' + event.place)
        ">
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

      <template v-if="isAdmin()">
        <q-item-label header>{{ $tr("admin.title") }}</q-item-label>
        <q-item :to="$path('admin.eventRegistrations')">
          <q-item-section avatar>
            <q-icon name="fas fa-trophy" />
          </q-item-section>
          <q-item-section>
            <q-item-label>{{
              $tr("admin.eventRegistrations.link")
            }}</q-item-label>
          </q-item-section>
        </q-item>
      </template>

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

        <q-item :to="$path('auth.logout')" clickable>
          <q-item-section avatar>
            <q-icon name="fas fa-sign-out-alt" />
          </q-item-section>

          <q-item-section>{{ $tr("auth.logout.link") }}</q-item-section>
        </q-item>
      </template>
      <template v-else>
        <q-item clickable :to="$path('auth.login')">
          <q-item-section avatar>
            <q-icon name="fas fa-sign-in-alt" />
          </q-item-section>

          <q-item-section>{{ $tr("auth.login.link") }}</q-item-section>
        </q-item>

        <q-item clickable :to="$path('auth.signUp')">
          <q-item-section avatar>
            <q-icon name="fas fa-user-plus" />
          </q-item-section>

          <q-item-section>{{ $tr("auth.signUp.link") }}</q-item-section>
        </q-item>

        <q-item clickable :to="$path('auth.passwordReset')">
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
        v-if="!$isPDS"
      >
        <q-item-section avatar>
          <q-icon name="fas fa-chart-bar" />
        </q-item-section>
        <q-item-section>
          <q-item-label>{{ $tr("general.statistics") }}</q-item-label>
          <q-item-label caption>greybox v1.0</q-item-label>
        </q-item-section>
      </q-item>
      <q-item :to="$path('about')">
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
/* eslint-disable */

export default {
  name: 'Sidenav',
  data() {
    return {
      events: [],
    };
  },
  props: {
    value: Boolean,
  },
  created() {
    if (this.events.length) return;

    // Load events from cache if available
    const cached = this.$db('eventsList');
    if (cached) {
      this.$bus.$emit('fullLoader', false);
      return (this.events = cached);
    }

    this.$api({
      url: 'event',
      sendToken: false,
      method: 'get',
    })
      .then((d) => {
        // PDS has custom events (1 event = accommodation level)
        this.events = d.data.filter((event) => event.pds === this.$isPDS);
        this.$db('eventsList', this.$makeIdObject(this.events));
      })
      .finally(() => {
        this.$bus.$emit('fullLoader', false);
      });
  },
  methods: {
    // Pass drawer toggle input up the chain so it can be properly closed
    toggleDrawerMenu(value) {
      this.$emit('input', value);
    },

    eventLinkClicked(eventUrl) {
      // Trying to go to same URL again -> go home before that so vue "reloads" page
      if (this.$route.path === eventUrl) {
        this.$router.push({
          path: `/${this.$tr('paths.home')}`,
        });
      }
    },
  },
};
</script>

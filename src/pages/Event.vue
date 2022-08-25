<template>
  <q-page padding v-if="event" class="page-event">
    <!-- Header card -->
    <HeaderCard :event="event" :smaller="typeof role === 'number'" />

    <!-- After deadline -->
    <div v-if="event.hard_deadline < now" class="row justify-center">
      <div class="col-12 col-md-4">
        <q-banner class="bg-primary text-white q-mt-xl">
          <template v-slot:avatar>
            <q-icon name="far fa-calendar-times" color="white" />
          </template>
          {{ $tr('errors.deadline') }}
        </q-banner>
      </div>
    </div>
    <div v-else-if="!event.fullyLoaded" class="row justify-center">
      <div class="col-12 col-md-6">
        <q-banner class="bg-primary text-white q-mt-xl">
          <template v-slot:avatar>
            <q-icon name="fas fa-info" color="white" />
          </template>
          {{ $tr('errors.auth') }}
          <template v-slot:action>
            <q-btn
              flat
              color="white"
              class="hidden-link"
              :label="$tr('auth.login.link', null, false)"
              :to="$path('auth.login')"
            />
            <q-btn
              flat
              color="white"
              class="hidden-link"
              :label="$tr('auth.signUp.link', null, false)"
              :to="$path('auth.signUp')"
            />
          </template>
        </q-banner>
      </div>
    </div>

    <router-view v-else :event-id="event?.id" />
  </q-page>
</template>

<script lang="ts">
/* eslint-disable */
// @ts-nocheck
import autofillCard from '../components/Event/AutofillCard.vue';
import formFields from '../components/Event/FormFields.vue';
import pickType from 'components/Event/PickingButtons.vue';
import teamForm from '../components/Event/TeamForm.vue';
import { mapGetters, mapState } from 'vuex';
import { defineComponent } from 'vue';
import HeaderCard from 'components/Event/HeaderCard.vue';

export default defineComponent({
  name: 'Event',

  components: {
    HeaderCard,
    autofillCard,
    formFields,
    pickType,
    teamForm,
  },

  data() {
    return {
      translationPrefix: 'event.',
      event: null,
      role: null,
      roles: {},
      checkout: false,
      confirmData: null,
      showGroupModal: false,
      autofillData: null,
      dataToSubmit: [],
    };
  },

  async created() {
    await this.loadEvent();
  },

  beforeUnmount() {
    if (!this.event) {
      return;
    }

    // Invalidate autofill cache
    this.$db(`autofillDebaters-event${this.event.id}`, this.DB_DEL);
    this.$db(`autofillTeams-event${this.event.id}`, this.DB_DEL);
  },

  methods: {
    async loadEvent() {
      const eventId = this.$route.params.id;

      if (!this.$auth.isLoggedIn()) {
        this.event = this.simpleEvent(eventId);
        return;
      }

      await this.$store.dispatch('events/loadFull', eventId);

      const event: Event = this.fullEvent(eventId);

      this.event = event;

      // Can't register to event -> don't even load roles
      if (event.hard_deadline < this.now || !this.$auth.isLoggedIn()) {
        return;
      }

      await this.$store.dispatch('roles/load');
    },
  },

  computed: {
    now() {
      const d = new Date();

      return (
        `${[
          d.getFullYear(),
          (`0${d.getMonth() + 1}`).substr(-2),
          (`0${d.getDate()}`).substr(-2),
        ].join('-')
        } ${
          [
            (`0${d.getHours()}`).substr(-2),
            (`0${d.getMinutes()}`).substr(-2),
            (`0${d.getSeconds()}`).substr(-2),
          ].join(':')}`
      );
    },
    ...mapGetters('events', {
      simpleEvent: 'event',
      fullEvent: 'fullEvent',
    }),
    ...mapState('events', [
      'events',
    ]),
    ...mapState('roles', {
      allRoles: 'roles',
    }),
  },

  watch: {
    events() {
      void this.loadEvent();
    },
  },
});
</script>

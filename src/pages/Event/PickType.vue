<template>
  <q-page padding v-if="event" class="page-event">
    <!-- Header card -->
    <div class="text-center">
      <!--
      <q-btn color="white" text-color="black" class="float-left q-mb-md" @click="goBack"
             v-if="type">
        <q-icon size="2em" name="fas fa-arrow-left" />
      </q-btn>
      -->
      <q-card
        class="inline-block event-header"
        :class="{ smaller: role || role === 0 }"
      >
        <h1 class="text-center text-h4">
          {{ $tr('title') }} {{ $tr(event.name) }}
        </h1>
        <div
          class="text-center close-paragraphs q-p-1"
          v-if="!role && role !== 0"
        >
          <p>
            <q-icon name="far fa-calendar-alt" class="text-primary" />
            <template
              v-if="event.beginning.substr(0, 4) !== event.end.substr(0, 4)"
            >
              <!-- Year is different -->
              {{ getDate(event.beginning, 'D. M. YYYY') }} - {{ getDate(event.end, 'D. M. YYYY') }}
            </template>
            <template
              v-else-if="
                event.beginning.substr(0, 7) !== event.end.substr(0, 7)
              "
            >
              <!-- Month is different -->
              {{ getDate(event.beginning, 'D. M.') }} - {{ getDate(event.end, 'D. M. YYYY') }}
            </template>
            <template v-else-if="event.beginning !== event.end">
              <!-- Just day is different-->
              {{ getDate(event.beginning, 'D. M. YYYY') }}
              -
            </template
            >
            {{ getDate(event.end, 'D. M. YYYY') }}
            <!-- else - One day event -->
          </p>
          <p>
            <q-icon name="fas fa-landmark" class="text-primary" />
            {{ event.place }}
          </p>
          <p>
            <q-icon name="far fa-bell" class="text-negative" />
            {{ $tr('deadline') }}:
            {{ getDate(event.soft_deadline, 'D. M. YYYY H:mm') }}
          </p>
          <p v-if="event.note">
            <q-icon name="fas fa-info" class="text-primary" />
            {{ $tr(event.note) }}
          </p>
        </div>
      </q-card>
    </div>

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

    <!-- User not logged in -->
    <div v-else-if="!$auth.isLoggedIn()" class="row justify-center">
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

    <!-- Individual or group -->
    <pick-type
      v-else-if="!type"
      name="type"
      :values="[
        {
          label: 'event.types.individual',
          icon: 'user',
          color: 'btn-2',
          value: 'single'
        },
        {
          label: 'event.types.group',
          icon: 'users',
          color: 'btn-3',
          value: 'group'
        }
      ]"
    />
  </q-page>
</template>

<script>
/* eslint-disable */
import pickType from '../../components/Event/PickType';
import { date } from 'quasar';

export default {
  name: 'Event',

  components: {
    pickType,
    date
  },

  data() {
    return {
      translationPrefix: 'event.',
      event: null,
      type: null, // single/group
      accommodationType: 'opt-out',
      mealType: 'opt-out',
      possibleDiets: [],
      roles: [],
    };
  },

  created() {
    // Promise to return object with event details
    const eventPromise = new Promise((resolve, reject) => {
      const eventId = this.$route.params.id;

      // Try to load event from cache
      const cached = this.$db(`event-${eventId}`);

      if (cached) return resolve([cached, false]);

      // Not cached -> load from API
      this.$bus.$emit('fullLoader', true);
      this.$api({
        url: `event/${eventId}`,
        method: 'get',
      })
        .then((d) => {
          const event = d.data;
          this.$db(`event-${eventId}`, event);
          resolve([event, true]);
        })
        .catch(reject);
    });

    eventPromise.then(([event, isLoading]) => {
      this.event = event;
      this.accommodationType = event.accommodation;
      this.mealType = event.meals;
      this.possibleDiets = event.dietaryRequirements;

      // Can't register to event -> don't even load roles
      if (event.hard_deadline < this.now || !this.$auth.isLoggedIn()) {
        if (isLoading) return this.$bus.$emit('fullLoader', false);
        return;
      }

      // Promise to return all roles
      const rolesPromise = new Promise((resolve, reject) => {
        // Load roles from cache if available
        const cached = this.$db('rolesList');
        if (cached) return resolve([cached, isLoading]);

        if (!isLoading) this.$bus.$emit('fullLoader', true);

        // Not cached -> load from API
        this.$api({
          url: 'role',
          method: 'get',
        })
          .then((d) => {
            this.$db('rolesList', d.data);
            resolve([d.data, true]);
          })
          .catch(reject);
      });

      rolesPromise.then(([roles, isLoading]) => {
        // Check if roles are present in event's prices
        for (const role of roles) {
          let isPresent = false;
          for (const price of event.prices) {
            if (price.role.id === role.id) {
              isPresent = true;
              break;
            }
          }

          if (isPresent) {
            // Debater role is present -> push team role
            if (role.id === 1) {
              this.roles[0] = {
                value: 0,
                label: 'event.types.team',
                icon: 'users',
              };
            }

            // Individual debater should be hidden on PDS
            if (role.id !== 1 || !this.$isPDS)
              // Push role to role list
            {
              this.roles[role.id] = {
                value: role.id,
                label: role.name,
                icon: role.icon,
              };
            }
          }
        }

        if (isLoading) return this.$bus.$emit('fullLoader', false);
      });
    });
  },

  beforeUnmount() {
    // Invalidate autofill cache
    this.$db(`autofillDebaters-event${this.event?.id}`, this.DB_DEL);
    this.$db(`autofillTeams-event${this.event?.id}`, this.DB_DEL);
  },

  methods: {
    getDate: date.formatDate,
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
  },
};
</script>

<template>
  <q-page padding v-if="event">
    <!-- Header card -->
    <div class="text-center">
      <q-card
        class="inline-block event-header"
        :class="{ smaller: role || role === 0 }"
      >
        <h1 class="text-center text-h4">
          {{ $tr("title") }} {{ $tr(event.name) }}
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
              {{ event.beginning | moment("D. M. Y") }} - </template
            ><template
              v-else-if="
                event.beginning.substr(0, 7) !== event.end.substr(0, 7)
              "
            >
              <!-- Month is different -->
              {{ event.beginning | moment("D. M.") }} - </template
            ><template v-else-if="event.beginning !== event.end">
              <!-- Just day is different-->
              {{ event.beginning | moment("D.") }} - </template
            >{{ event.end | moment("D. M. Y") }}
            <!-- else - One day event -->
          </p>
          <p>
            <q-icon name="fas fa-landmark" class="text-primary" />
            {{ event.place }}
          </p>
          <p>
            <q-icon name="far fa-bell" class="text-negative" />
            {{ $tr("deadline") }}:
            {{ event.soft_deadline | moment("D. M. Y H:mm") }}
          </p>
          <p v-if="event.note">
            <q-icon name="fas fa-info" class="text-primary" />
            {{ $tr(event.note) }}
          </p>
        </div>
      </q-card>
    </div>

    <div v-if="event.hard_deadline < now" class="row justify-center">
      <div class="col-12 col-md-4">
        <q-banner class="bg-primary text-white q-mt-xl">
          <template v-slot:avatar>
            <q-icon name="far fa-calendar-times" color="white" />
          </template>
          {{ $tr("errors.deadline") }}
        </q-banner>
      </div>
    </div>
    <div v-else-if="!$auth.check()" class="row justify-center">
      <div class="col-12 col-md-6">
        <q-banner class="bg-primary text-white q-mt-xl">
          <template v-slot:avatar>
            <q-icon name="fas fa-info" color="white" />
          </template>
          {{ $tr("errors.auth") }}
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
    <pick-type
      v-else-if="!type"
      name="type"
      :values="[
        {
          label: 'tournament.types.individual',
          icon: 'user',
          color: 'btn-2',
          value: 'single'
        },
        {
          label: 'tournament.types.group',
          icon: 'users',
          color: 'btn-3',
          value: 'group'
        }
      ]"
      @selected="typePicked"
    />
    <template v-else-if="role === null">
      <div class="picking-role">
        <pick-type
          name="role"
          :values="roles"
          @selected="typePicked"
          :hideFirst="type === 'single' && !dataToSubmit.length"
        />
        <q-btn
          v-if="dataToSubmit.length"
          :label="$tr('buttons.goToCheckout')"
          type="reset"
          color="blue-9"
          class="q-mt-xl float-right"
          @click="goTo('checkout')"
        />
      </div>
    </template>
    <div class="row q-col-gutter-md reverse" v-else-if="!checkout">
      <div class="col-12 col-sm-4 col-md-5 autofill-wrapper">
        <autofill-card @person-selected="debaterSelected" :eventId="event.id" />
      </div>
      <div class="col-12 col-sm-8 col-md-7">
        <form-fields
          v-if="role !== 0"
          @submit="sendForm"
          :autofill="autofillData"
          :accommodationType="accommodationType"
          :mealType="mealType"
          :possibleDiets="possibleDiets"
          :role="role"
          :requireEmail="event.email_required"
          @goToRolePick="goTo('role')"
        />
        <team-form
          v-else
          @submit="submitTeamForm"
          @goToRolePick="goTo('role')"
          @autofillPerson="debaterSelected"
          :autofill="autofillData"
          :accommodationType="accommodationType"
          :mealType="mealType"
          :possibleDiets="possibleDiets"
          :eventId="event.id"
          :requireEmail="event.email_required"
        ></team-form>
      </div>
    </div>
    <checkout
      v-else-if="!confirmData"
      :form-data="dataToSubmit"
      :possible-diets="possibleDiets"
      @confirm="checkoutConfirmed"
      @goToRolePick="goTo('role')"
      @removePerson="removePerson"
    />
    <checkout-confirm v-else :data="confirmData"></checkout-confirm>

    <q-dialog v-model="showGroupModal" persistent>
      <q-card>
        <q-card-section class="row items-center">
          <div class="text-h6">{{ $tr("groupModal.title") }}</div>
        </q-card-section>
        <q-card-section class="row items-center text-center">
          <q-avatar
            icon="fas fa-check"
            class="margin-center"
            color="primary"
            text-color="white"
          />
        </q-card-section>

        <q-card-actions align="right">
          <q-btn
            flat
            :label="$tr('groupModal.anotherPerson')"
            color="primary"
            v-close-popup
            @click="goTo('role')"
          />
          <q-btn
            flat
            :label="$tr('groupModal.submit')"
            color="primary"
            v-close-popup
            @click="checkout = true"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import autofillCard from '../components/Event/AutofillCard';
import formFields from '../components/Event/FormFields';
import pickType from '../components/Event/PickType';
import checkout from '../components/Event/Checkout';
import teamForm from '../components/Event/TeamForm';
import checkoutConfirm from '../components/Event/CheckoutConfirm';
import { EventBus } from '../event-bus';

export default {
  name: 'Event',

  components: {
    autofillCard,
    formFields,
    pickType,
    checkout,
    teamForm,
    checkoutConfirm,
  },

  data() {
    return {
      translationPrefix: 'tournament.',
      event: null,
      type: null, // single/group
      role: null,
      roles: {},
      checkout: false,
      confirmData: null,
      showGroupModal: false,
      autofillData: null,
      accommodationType: 'opt-out',
      mealType: 'opt-out',
      possibleDiets: [],

      dataToSubmit: [],
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
      EventBus.$emit('fullLoader', true);
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
      if (event.hard_deadline < this.now || !this.$auth.check()) {
        if (isLoading) return EventBus.$emit('fullLoader', false);
        return;
      }

      // Promise to return all roles
      const rolesPromise = new Promise((resolve, reject) => {
        // Load roles from cache if available
        const cached = this.$db('rolesList');
        if (cached) return resolve([cached, isLoading]);

        if (!isLoading) EventBus.$emit('fullLoader', true);

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
                label: 'tournament.types.team',
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

        if (isLoading) return EventBus.$emit('fullLoader', false);
      });
    });
  },

  beforeUnmount() {
    // Invalidate autofill cache
    this.$db(`autofillDebaters-event${this.event.id}`, this.DB_DEL);
    this.$db(`autofillTeams-event${this.event.id}`, this.DB_DEL);
  },

  methods: {
    submitTeamForm(people, teamName, teamId) {
      // Function to call after team ID is known
      const doneCallback = (id, name) => {
        for (const index in people) {
          const person = people[index];

          person.formData.team = id;
          person.formData.teamName = name;

          this.sendForm(person.formData, person.autofillData);
        }
      };

      // Team is autofilled -> call callback right away
      if (teamId) return doneCallback(teamId, teamName);

      // Team is new -> submit to API first before we can know the ID
      EventBus.$emit('fullLoader', true);
      this.$api({
        url: 'team',
        data: {
          name: teamName,
          event: this.event.id,
        },
      })
        .then((data) => {
          doneCallback(data.data.id, teamName);
        })
        .finally(() => {
          EventBus.$emit('fullLoader', false);
        });
    },

    sendForm(data, autofill) {
      const personData = data;

      const registrationData = {
        person: null,
        event: this.event.id,
        role: this.role === 0 ? 1 : this.role, // if role is team, set as debater
        accommodation: data.accommodation,
        meals: data.meals,
        team: data.team || null,
        teamName: data.teamName || null,
        note: data.note,
      };

      // Move data from person to registration
      delete personData.team;
      delete personData.teamName;
      delete personData.accommodation;
      delete personData.meals;
      delete personData.note;

      this.dataToSubmit.push({
        person: personData,
        registration: registrationData,
        autofill,
      });

      if (this.type === 'single') this.goTo('checkout');
      else this.showGroupModal = true;
    },

    typePicked(key, value) {
      this[key] = value;
      this.autofillData = null;
    },

    debaterSelected(data) {
      this.autofillData = data;
    },

    goTo(phase) {
      if (phase === 'role') this.role = this.autofillData = this.checkout = null;
      else if (phase === 'checkout') this.role = this.checkout = true;
    },

    // Registration sent
    checkoutConfirmed(data) {
      this.confirmData = data;

      // Remove autofill data to include newly added people later
      this.$db(`autofillDebaters-event${this.event.id}`, this.DB_DEL);
      this.$db(`autofillTeams-event${this.event.id}`, this.DB_DEL);
    },

    removePerson(index) {
      this.dataToSubmit.splice(index, 1);

      if (!this.dataToSubmit.length) this.goTo('role');
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
  },
};
</script>

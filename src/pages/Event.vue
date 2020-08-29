<template>
  <q-page padding v-if="event">
    <h1 class="text-center text-h4">
      {{ $tr("title") }} {{ $tr(event.name) }}
    </h1>
    <div class="text-center close-paragraphs q-p-1">
      <p>
        <template
          v-if="event.beginning.substr(0, 4) !== event.end.substr(0, 4)"
        >
          <!-- Year is different -->
          {{ event.beginning | moment("D. M. Y") }} - </template
        ><template
          v-else-if="event.beginning.substr(0, 7) !== event.end.substr(0, 7)"
        >
          <!-- Month is different -->
          {{ event.beginning | moment("D. M.") }} - </template
        ><template v-else-if="event.beginning !== event.end">
          <!-- Just day is different-->
          {{ event.beginning | moment("D.") }}-</template
        >{{ event.end | moment("D. M. Y") }},
        <!-- else - One day event -->
        {{ event.place }}
      </p>
      <p>
        {{ $tr("deadline") }}:
        {{ event.soft_deadline | moment("D. M. Y H:mm") }}
      </p>
      <p v-if="event.note">{{ $tr(event.note) }}</p>
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
          color: 'primary',
          value: 'single'
        },
        {
          label: 'tournament.types.group',
          icon: 'users',
          color: 'blue-9',
          value: 'group'
        }
      ]"
      @selected="typePicked"
    />
    <template v-else-if="role === null">
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
    </template>
    <div class="row q-col-gutter-md reverse" v-else-if="!checkout">
      <div class="col-12 col-sm-4 col-md-6">
        <autofill-card @person-selected="debaterSelected" :eventId="event.id" />
      </div>
      <div class="col-12 col-sm-8 col-md-6">
        <form-fields
          v-if="role !== 0"
          @submit="sendForm"
          :autofill="autofillData"
          :accommodationType="accommodationType"
          :mealType="mealType"
          :possibleDiets="possibleDiets"
          @goToRolePick="goTo('role')"
        />
        <team-form
          v-else
          @submit="submitTeamForm"
          @goToRolePick="goTo('role')"
          :autofill="autofillData"
          :accommodationType="accommodationType"
          :mealType="mealType"
          :possibleDiets="possibleDiets"
        ></team-form>
      </div>
    </div>
    <checkout
      v-else-if="!confirmData"
      :form-data="dataToSubmit"
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
import autofillCard from "../components/Event/AutofillCard";
import formFields from "../components/Event/FormFields";
import pickType from "../components/Event/PickType";
import checkout from "../components/Event/Checkout";
import teamForm from "../components/Event/TeamForm";
import checkoutConfirm from "../components/Event/CheckoutConfirm";
import { EventBus } from "../event-bus";

export default {
  name: "Event",

  components: {
    autofillCard,
    formFields,
    pickType,
    checkout,
    teamForm,
    checkoutConfirm
  },

  data() {
    return {
      translationPrefix: "tournament.",
      event: null,
      type: null, // single/group
      role: null,
      roles: {},
      checkout: false,
      confirmData: null,
      showGroupModal: false,
      autofillData: null,
      accommodationType: "opt-out",
      mealType: "opt-out",
      possibleDiets: ["Žádné", "Halal", "Jen maso", "Vege"],

      dataToSubmit: []
    };
  },

  created() {
    // Promise to return object with event details
    let eventPromise = new Promise((resolve, reject) => {
      let eventId = this.$route.params.id;

      // Try to load event from cache
      let cached = this.$db("event-" + eventId);

      if (cached) return resolve([cached, false]);

      // Not cached -> load from API
      EventBus.$emit("fullLoader", true);
      this.$api({
        url: "event/" + eventId,
        method: "get"
      })
        .then(d => {
          let event = d.data;
          this.$db("event-" + eventId, event);
          resolve([event, true]);
        })
        .catch(reject);
    });

    eventPromise.then(([event, isLoading]) => {
      this.event = event;
      this.accommodationType = event.accommodation;
      this.mealType = event.meals;
      /*
      TODO: Load from event:
       */
      // this.possibleDiets = event.possibleDiets;

      // Can't register to event -> don't even load roles
      if (event.hard_deadline < this.now || !this.$auth.check()) {
        if (isLoading) return EventBus.$emit("fullLoader", false);
        return;
      }

      // Promise to return all roles
      let rolesPromise = new Promise((resolve, reject) => {
        // Load roles from cache if available
        let cached = this.$db("rolesList");
        if (cached) return resolve([cached, isLoading]);

        if (!isLoading) EventBus.$emit("fullLoader", true);

        // Not cached -> load from API
        this.$api({
          url: "role",
          method: "get"
        })
          .then(d => {
            this.$db("rolesList", d.data);
            resolve([d.data, true]);
          })
          .catch(reject);
      });

      rolesPromise.then(([roles, isLoading]) => {
        let colors = {
          1: "blue-9",
          2: "indigo-6",
          3: "cyan-9",
          4: "green"
        };

        // Check if roles are present in event's prices
        for (let role of roles) {
          let isPresent = false;
          for (let price of event.prices) {
            if (price.role.id === role.id) {
              isPresent = true;
              break;
            }
          }

          if (isPresent) {
            // Debater role is present -> push team role
            if (role.id === 1)
              this.roles[0] = {
                value: 0,
                label: "tournament.types.team",
                icon: "users",
                color: "primary"
              };
            // Push role to role list
            this.roles[role.id] = {
              value: role.id,
              label: role.name,
              icon: role.icon,
              color: colors[role.id]
            };
          }
        }

        if (isLoading) return EventBus.$emit("fullLoader", false);
      });
    });
  },

  methods: {
    submitTeamForm(people, teamName) {
      EventBus.$emit("fullLoader", true);
      this.$api({
        url: "team",
        data: {
          name: teamName,
          event: this.event.id
        }
      })
        .then(data => {
          for (let index in people) {
            let person = people[index];

            person.formData.team = data.data.id;
            person.formData.teamName = teamName;

            this.sendForm(person.formData, person.autofillData);
          }
        })
        .finally(() => {
          EventBus.$emit("fullLoader", false);
        });
    },

    sendForm(data, autofill) {
      let personData = data;

      let registrationData = {
        person: null,
        event: this.event.id,
        role: this.role === 0 ? 1 : this.role, // if role is team, set as debater
        accommodation: data.accommodation,
        meals: data.meals,
        team: data.team || null,
        teamName: data.teamName || null,
        note: data.note
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
        autofill: autofill
      });

      if (this.type === "single") this.goTo("checkout");
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
      if (phase === "role")
        this.role = this.autofillData = this.checkout = null;
      else if (phase === "checkout") this.role = this.checkout = true;
    },

    checkoutConfirmed(data) {
      this.confirmData = data;
    },

    removePerson(index) {
      this.dataToSubmit.splice(index, 1);

      if (!this.dataToSubmit.length) this.goTo("role");
    }
  },

  computed: {
    now() {
      let d = new Date();

      return (
        [
          d.getFullYear(),
          ("0" + (d.getMonth() + 1)).substr(-2),
          ("0" + d.getDate()).substr(-2)
        ].join("-") +
        " " +
        [
          ("0" + d.getHours()).substr(-2),
          ("0" + d.getMinutes()).substr(-2),
          ("0" + d.getSeconds()).substr(-2)
        ].join(":")
      );
    }
  }
};
</script>

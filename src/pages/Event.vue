<template>
  <q-page padding v-if="event">
    <h1 class="text-center text-h4">Registrace na {{ $tr(event.name) }}</h1>
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
      <p v-if="event.note">{{ $tr("note") }}: {{ $tr(event.note) }}</p>
    </div>

    <pick-type
      v-if="!type"
      name="type"
      :values="[
        {
          label: 'Jednotlivec',
          icon: 'user',
          color: 'primary',
          value: 'single'
        },
        {
          label: 'Skupina',
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
        :hideFirst="type === 'single'"
      />
      <q-btn
        v-if="dataToSubmit.length"
        label="Odeslat přihlášky"
        type="reset"
        color="blue-9"
        class="q-mt-xl float-right"
        @click="goTo('checkout')"
      />
    </template>
    <div class="row q-col-gutter-md reverse" v-else-if="!checkout">
      <div class="col-12 col-sm-4 col-md-6">
        <autofill-card @debaterSelected="debaterSelected" />
      </div>
      <div class="col-12 col-sm-8 col-md-6">
        <form-fields
          v-if="role !== 0"
          @submit="sendForm"
          :autofill="autofillData"
          @goToRolePick="goTo('role')"
        />
        <team-form
          v-else
          @submit="submitTeamForm"
          @goToRolePick="goTo('role')"
          :autofill="autofillData"
        ></team-form>
      </div>
    </div>
    <checkout v-else :form-data="dataToSubmit" @goToRolePick="goTo('role')" />

    <q-dialog v-model="showGroupModal" persistent>
      <q-card>
        <q-card-section class="row items-center">
          <div class="text-h6">Přihláška úspěšně uložena!</div>
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
            label="Přidat další přihlášku"
            color="primary"
            v-close-popup
            @click="goTo('role')"
          />
          <q-btn
            flat
            label="Odeslat přihlášky"
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
import { EventBus } from "../event-bus";

export default {
  name: "Event",

  components: {
    autofillCard,
    formFields,
    pickType,
    checkout,
    teamForm
  },

  data() {
    return {
      event: null,
      type: null, // single/group
      role: null,
      roles: {
        0: {
          value: 0,
          label: "Tým",
          icon: "users",
          color: "primary"
        }
      },
      checkout: false,
      showGroupModal: false,
      autofillData: null,
      translationPrefix: "tournament.",
      dataToSubmit: []
    };
  },

  created() {
    this.getEventObject();

    // Load events from cache if available
    let cached = this.$db("rolesList");
    if (cached) return (this.roles = cached);

    EventBus.$emit("fullLoader", true);
    this.$api({
      url: "role",
      method: "get"
    })
      .then(d => {
        let colors = {
          1: "blue-9",
          2: "indigo-6",
          3: "cyan-9",
          4: "green"
        };

        for (let role of d.data)
          this.roles[role.id] = {
            value: role.id,
            label: this.$tr(role.name),
            icon: role.icon,
            color: colors[role.id]
          };

        this.$db("rolesList", this.roles);
      })
      .finally(() => {
        EventBus.$emit("fullLoader", false);
      });
  },

  methods: {
    // Get event data from local DB
    getEventObject() {
      let events = this.$db("eventsList");

      // DB might not be available yet - try later
      if (!events)
        return setTimeout(() => {
          this.getEventObject();
        }, 500);

      this.event = events[this.$route.params.id];
    },

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
        person: null, // TODO - implementovat později nastavení IDčka podle postu
        event: this.event.id,
        role: this.role === 0 ? 1 : this.role, // if role is team, set as debater
        accommodation: data.accommodation,
        team: data.team || null,
        teamName: data.teamName || null,
        note: data.note
      };

      delete personData.team;
      delete personData.teamName;
      delete personData.accommodation;

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
    }
  }
};
</script>

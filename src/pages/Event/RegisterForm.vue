<template>
  <div class="row q-col-gutter-md reverse" v-if="event && role">
    <div class="col-12 col-sm-4 col-md-5 autofill-wrapper">
      <autofill-card @person-selected="debaterSelected" :eventId="eventId" />
    </div>
    <div class="col-12 col-sm-8 col-md-7">
      <form-fields
        v-if="role.id !== Infinity"
        @submit="sendForm"
        :autofill="autofillData"
        :accommodationType="event.accommodation"
        :mealType="event.meals"
        :possibleDiets="event.dietaryRequirements"
        :role="role.id"
        :requireEmail="event.email_required"
        @goToRolePick="goTo('role')"
      />
      <team-form
        v-else
        @submit="submitTeamForm"
        @goToRolePick="goTo('role')"
        @autofillPerson="debaterSelected"
        :autofill="autofillData"
        :accommodationType="event.accommodation"
        :mealType="event.meals"
        :possibleDiets="event.dietaryRequirements"
        :eventId="eventId"
        :requireEmail="event.email_required"
      ></team-form>
    </div>
    <!-- Success -->
    <q-dialog v-model="showGroupModal" persistent>
      <q-card>
        <q-card-section class="row items-center">
          <div class="text-h6">{{ $tr('groupModal.title') }}</div>
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
            @click="goTo('checkout')"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<script lang="ts">
/* eslint-disable */
// @ts-nocheck
import { defineComponent } from 'vue';
import { Role } from 'src/types/role';
import { Event, EventFull } from 'src/types/event';
import autofillCard from 'components/Event/AutofillCard.vue';
import formFields from 'components/Event/FormFields.vue';
import teamForm from 'components/Event/TeamForm.vue';

export default defineComponent({
  name: 'RegisterForm',

  props: {
    eventId: {
      type: Number,
      required: true,
    },
  },

  data() {
    return {
      translationPrefix: 'event.',
      autofillData: null,
      showGroupModal: false,
    };
  },

  components: {
    autofillCard,
    formFields,
    teamForm,
  },

  computed: {
    role(): Role {
      // eslint-disable-next-line max-len
      // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
      return <Role> this.$store.getters['roles/roleFromSlug'](
        this.$route.params.role,
      );
    },
    event(): EventFull {
      return <Event> this.$store.getters['events/fullEvent'](this.eventId);
    }
  },

  methods: {
    debaterSelected(data) {
      this.autofillData = data;
    },

    sendForm(data, autofill) {
      const personData = data;

      const registrationData = {
        person: null,
        event: this.eventId,
        role: this.role.id === Infinity ? 1 : this.role, // if role is team, set as debater
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

      this.$store.commit('eventRegistrationForm/addEventRegistration', {
        person: personData,
        registration: registrationData,
        autofill,
      });

      if (this.type === 'single') {
        this.goTo('checkout');
      } else {
        this.showGroupModal = true;
      }
    },

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
      this.$bus.$emit('fullLoader', true);
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
          this.$bus.$emit('fullLoader', false);
        });
    },

    goTo(phase: string) {
      window.alert(`GO TO ${phase}`);
    }
  },
});
</script>

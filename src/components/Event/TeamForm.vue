<template>
  <!-- Submit on validation error is needed in order to inform user about errors -->
  <q-form class="team-form" @submit="submitForm" @validation-error="submitForm">
    <q-select
      outlined
      :value="teamName"
      :label="$tr('fields.teamName')"
      use-input
      hide-selected
      fill-input
      input-debounce="0"
      :options="teamsAutofill"
      @input="autofillSelected"
      @filter="filterTeamNames"
      @input-value="teamName = $event"
      lazy-rules
      :loading="loadingTeamFill"
      :rules="[
        val =>
          (val && val.length > 0) || $tr('general.form.fieldError', null, false)
      ]"
    />

    <person-card
      ref="person-card"
      v-for="(person, id, index) in people"
      v-bind:key="id"
      :visible="id == visibleId"
      :id="id"
      :index="index"
      :error="person.error"
      :autofill="person.autofill"
      @toggleVisibility="toggleVisibility"
      @delete="deletePerson"
      :possibleDiets="possibleDiets"
      :accommodationType="accommodationType"
    />
    <g-d-p-r-checkbox v-model="accept" :error="acceptError" />
    <div class="text-center">
      <q-btn
        color="blue-9"
        icon="fas fa-plus"
        :disabled="Object.keys(people).length >= maxMembers"
        :label="$tr('buttons.addDebater')"
        @click="addPerson"
        class="q-mb-sm"
      />
      <q-btn
        :label="$tr('buttons.continue')"
        :disabled="!Object.keys(people).length"
        type="submit"
        color="primary"
        class="q-ml-sm q-mr-sm q-mb-sm"
      />
      <q-btn
        :label="$tr('buttons.clear')"
        type="reset"
        color="primary"
        @click="$emit('goToRolePick')"
        flat
        class="q-pt-sm q-mb-sm"
      />
    </div>
  </q-form>
</template>

<script>
import personCard from "./TeamPersonCard";
import GDPRCheckbox from "./GDPRCheckbox";
export default {
  name: "TeamForm",
  components: {
    GDPRCheckbox,
    personCard
  },
  props: {
    autofill: Object,
    accommodationType: String,
    possibleDiets: Array
  },
  data() {
    return {
      pastTeams: [],
      teamsAutofill: [],
      translationPrefix: "tournament.",
      people: {},
      visibleId: null,
      teamName: null,
      teamId: null,
      accept: false,
      acceptError: false,
      maxMembers: 5,
      loadingTeamFill: false
    };
  },
  created() {
    this.addPerson();

    let cached = this.$db("autofillTeams");

    if (cached) return (this.pastTeams = this.teamsAutofill = cached);

    this.$api({
      url: "user/" + this.$auth.user().id + "/team", // "/event/" + this.eventId,
      method: "get"
    }).then(d => {
      this.pastTeams = this.teamsAutofill = d.data;
      this.$db("autofillTeams", this.pastTeams, true);
    });
  },
  methods: {
    filterTeamNames(val, update) {
      if (val) this.teamId = null;

      update(() => {
        const needle = val.toLocaleLowerCase();
        this.teamsAutofill = this.pastTeams
          // Filter teams based on name
          .filter(v => v.name.toLocaleLowerCase().includes(needle))
          // Parse teams to select compatible objects
          .map(item => {
            return {
              label: item.name,
              value: item.id
            };
          });
      });
    },
    autofillSelected(value) {
      this.teamId = value.value;

      this.loadingTeamFill = true;

      // Team selected -> get its members
      this.$api({
        url: "team/" + this.teamId,
        method: "get"
      })
        .then(d => {
          // Empty people
          this.people = {};
          this.addPerson();

          let data = d.data.members;

          // Autofill all members
          let autofillMember = index => {
            let member = data[index];

            this.$emit("autofillPerson", member);

            if (!data[index + 1])
              return this.$nextTick(() => {
                this.visibleId = null;
              });

            this.$nextTick(() => {
              this.addPerson();

              this.$nextTick(() => {
                autofillMember(index + 1);
              });
            });
          };

          this.$nextTick(() => {
            autofillMember(0);
          });
        })
        .finally(() => {
          this.loadingTeamFill = false;
        });
    },
    toggleVisibility(id) {
      if (this.visibleId === id) this.visibleId = null;
      else this.visibleId = id;
    },
    deletePerson(id) {
      this.$delete(this.people, id);
    },
    addPerson() {
      if (Object.keys(this.people).length >= this.maxMembers)
        return this.$flash(
          this.$tr("errors.teamLength", { len: this.maxMembers }),
          "error"
        );

      let id;
      do {
        id = this.generateId();
      } while (this.people[id]);

      this.$set(this.people, id, {
        formData: null,
        autofillData: null,
        error: false,
        autofill: null
      });
      this.toggleVisibility(id);
    },

    // Source: https://jsfiddle.net/yo39a9cw/
    generateId(len = 5) {
      let text = "";
      let chars = "abcdefghijklmnopqrstuvwxyz0123456789";

      for (let i = 0; i < len; i++) {
        text += chars.charAt(Math.floor(Math.random() * chars.length));
      }

      return text;
    },

    submitForm() {
      let validationPromise = new Promise((resolve, reject) => {
        this.acceptError = !this.accept;

        if (!this.accept || !this.teamName || !this.teamName.trim().length)
          reject();

        let cards = this.$refs["person-card"];
        let validated = 0;
        let hasError = false;

        if (!cards.length) return reject();

        // Validate and submit all people
        cards.forEach(item => {
          let formFields = item.$refs["form-fields"];
          let form = formFields.$refs["q-form"];
          let id = item.id;

          // Trigger qForm validation
          form.validate().then(isValid => {
            validated++;
            if (isValid) {
              let values = formFields.sendForm();

              if (values) {
                this.$set(this.people[id], "formData", values.formData);
                this.$set(this.people[id], "autofillData", values.autofillData);
                this.$set(this.people[id], "error", false);

                if (validated >= cards.length) {
                  if (hasError) reject();
                  else resolve();
                }

                return true;
              }
            }

            this.$set(this.people[id], "error", true);
            hasError = true;

            if (validated >= cards.length) reject();
          });
        });
      });

      validationPromise
        .then(() => {
          this.$emit("submit", this.people, this.teamName, this.teamId);
        })
        .catch(() => {
          this.$flash(this.$tr("general.form.error", null, false), "error");
        });
    }
  },

  watch: {
    autofill(data) {
      if (this.visibleId)
        this.$set(this.people[this.visibleId], "autofill", data);
    }
  }
};
</script>

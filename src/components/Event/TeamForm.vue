<template>
  <!-- Submit on validation error is needed in order to inform user about errors -->
  <q-form class="team-form" @submit="submitForm" @validation-error="submitForm">
    <q-input
      outlined
      v-model="teamName"
      :label="$tr('fields.teamName')"
      class="col-12 col-sm-6"
      lazy-rules
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
    />
    <g-d-p-r-checkbox v-model="accept" :error="acceptError" />
    <div class="text-center">
      <q-btn
        color="blue-9"
        icon="fas fa-plus"
        :label="$tr('buttons.addDebater')"
        @click="addPerson"
      />
      <q-btn
        :label="$tr('buttons.continue')"
        type="submit"
        color="primary"
        class="q-ml-sm q-mr-sm"
      />
      <q-btn
        :label="$tr('buttons.clear')"
        type="reset"
        color="primary"
        @click="$emit('goToRolePick')"
        flat
        class="q-pt-sm"
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
    autofill: Object
  },
  data() {
    return {
      translationPrefix: "tournament.",
      people: {},
      visibleId: null,
      teamName: null,
      accept: false,
      acceptError: false
    };
  },
  created() {
    this.addPerson();
  },
  methods: {
    toggleVisibility(id) {
      if (this.visibleId == id) this.visibleId = null;
      else this.visibleId = id;
    },
    deletePerson(id) {
      this.$delete(this.people, id);
    },
    addPerson() {
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
          this.$emit("submit", this.people, this.teamName);
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

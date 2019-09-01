<template>
  <div>
    <div class="row">
      <person-card
        v-for="person in formData"
        v-bind:key="JSON.stringify(person)"
        :person="person"
      />
    </div>

    <div class="q-pt-md">
      <q-btn
        label="Přidat dalšího člověka"
        color="blue-9"
        @click="$emit('goToRolePick')"
      />
      <q-btn
        :loading="loading"
        class="float-right"
        size="lg"
        color="primary"
        @click="sendForm"
      >
        Potvrdit přihlášku
        <template v-slot:loading>
          <q-spinner-hourglass class="on-left" />
          Odesílám
        </template>
      </q-btn>
    </div>
  </div>
</template>

<script>
import personCard from "./CheckoutPersonCard";

export default {
  name: "Checkout",
  props: {
    formData: Array
  },
  data() {
    return {
      loading: false
    };
  },
  methods: {
    sendForm() {
      this.loading = !this.loading;

      // Send person and registration requests
      let registrationPromise = new Promise((resolve, reject) => {
        let registerCount = this.formData.length;
        let registered = 0;

        for (let index in this.formData) {
          let person = this.formData[index];

          let createPerson = this._createPerson(person);

          createPerson
            .then(person_id => {
              person.registration.person = person_id;
              this.$api({
                url: "registration",
                data: person.registration,
                method: "post",
                alerts: false
              })
                .then(data => {
                  registered++;
                  if (registerCount <= registered) return resolve(data);
                })
                .catch(data => {
                  reject(data);
                });
            })
            .catch(data => {
              reject(data);
            });
        }
      });

      // All done -> ask for confirmation
      registrationPromise
        .then(data => {
          this.$api({
            url: "registration/" + data.data.id + "/confirm",
            method: "put"
          })
            .then(data => {
              // TODO - redirect to confirm page with received data
              this.$flash("Registrace úšpěšně odeslána!", "success");
              console.log("Registration confirmed!", data.data);
            })
            .finally(() => {
              this.loading = false;
            });
        })
        .catch(() => {
          this.loading = false;
          this.$flash(
            "V průběhu registrace nastala chyba, opakujte akci.",
            "error"
          );
        });
    },

    // Create or update person
    _createPerson(person) {
      return new Promise((resolve, reject) => {
        let autofill = person.autofill;

        // Person is just autofilled and not edited -> return ID
        if (autofill && !autofill.edited) return resolve(autofill.id);

        // Person is autofilled and edited / newly created
        this.$api({
          url: "person" + (autofill ? "/" + autofill.id : ""),
          data: person.person,
          method: autofill ? "put" : "post",
          alerts: false
        })
          .then(data => {
            resolve(data.data.id);
          })
          .catch(data => {
            reject(data);
          });
      });
    }
  },
  components: {
    personCard
  }
};
</script>

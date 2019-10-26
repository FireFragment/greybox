<template>
  <div>
    <div class="row q-mb-md">
      <div class="col-12 col-sm-6 col-md-3 q-pa-xs items-stretch">
        <q-card class="thin-header-card normal-margin-card">
          <q-card-section class="bg-blue-9 text-white card-header">
            <div class="row items-center no-wrap">
              <div class="col">
                <div class="text-h6">{{ $tr("billing.title") }}</div>
              </div>
              <div class="col-auto">
                <billing-menu @selected="changeBilling" />
              </div>
            </div>
          </q-card-section>

          <q-card-section>
            <div v-if="!billingClient">
              {{ $auth.user().username }}
            </div>
            <div v-else>
              {{ billingClient.name }}
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <div class="row">
      <person-card
        v-for="(person, index) in formData"
        v-bind:key="JSON.stringify(person)"
        :person="person"
        :person-index="index"
        @remove="removePerson"
      />
    </div>

    <div class="q-pt-md">
      <q-btn
        :label="$tr('back')"
        color="blue-9"
        @click="$emit('goToRolePick')"
        class="q-my-xs"
      />
      <q-btn
        :loading="loading"
        class="float-right q-my-xs"
        size="lg"
        color="primary"
        @click="sendForm"
      >
        {{ $tr("submit") }}
        <template v-slot:loading>
          <q-spinner-hourglass class="on-left" />
          {{ $tr("loading") }}
        </template>
      </q-btn>
    </div>
  </div>
</template>

<script>
import personCard from "./CheckoutPersonCard";
import billingMenu from "./BillingMenu";

export default {
  name: "Checkout",
  props: {
    formData: Array
  },
  data() {
    return {
      translationPrefix: "tournament.checkout.",
      loading: false,
      billingClient: null
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
              this.formData[index].registration.person = person_id;

              if (person.registered_data) {
                registered++;
                if (registerCount <= registered)
                  return resolve(person.registered_data);
              }

              this.$api({
                url: "registration",
                data: person.registration,
                method: "post",
                alerts: false
              })
                .then(data => {
                  registered++;
                  this.formData[index].registered_data = data;
                  if (registerCount <= registered) return resolve(data);
                })
                .catch(data => {
                  reject([data, person]);
                });
            })
            .catch(data => {
              reject([data, person]);
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
              this.$flash(this.$tr("success"), "success");
              this.$emit("confirm", data.data);
            })
            .catch(data => {
              if (data.response.data) {
                let message = data.response.data.message;

                if (message)
                  return this.$flash(
                    this.$tr("validation." + message),
                    "error"
                  );
              }

              this.$flash(this.$tr("error"), "error");
            })
            .finally(() => {
              this.loading = false;
            });
        })
        .catch(([data, person = null]) => {
          this.loading = false;

          if (data.response.data) {
            let message = data.response.data.message;

            if (message)
              return this.$flash(
                this.$tr("validation." + message, {
                  person: person.person.name + " " + person.person.surname
                }),
                "error"
              );
          }

          this.$flash(this.$tr("error"), "error");
        });
    },

    // Create or update person
    _createPerson(person) {
      return new Promise((resolve, reject) => {
        let autofill = person.autofill;

        // Person is just autofilled and not edited -> return ID
        if (autofill && !autofill.edited) return resolve(autofill.id);

        // Person already created -> don't recreate
        if (person.registration.person)
          return resolve(person.registration.person);

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
    },

    removePerson(index) {
      this.$emit("removePerson", index);
    },

    changeBilling(client) {
      this.billingClient = client;
      // TODO emit event with client.id
    }
  },
  components: {
    personCard,
    billingMenu
  }
};
</script>

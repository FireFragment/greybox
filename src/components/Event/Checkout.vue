<template>
    <div class="q-px-lg">
      <div class="row q-mb-md">
        <div class="col-12 col-sm-6 col-md-3 q-py-xs items-stretch">
          <q-card class="thin-header-card normal-margin-card">
            <q-card-section class="bg-blue-9 text-white card-header">
              <div class="row items-center no-wrap">
                <div class="col">
                  <div class="text-h6">{{ $tr('billing.title') }}</div>
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
                <p class="q-mb-sm">
                  <b>{{ billingClient.name }}</b>
                  <template v-if="billingClient.email">
                    <br />{{ billingClient.email }}
                  </template>
                </p>
                <p class="q-mb-sm" v-if="billingClient.registration_no">
                  {{ $tr('billing.fields.registration_no') }}:
                  {{ billingClient.registration_no }}
                </p>
                <p
                    class="q-mb-none"
                    v-if="
                  billingClient.street ||
                    billingClient.city ||
                    billingClient.zip ||
                    selectedCountry
                "
                >
                  {{ billingClient.street }} <br v-if="billingClient.street" />
                  {{
                    billingClient.city
                  }}
                  <template v-if="billingClient.zip && billingClient.city"
                  >,
                  </template>
                  {{ billingClient.zip }}
                  <template v-if="selectedCountry">
                    <br v-if="billingClient.city || billingClient.zip" />
                    {{ $tr(selectedCountry) }}

                    <!-- Element has to be mounted in order for API to be loaded -->
                    <country-select v-show="false" />
                  </template>
                </p>
              </div>
            </q-card-section>
          </q-card>
        </div>
      </div>

      <div class="row -q-mx-sm">
        <person-card
            v-for="(person, index) in formData"
            v-bind:key="JSON.stringify(person)"
            :person="person"
            :registration="person.registration"
            :person-index="index"
            :possible-diets="possibleDiets"
            @remove="removePerson"
        />
      </div>

      <div class="q-pt-md row">
        <div class="col-12 col-sm-6">
          <q-btn
              :label="$tr('back')"
              color="blue-9"
              @click="$emit('goToRolePick')"
              class="q-my-xs"
          />
        </div>
        <div class="col-12 col-sm-6">
          <q-btn
              :loading="loading"
              class="float-right q-my-xs"
              size="lg"
              color="primary"
              @click="sendForm"
          >
            {{ $tr('submit') }}
            <template v-slot:loading>
              <q-spinner-hourglass class="on-left" />
              {{ $tr('loading') }}
            </template>
          </q-btn>
        </div>
      </div>
  </div>
</template>

<script>
/* eslint-disable */
import PersonCard from './CheckoutPersonCard';
import billingMenu from './BillingMenu';
import CountrySelect from './CountrySelect';

export default {
  name: 'Checkout',
  props: {
    formData: Array,
    possibleDiets: Array,
  },
  emits: [
    'goToRolePick',
    'confirm',
    'removePerson',
  ],
  data() {
    return {
      translationPrefix: 'tournament.checkout.',
      loading: false,
      billingClient: null,
    };
  },
  computed: {
    selectedCountry() {
      if (!this.$isPDS || !this.billingClient || !this.billingClient.country) return null;

      const db = this.$db('countries-select');

      if (!db) return null;

      const filtered = db.filter(
        (item) => item.value === this.billingClient.country,
      );

      if (filtered.length) return filtered[0].label;

      return null;
    },
  },
  methods: {
    sendForm() {
      this.loading = !this.loading;

      // Send person and registration requests
      const registrationPromise = new Promise((resolve, reject) => {
        const registerCount = this.formData.length;
        let registered = 0;

        for (const index in this.formData) {
          const person = this.formData[index];

          const createPerson = this._createPerson(person);

          createPerson
            .then((person_id) => {
              person.registration.person = person_id;
              this.formData[index].registration.person = person_id;

              if (person.registered_data) {
                registered++;
                if (registerCount <= registered) return resolve(person.registered_data);
              }

              this.$api({
                url: 'registration',
                data: person.registration,
                method: 'post',
                alerts: false,
              })
                .then((data) => {
                  registered++;
                  this.formData[index].registered_data = data;
                  if (registerCount <= registered) return resolve(data);
                })
                .catch((data) => {
                  reject([data, person]);
                });
            })
            .catch((data) => {
              reject([data, person]);
            });
        }
      });

      // All done -> ask for confirmation
      registrationPromise
        .then((data) => {
          const confirmData = {
            lang: this.$i18n.locale,
          };

          if (this.billingClient) confirmData.client = this.billingClient.id;

          this.$api({
            url: `registration/${data.data.id}/confirm`,
            method: 'put',
            data: confirmData,
          })
            .then((data) => {
              this.$flash(this.$tr('success'), 'success');
              this.$emit('confirm', data.data);
            })
            .catch((data) => {
              if (data.response && data.response.data) {
                const { message } = data.response.data;

                if (message) {
                  return this.$flash(
                    this.$tr(`validation.${message}`),
                    'error',
                  );
                }
              }

              this.$flash(this.$tr('error'), 'error');
            })
            .finally(() => {
              this.loading = false;
            });
        })
        .catch(([data, person = null]) => {
          this.loading = false;

          if (data.response && data.response.data) {
            const { message } = data.response.data;

            if (message) {
              return this.$flash(
                this.$tr(`validation.${message}`, {
                  person: `${person.person.name} ${person.person.surname}`,
                }),
                'error',
              );
            }
          }

          this.$flash(this.$tr('error'), 'error');
        });
    },

    // Create or update person
    _createPerson(person) {
      return new Promise((resolve, reject) => {
        const { autofill } = person;

        // Person is just autofilled and not edited -> return ID
        if (autofill && !autofill.edited) return resolve(autofill.id);

        // Person already created -> don't recreate
        if (person.registration.person) return resolve(person.registration.person);

        // Person is autofilled and edited / newly created
        this.$api({
          url: `person${autofill ? `/${autofill.id}` : ''}`,
          data: person.person,
          method: autofill ? 'put' : 'post',
          alerts: false,
        })
          .then((data) => {
            resolve(data.data.id);
          })
          .catch((data) => {
            reject(data);
          });
      });
    },

    removePerson(index) {
      this.$emit('removePerson', index);
    },

    changeBilling(client) {
      this.billingClient = client;
    },
  },
  components: {
    CountrySelect,
    PersonCard,
    billingMenu,
  },
};
</script>

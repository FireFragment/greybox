<template>
  <div class="row justify-center" v-if="data">
    <div class="col-12 col-md-6">
      <q-banner class="bg-primary text-white q-mt-md">
        <template v-slot:avatar>
          <q-icon name="fas fa-check" color="white" />
        </template>
        {{ $tr("title") }}
        <template v-if="data.totalAmount">{{ $tr("invoiceTitle") }}</template>
      </q-banner>
    </div>
    <div class="col-12">
      <div
        class="row justify-center q-mt-lg"
        v-if="data.totalAmount && typeof data.invoice === 'object'"
      >
        <div class="col-12 col-md-3">
          <q-card class="my-card">
            <!-- Don't show QR code for EUR payments -->
            <img
              v-if="data.invoice.qr_full_url && data.invoice.currency !== 'EUR'"
              :src="data.invoice.qr_full_url"
              alt="QR"
            />

            <!-- Key is needed to rerender on language change -->
            <q-list v-bind:key="$tr('invoice.item')">
              <q-item>
                <q-item-section avatar>
                  <q-icon color="primary" name="fas fa-dollar-sign" />
                </q-item-section>

                <q-item-section>
                  <q-item-label caption>{{
                      $tr("invoice.total")
                    }}</q-item-label>
                  <q-item-label
                  >{{
                      data.invoice.total
                        .replace(/\d(?=(\d{3})+\.)/g, "$& ")
                        .replace(/\./g, ",")
                    }}
                    {{ data.invoice.currency }}</q-item-label
                  >
                </q-item-section>
              </q-item>

              <q-item>
                <q-item-section avatar>
                  <q-icon color="green" name="far fa-calendar-alt" />
                </q-item-section>

                <q-item-section>
                  <q-item-label caption>{{
                      $tr("invoice.dueOn")
                    }}</q-item-label>
                  <q-item-label>
                    {{ getDate(data.invoice.due_on, "D. M. YYYY") }}
                  </q-item-label>
                </q-item-section>
              </q-item>

              <q-item
                clickable
                class="hidden-link"
                :to="data.invoice.pdf_full_url"
                :href="data.invoice.pdf_full_url"
                target="_blank"
              >
                <q-item-section avatar>
                  <q-icon color="blue-9" name="fas fa-file-download" />
                </q-item-section>

                <q-item-section>
                  <q-item-label class="text-black">{{
                      $tr("invoice.download")
                    }}</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </q-card>
        </div>
      </div>
      <div class="row justify-center q-mt-lg">
        <div class="col-12 col-md-7">
          <q-table
            :rows="data.invoiceLines"
            :columns="columns"
            row-key="name"
            :hide-bottom="true"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
/* eslint-disable */
import { date } from "quasar";

export default {
  name: 'CheckoutConfirm',
  data() {
    return {
      translationPrefix: 'event.checkout.confirm.',
    };
  },
  methods: {
    getDate: date.formatDate,
    enterGuard() {
      if (!this.data) {
        this.$router.replace(this.$translatedRouteLink({
          name: 'event-pick-role',
          params: this.$route.params,
        }));
      }
    }
  },
  beforeMount() {
    this.enterGuard();
  },
  mounted() {
    // Needed in case anyone accesses the route directly
    setTimeout(() => this.enterGuard(), 200);
  },
  computed: {
    eventId() {
      return this.$route.params.id;
    },
    columns() {
      return [
        {
          label: this.$tr('invoice.item'),
          name: 'name',
          align: 'left',
          field: 'name',
        },
        {
          label: this.$tr('invoice.quantity'),
          name: 'quantity',
          align: 'center',
          field: (row) => `${row.quantity} ${row.unit_name}`,
        },
        {
          label: this.$tr('invoice.price'),
          name: 'unit_price',
          field: (row) => `${row.unit_price
          } ${
            typeof this.data.invoice === 'object'
              ? this.data.invoice.currency
              : 'CZK'}`, // to možná nestačí
        },
      ];
    },
    data() {
      return this.$store.state.eventRegistrationForm[this.eventId].confirmation;
    },
  },
};
</script>

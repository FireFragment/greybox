<template>
  <div>
    <q-btn
      color="white"
      round
      flat
      icon="edit"
      :loading="loading"
      @click="openMenu"
      ref="main-btn"
    >
      <q-menu cover auto-close loading v-if="clients">
        <q-list>
          <q-item clickable @click="editClient()">
            <q-item-section>Přidat nové údaje</q-item-section>
            <q-item-section avatar>
              <q-icon name="fas fa-plus" />
            </q-item-section>
          </q-item>
          <q-separator v-if="clients.length" />
          <q-item
            clickable
            @click="selectClient(client)"
            v-for="client in clients"
            v-bind:key="client.id"
          >
            <q-item-section>{{ client.name }} </q-item-section>
            <q-item-section avatar>
              <q-btn
                color="black"
                round
                flat
                icon="edit"
                @click.stop="editClient(client)"
              />
            </q-item-section>
          </q-item>
        </q-list>
      </q-menu>
    </q-btn>
    <edit-dialog
      @state-change="modalChange"
      :visible="showEditModal"
      :client="editedClient"
    />
  </div>
</template>

<script>
import editDialog from "./BillingEditDialog";

export default {
  name: "BillingMenu",
  components: { editDialog },
  data() {
    return {
      translationPrefix: "tournament.checkout.billing.",
      loading: false,
      clients: null,
      editedClient: null,
      showEditModal: false
    };
  },
  methods: {
    openMenu() {
      if (this.clients) return true;

      this.loading = true;

      // TODO - load clients from API if they were not loaded from cache earlier
      setTimeout(() => {
        this.clients = [];
        this.loading = false;

        setTimeout(() => {
          this.$refs["main-btn"].$el.click();
        }, 100);
      }, 1000);
    },

    selectClient(client) {
      this.$emit("selected", client);
    },

    editClient(client = null) {
      this.editedClient = client;

      this.showEditModal = true;
      console.log("editing ", client);
    },

    modalChange(value) {
      this.showEditModal = value;
    }
  },
  mounted() {
    // TODO - load clients from cache if exist
    this.clients = [
      {
        id: 2,
        name: "Test2",
        street: "Sesami street",
        street2: "239/1",
        city: "Hogsmeade",
        zip: "12345",
        country: "US",
        registration_no: "0987654321",
        full_name: "Mister Client",
        email: "mister@client.com",
        user: 1,
        created_at: "2019-07-28 00:50:48",
        updated_at: "2019-07-28 00:50:48"
      },
      {
        id: 6,
        name: "Test",
        street: null,
        street2: null,
        city: null,
        zip: null,
        country: "CZ",
        registration_no: null,
        full_name: null,
        email: null,
        user: 1,
        created_at: "2019-07-28 00:50:48",
        updated_at: "2019-07-28 00:50:48"
      }
    ];
  }
};
</script>

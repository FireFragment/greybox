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
        <q-list class="smaller-margin-menu">
          <q-item clickable @click="editClient()">
            <q-item-section avatar>
              <q-icon name="fas fa-plus" />
            </q-item-section>
            <q-item-section>{{ $tr("addButton") }}</q-item-section>
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
      @client-change="clientChange"
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
      // Clients already available
      if (this.clients) return true;

      // Load clients from API
      this.loading = true;

      this.$api({
        //url: "client",
        url: "user/" + this.$auth.user().id + "/client",
        method: "get"
      })
        .then(data => {
          this.clients = data.data;

          // Reopen client menu once loaded
          setTimeout(() => {
            this.$refs["main-btn"].$el.click();
          }, 100);
        })
        .finally(() => {
          this.loading = false;
        });
    },

    selectClient(client) {
      this.$emit("selected", client);
    },

    // Trigger client editing
    editClient(client = null) {
      this.editedClient = client;

      this.showEditModal = true;
    },

    // Client was changed - update inner DB
    clientChange(id, data, isNew = false) {
      this.selectClient(data);

      // Add new client
      if (isNew) return this.clients.push(data);

      this.clients.forEach((client, index) => {
        if (client.id === id) {
          // Remove client
          if (!data) this.$delete(this.clients, index);
          // Update client
          else this.$set(this.clients, index, data);
        }
      });
    },

    // Modal's state change
    modalChange(value) {
      this.showEditModal = value;
    }
  },
  mounted() {
    // Load clients from cache if it exists
    let cached = this.$db("billingClients");

    if (cached) this.clients = cached;
  },

  watch: {
    // Update cache on clients change
    clients(value) {
      this.$db("billingClients", value, true);
    }
  }
};
</script>

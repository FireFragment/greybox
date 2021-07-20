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
      <q-menu cover auto-close v-if="clients" v-model="showPopupMenu">
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
      :key="
        (editedClient ? editedClient.id : 'new') +
          (showEditModal ? 'visible' : 'n')
      "
    />
  </div>
</template>

<script>
/* eslint-disable */
import editDialog from './BillingEditDialog';

export default {
  name: 'BillingMenu',
  components: { editDialog },
  data() {
    return {
      translationPrefix: 'tournament.checkout.billing.',
      loading: false,
      clients: null,
      editedClient: null,
      showPopupMenu: false,
      showEditModal: false,
    };
  },
  methods: {
    openMenu() {
      // Clients already available
      if (this.clients) return true;

      // Load clients from API
      this.loadClients().then(() => {
        // Reopen client menu once loaded
        setTimeout(() => {
          this.$refs['main-btn'].$el.click();
        }, 100);
      });
    },

    loadClients() {
      if (!this.clients) {
        // Try loading billing details from cache
        const cached = this.$db('billingDetails', null, true);

        if (cached) this.clients = cached;
        else this.loading = true;
      }

      return new Promise((resolve, reject) => {
        if (this.clients) return resolve(this.clients);

        this.$api({
          url: `user/${this.$auth.user().id}/client`,
          method: 'get',
        })
          .then((data) => {
            this.clients = data.data;
            this.$db('billingDetails', this.clients, true);
            resolve(this.clients);
          })
          .catch((data) => {
            this.$flash(this.$tr('general.error', null, false), 'error');
            reject(data);
          })
          .finally(() => {
            this.loading = false;
          });
      });
    },

    selectClient(client) {
      this.$emit('selected', client);
    },

    // Trigger client editing
    editClient(client = null) {
      this.editedClient = client;

      this.showEditModal = true;
      this.showPopupMenu = false;
    },

    // Client was changed - update inner DB
    clientChange(id, data, isNew = false) {
      this.selectClient(data);

      // Add new client
      if (isNew) this.clients.push(data);
      else {
        this.clients.forEach((client, index) => {
          if (client.id === id) {
          // Remove client
            if (!data) delete this.clients[index];
            // Update client
            else this.clients[index] = data;
          }
        });
      }

      // Update cache
      this.$db('billingDetails', this.clients, true);
    },

    // Modal's state change
    modalChange(value) {
      this.showEditModal = value;
    },
  },
  created() {
    this.loadClients().then(() => {
      if (this.clients && this.clients.length)
      // some billing information found -> select first one
      { this.selectClient(this.clients[0]); }
      // no billing info -> open modal to create one
      else this.editClient();
    });
  },
  mounted() {
    // Load clients from cache if it exists
    const cached = this.$db('billingClients');

    if (cached) this.clients = cached;
  },

  watch: {
    // Update cache on clients change
    clients(value) {
      this.$db('billingClients', value, true);
    },
  },
};
</script>

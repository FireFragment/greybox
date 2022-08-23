<template>
  <q-page padding>
    <h1 class="text-center text-h4">...</h1>
    <div class="q-pa-md">
      <q-table
          :rows="$store.getters['eventsRegistrations/eventRegistrations'](eventId)"
          :columns="columns"
          :binary-state-sort="true"
          sort-by="surname"
          :pagination="initialPagination"
          no-data-label="Žádné přihlášky nenalezeny"
          row-key="id"
      />
    </div>
  </q-page>
</template>

<script lang="ts">

import { mapState } from 'vuex';
import { EventRegistration } from 'src/types/event';

export default {
  name: 'EventRegistrations',
  computed: {
    ...mapState('events', [
      'eventRegistrations',
    ]),
  },
  async created() {
    await this.$store.dispatch('eventsRegistrations/load', this.eventId);
  },
  data() {
    const outputBoolean = (val: boolean) => (val ? '✅' : '❌');

    return {
      // TODO - translate labels
      eventId: parseInt(this.$route.params.id, 10),
      columns: [{
        name: 'surname', label: 'Příjmení', field: (row: EventRegistration) => row.person.surname, sortable: true, align: 'left',
      }, {
        name: 'name', label: 'Jméno', field: (row: EventRegistration) => row.person.name, sortable: true, align: 'left',
      }, {
        name: 'role', label: 'Role', field: (row: EventRegistration) => row.role.name, format: this.$tr, sortable: true, align: 'left',
      }, {
        name: 'team', label: 'Tým', field: (row: EventRegistration) => row.team?.name ?? '-', sortable: true, align: 'left',
      }, {
        name: 'note', label: 'Poznámka', field: 'note', align: 'left', sortable: true,
      }, {
        name: 'accommodation', label: 'Ubytování', align: 'center', field: 'accommodation', format: outputBoolean, sortable: true,
      }, {
        name: 'meals', label: 'Jídlo', align: 'center', field: 'meals', format: outputBoolean, sortable: true,
      }],
      initialPagination: {
        sortBy: 'surname',
        descending: false,
        rowsPerPage: 20,
      },
      // TODO - translate 'Records per page' etc
    };
  },
};
</script>

<template>
  <q-page padding>
    <h1 class="text-center text-h4">{{ event ? $tr(event.name) : '-' }}</h1>
    <div class="q-pa-md">
      <q-table
        :rows="registrations"
        :columns="columns"
        :binary-state-sort="true"
        sort-by="surname"
        :pagination="initialPagination"
        no-data-label="Žádné přihlášky nenalezeny"
        row-key="id"
      >
        <template v-slot:header-cell-role="props">
          <q-th :props="props" class="filterable-table-heading">
            <q-select borderless v-model="roleFilterModel"
                      :options="roleFilterOptions"
                      option-value="id"
                      :option-label="item => $tr(item.name)"
                      :label="props.col.label" :dense="true" :options-dense="true"
                      class="roles-filter" popup-content-class="table-filter-options">
              <template v-slot:prepend>
                <q-icon name="fas fa-filter" />
              </template>
            </q-select>
          </q-th>
        </template>
        <template v-slot:header-cell-accommodation="props">
          <q-th :props="props" class="filterable-table-heading">
            <q-select borderless v-model="accommodationFilterModel" :options="booleanFilterOptions"
                      :label="props.col.label" :dense="true" :options-dense="true"
                      class="accommodation-filter" popup-content-class="table-filter-options">
              <template v-slot:prepend>
                <q-icon name="fas fa-filter" />
              </template>
            </q-select>
          </q-th>
        </template>
        <template v-slot:header-cell-meals="props">
          <q-th :props="props" class="filterable-table-heading">
            <q-select borderless v-model="mealsFilterModel" :options="booleanFilterOptions"
                      :label="props.col.label" :dense="true" :options-dense="true"
                      class="meals-filter" popup-content-class="table-filter-options">
              <template v-slot:prepend>
                <q-icon name="fas fa-filter" />
              </template>
            </q-select>
          </q-th>
        </template>
      </q-table>
    </div>
  </q-page>
</template>

<script lang="ts">

import { mapState } from 'vuex';
import { Event, EventRegistration } from 'src/types/event';
import { Role } from 'src/types/role';

export default {
  name: 'EventRegistrations',
  computed: {
    ...mapState('events', [
      'eventRegistrations',
    ]),
    registrations(): EventRegistration[] {
      return <EventRegistration[]> this.$store.getters['eventsRegistrations/eventRegistrations'](this.eventId);
    },
    event(): Event {
      return <Event> this.$store.getters['events/event'](this.eventId);
    },
    roleFilterOptions(): Role[] {
      if (!this.registrations) {
        return [];
      }

      const roles: Role[] = (<EventRegistration[]>Object.values(this.registrations))
        .map((item) => item.role);
      const idsOnly = roles.map((item) => item.id);

      // Filter out only unique roles
      return roles.filter((item, index) => idsOnly.indexOf(item.id) === index);
    },
  },
  async created() {
    await this.$store.dispatch('eventsRegistrations/load', this.eventId);
  },
  data() {
    const outputBoolean = (val: boolean) => (val ? '✅' : '❌');

    return {
      roleFilterModel: null,
      accommodationFilterModel: null,
      mealsFilterModel: null,
      booleanFilterOptions: ['Vše', 'Ano', 'Ne'],
      // TODO - translate labels
      eventId: parseInt(this.$route.params.id, 10),
      columns: [{
        name: 'surname', label: 'Příjmení', field: (row: EventRegistration) => row.person.surname, sortable: true, align: 'left',
      }, {
        name: 'name', label: 'Jméno', field: (row: EventRegistration) => row.person.name, sortable: true, align: 'left',
      }, {
        name: 'role', label: 'Role', field: (row: EventRegistration) => row.role.name, format: this.$tr, sortable: false, align: 'center',
      }, {
        name: 'team', label: 'Tým', field: (row: EventRegistration) => row.team?.name ?? '-', sortable: true, align: 'left',
      }, {
        name: 'note', label: 'Poznámka', field: 'note', align: 'left', sortable: true,
      }, {
        name: 'accommodation', label: 'Ubytování', align: 'center', field: 'accommodation', format: outputBoolean, sortable: false,
      }, {
        name: 'meals', label: 'Jídlo', align: 'center', field: 'meals', format: outputBoolean, sortable: false,
      }, {
        name: 'dietary_requirements', label: 'Jídlo - omezení', align: 'center', field: (row: EventRegistration) => row.person.dietary_requirement, sortable: true,
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

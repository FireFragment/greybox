<template>
  <q-page padding>
    <h1 class="text-center text-h4">{{ event ? $tr(event.name, null, false) : '-' }}</h1>
    <div class="q-pa-md" style="max-width: 350px">
      <q-card>
        <q-card-section>
          <div class="text-subtitle2">{{ $tr("overview.title") }}</div>
        </q-card-section>
        <q-card-section class="q-pa-none">
          <q-list bordered separator>
            <q-item
              v-for="role in uniqueRoles"
              :key="role.id"
            >
              <q-item-section>
                <q-item-label caption>{{ $tr(role.name, null, false) }}</q-item-label>
                <q-item-label>{{ roleRegistrationsCount(role) }}</q-item-label>
              </q-item-section>
            </q-item>
          </q-list>
        </q-card-section>
      </q-card>
    </div>
    <div class="q-pa-md">
      <q-table
        :rows="registrations"
        :columns="columns"
        :binary-state-sort="true"
        sort-by="surname"
        :pagination="initialPagination"
        :no-data-label="$tr('noData')"
        row-key="id"
        :filter="filterObject"
        :filter-method="filterTableRows"
      >
        <template v-slot:header-cell-role="props">
          <q-th :props="props" class="filterable-table-heading">
            <q-select borderless v-model="roleFilterModel"
                      :options="uniqueRoles"
                      option-value="id"
                      :option-label="item => $tr(item.name, null, false)"
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
        <template v-slot:body-cell-note="props">
          <q-td :props="props" class="small-overflow-column">
            {{ props.value }}
            <q-tooltip v-if="props.row.note">
              {{ props.value }}
            </q-tooltip>
          </q-td>
        </template>
      </q-table>
    </div>
  </q-page>
</template>

<script lang="ts">

import { mapState } from 'vuex';
import { Event, EventRegistration } from 'src/types/event';
import { Role } from 'src/types/role';
import { defineComponent } from 'vue';

const booleanFilterOptions = ['Vše', 'Ano', 'Ne'];

interface FilterObject {
  role: Role | null;
  accommodation: typeof booleanFilterOptions[number] | null;
  meals: (typeof booleanFilterOptions[number]) | null;
}

export default defineComponent({
  name: 'EventRegistrations',
  computed: {
    ...mapState('events', [
      'eventRegistrations',
    ]),
    registrations(): EventRegistration[] {
      // eslint-disable-next-line max-len
      // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
      return <EventRegistration[]> this.$store.getters['eventsRegistrations/eventRegistrations'](this.eventId);
    },
    event(): Event {
      // eslint-disable-next-line max-len
      // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
      return <Event> this.$store.getters['events/event'](this.eventId);
    },
    uniqueRoles(): Role[] {
      if (!this.registrations) {
        return [];
      }

      const roles: Role[] = (Object.values(this.registrations))
        .map((item) => item.role);
      const idsOnly = roles.map((item) => item.id);

      // Filter out only unique roles
      return [
        {
          id: 0,
          icon: '',
          name: {
            id: 0,
            cs: 'Vše',
            en: 'All',
            created_at: '',
            updated_at: '',
          },
          created_at: '',
          updated_at: '',
        },
        ...roles.filter((item, index) => idsOnly.indexOf(item.id) === index),
      ];
    },
    eventId(): number {
      const idParam: string | string[] = this.$route.params.id;
      if (typeof idParam !== 'string') {
        return 0;
      }
      return parseInt(idParam, 10);
    },
    filterObject(): FilterObject {
      return {
        role: this.roleFilterModel,
        accommodation: this.accommodationFilterModel,
        meals: this.mealsFilterModel,
      };
    },
  },
  async created() {
    await this.$store.dispatch('eventsRegistrations/load', this.eventId);
  },
  data() {
    const outputBoolean = (val: boolean) => (val ? '✅' : '❌');
    const emptyToHyphen = (val: string | null) => (val ?? '-');
    return {
      translationPrefix: 'admin.eventRegistrations.',
      roleFilterModel: null,
      accommodationFilterModel: null,
      mealsFilterModel: null,
      booleanFilterOptions,
      columns: [{
        name: 'surname', label: this.$tr('admin.eventRegistrations.labels.surname'), field: (row: EventRegistration) => row.person.surname, sortable: true, align: 'left',
      }, {
        name: 'name', label: this.$tr('admin.eventRegistrations.labels.name'), field: (row: EventRegistration) => row.person.name, sortable: true, align: 'left',
      }, {
        name: 'role', label: this.$tr('admin.eventRegistrations.labels.role'), field: (row: EventRegistration) => row.role.name, format: this.$tr, sortable: false, align: 'center',
      }, {
        name: 'team', label: this.$tr('admin.eventRegistrations.labels.team'), field: (row: EventRegistration) => row.team?.name ?? '-', sortable: true, align: 'left',
      }, {
        name: 'note', label: this.$tr('admin.eventRegistrations.labels.note'), field: 'note', format: emptyToHyphen, sortable: true, align: 'left',
      }, {
        name: 'accommodation', label: this.$tr('admin.eventRegistrations.labels.accommodation'), field: 'accommodation', format: outputBoolean, sortable: false, align: 'center',
      }, {
        name: 'meals', label: this.$tr('admin.eventRegistrations.labels.sumealsrname'), field: 'meals', format: outputBoolean, sortable: false, align: 'center',
      }, {
        name: 'dietary_requirements', label: this.$tr('admin.eventRegistrations.labels.dietaryRequirements'), field: (row: EventRegistration) => row.person.dietary_requirement, format: emptyToHyphen, sortable: true, align: 'center',
      }],
      initialPagination: {
        sortBy: 'surname',
        descending: false,
        rowsPerPage: 20,
      },
    };
  },
  methods: {
    filterTableRows: (rows: EventRegistration[], terms: FilterObject): EventRegistration[] => (
      rows.filter((item) => (
        (terms.role == null || terms.role.id === 0 || terms.role.id === item.role.id)
        && (terms.accommodation == null || terms.accommodation === 'Vše' || ((terms.accommodation === 'Ano') === item.accommodation))
        && (terms.meals == null || terms.meals === 'Vše' || ((terms.meals === 'Ano') === item.meals))
      ))
    ),
    roleRegistrationsCount(role: Role): number {
      return this.registrations.filter((item) => role.id === 0 || role.id === item.role.id).length;
    },
  },
});
</script>

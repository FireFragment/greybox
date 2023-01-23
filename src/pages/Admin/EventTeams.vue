<template>
  <q-page padding>
    <h1 class="text-center text-h4">
      {{ event ? $tr(event.name, null, false) : '-' }} - {{ $tr("viewTypes.teams") }}
    </h1>
    <div class="q-pa-md">
      <q-table
        :rows="registrations"
        :columns="columns"
        :binary-state-sort="true"
        sort-by="surname"
        :pagination="initialPagination"
        :no-data-label="$tr('noData')"
        row-key="id"
        :loading="tableLoading"
        color="primary"
      >
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
import { EventFull, EventRegistration } from 'src/types/event';

import { defineComponent } from 'vue';

export default defineComponent({
  name: 'EventTeams',
  computed: {
    ...mapState('events', [
      'eventRegistrations',
    ]),
    registrations(): EventRegistration[] {
      // eslint-disable-next-line max-len
      // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
      return <EventRegistration[]> this.$store.getters['eventsRegistrations/eventRegistrations'](this.eventId);
    },
    event(): EventFull {
      // eslint-disable-next-line max-len
      // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
      return <EventFull> this.$store.getters['events/fullEvent'](this.eventId);
    },
    eventId(): number {
      const idParam: string | string[] = this.$route.params.id;
      if (typeof idParam !== 'string') {
        return 0;
      }
      return parseInt(idParam, 10);
    },
  },
  async created() {
    // Not cached -> load from API
    await this.$store.dispatch('events/loadFull', this.eventId);
    await this.$store.dispatch('roles/load');
    await this.$store.dispatch('eventsRegistrations/load', this.eventId);
  },
  data() {
    const emptyToHyphen = (val: string | null) => (val ?? '-');
    return {
      translationPrefix: 'admin.eventRegistrations.',
      roleFilterModel: null,
      accommodationFilterModel: null,
      mealsFilterModel: null,
      columns: [{
        name: 'surname', label: this.$tr('admin.eventRegistrations.labels.club'), field: (row: EventRegistration) => row.person.surname, sortable: true, align: 'left',
      }, {
        name: 'name', label: this.$tr('admin.eventRegistrations.labels.teamName'), field: (row: EventRegistration) => row.person.name, sortable: true, align: 'left',
      }, {
        name: 'role', label: this.$tr('admin.eventRegistrations.labels.members'), field: (row: EventRegistration) => row.role.name, format: this.$tr, sortable: false, align: 'left',
      }, {
        name: 'team', label: this.$tr('admin.eventRegistrations.labels.registrant'), field: (row: EventRegistration) => row.team?.name ?? '-', sortable: false, align: 'left',
      }, {
        name: 'note', label: this.$tr('admin.eventRegistrations.labels.note'), field: 'note', format: emptyToHyphen, sortable: true, align: 'left',
      }],
      initialPagination: {
        sortBy: 'surname',
        descending: false,
        rowsPerPage: 20,
      },
      tableLoading: false,
    };
  },
});
</script>

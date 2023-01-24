<template>
  <q-page padding v-if="teams">
    <h1 class="text-center text-h4">
      {{ event ? $tr(event.name, null, false) : '-' }} - {{ $tr("viewTypes.teams") }}
    </h1>
    <div class="q-pa-md">
      <q-table
        :rows="properTeams"
        :columns="columns"
        :binary-state-sort="true"
        sort-by="surname"
        :pagination="initialPagination"
        :no-data-label="$tr('noData')"
        row-key="id"
        :loading="tableLoading"
        color="primary"
      >
        <!-- Members body cell -->
        <template v-slot:body-cell-members="props">
          <q-td :props="props">
            <span v-for="(person, index) in props.value"
                      v-bind:key="person.id">
              <component :is="person.old_greybox_id ? 'a' : 'span'"
                         :href="person.old_greybox_id
                         ? `${config.oldGreyboxUrl}?page=clovek&clovek_id=${person.old_greybox_id}`
                         : undefined"
                        :target="person.old_greybox_id ? '_blank' : undefined">
                {{ person.name[0] }}.
                {{ person.surname.substring(0, 15) }}
              </component><template v-if="props.value.length - 1 !== index">,&nbsp;</template>
              <q-tooltip>
                {{ person.name }}
                {{ person.surname }}
              </q-tooltip>
            </span>
          </q-td>
        </template>
        <!-- Registrant body cell -->
        <template v-slot:body-cell-registrant="props">
          <q-td :props="props" class="small-overflow-column">
            <a :href="`mailto:${props.value.username}`">
              {{ props.value.person.name }}
              {{ props.value.person.surname }}
            </a>
          </q-td>
        </template>
        <!-- Warning body cell -->
        <template v-slot:body-cell-warnings="props">
          <q-td :props="props">
            <template v-if="!props.value.length">
              -
            </template>
            <span v-for="(warning, index) in props.value"
                  v-bind:key="index"
                  class="q-px-xs text-h6 text-negative"
            >
              <i class="fas fa-skull-crossbones"></i>
              <q-tooltip>
                {{ warning }}
              </q-tooltip>
            </span>
          </q-td>
        </template>
      </q-table>
    </div>
  </q-page>
</template>

<script lang="ts">

import {
  EventFull, EventTeam, EventTeamAggregate, EventTeamProper,
} from 'src/types/event';

import { defineComponent } from 'vue';
import config from '../../config';

export default defineComponent({
  name: 'EventTeams',
  computed: {
    event(): EventFull {
      // eslint-disable-next-line max-len
      // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
      return <EventFull> this.$store.getters['events/fullEvent'](this.eventId);
    },
    teams(): EventTeam[] {
      // eslint-disable-next-line max-len
      // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
      return <EventTeam[]> this.$store.getters['eventsTeams/eventTeams'](this.eventId);
    },
    properTeams(): EventTeamProper[] {
      return <EventTeamProper[]> this.teams.filter((team) => team.team !== null);
    },
    aggregateTeam(): EventTeamAggregate | undefined {
      return <EventTeamAggregate> this.teams.find((team) => team.team === null);
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
    await this.$store.dispatch('eventsTeams/load', this.eventId);
  },
  data() {
    return {
      translationPrefix: 'admin.eventRegistrations.',
      roleFilterModel: null,
      accommodationFilterModel: null,
      mealsFilterModel: null,
      config,
      columns: [{
        name: 'club', label: this.$tr('admin.eventRegistrations.labels.club'), field: (row: EventTeamProper) => row.team.institution ?? '-', sortable: true, align: 'left',
      }, {
        name: 'name', label: this.$tr('admin.eventRegistrations.labels.teamName'), field: (row: EventTeamProper) => row.team.name, sortable: true, align: 'left', classes: 'text-bold',
      }, {
        name: 'members', label: this.$tr('admin.eventRegistrations.labels.members'), field: 'members', sortable: false, align: 'left',
      }, {
        name: 'registrant', label: this.$tr('admin.eventRegistrations.labels.registrant'), field: 'registered_by', sortable: false, align: 'left',
      }, {
        name: 'warnings', label: this.$tr('admin.eventRegistrations.labels.note'), field: 'warnings', align: 'center', sortable: true, sort: (a: string[], b: string[]) => a.length - b.length,
      }],
      initialPagination: {
        sortBy: 'club',
        descending: false,
        rowsPerPage: 20,
      },
      tableLoading: false,
    };
  },
});
</script>

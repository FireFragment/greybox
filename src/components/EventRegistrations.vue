<template>
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
      :loading="tableLoading"
      color="primary"
    >
      <!-- Role header cell - filterable -->
      <template v-slot:header-cell-role="props">
        <q-th :props="props" class="filterable-table-heading">
          <q-select borderless v-model="roleFilterModel"
                    :options="uniqueRoles(registrations)"
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
      <!-- Accommodation header cell - filterable -->
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
      <!-- Meal type header cell - filterable -->
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
      <!-- Role body cell -->
      <template v-slot:body-cell-role="props">
        <q-td :props="props">
          <!-- Admin view - show role select to edit -->
          <q-select borderless :model-value="participantRoles[props.row.id]"
                    @update:model-value="(role) => changeParticipantRole(role, props.row.id)"
                    :options="applicableRoles"
                    option-value="id" :option-label="item => $tr(item.name, null, false)"
                    :dense="true" :options-dense="true"
                    :disable="tableLoading"
                    v-if="type === 'admin'"/>
          <!-- Non-edmin view - show static role -->
          <template v-else>
            {{ $tr(props.row.role.name) }}
          </template>
        </q-td>
      </template>
      <!-- Team body cell -->
      <template v-slot:body-cell-team="props">
        <q-td :props="props">
          <!-- Row is debater && admin view - show team select to edit -->
          <q-select borderless :model-value="props.row.team"
                    @update:model-value="(team) => changeParticipantTeam(team, props.row.id)"
                    :options="teams"
                    option-value="id" option-label="name"
                    :dense="true" :options-dense="true"
                    :disable="tableLoading"
                    v-if="type === 'admin' && props.row.role.id ===  config.debaterRoleId" />
          <!-- Row is not debater || non-admin view - show static team -->
          <template v-else>
            {{ props.value }}
          </template>
        </q-td>
      </template>
      <!-- Note body cell -->
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
</template>

<script lang="ts">
import { DietaryRequirement, EventRegistration } from 'src/types/event';
import { defineComponent } from 'vue';
import { Team } from 'src/types/debate';
import { Role } from 'src/types/role';
import { $tr } from 'boot/custom';
import config from 'src/config';
import { AxiosResponse } from 'axios';
import { getAllTranslations, TranslatedString } from 'boot/i18n';
import { langs } from 'src/translation/config';
import { EventsRegistrationsObjectType } from 'src/store/eventsRegistrations/state';

const booleanFilterOptions = [$tr('event.registrationsOverview.all'), $tr('event.registrationsOverview.yes'), $tr('event.registrationsOverview.no')];

const fakeRoleObject = (nameTrKey: string): Role => ({
  id: Infinity,
  icon: '',
  name: {
    id: Infinity,
    created_at: '',
    updated_at: '',
    ...getAllTranslations(nameTrKey),
  },
  slug: <TranslatedString><unknown> new Map(langs.map((lang) => ([lang, '']))),
  created_at: '',
  updated_at: '',
});

export const uniqueRoles = (registrations: EventRegistration[]): Role[] => {
  if (!registrations) {
    return [];
  }

  const roles: Role[] = (Object.values(registrations))
    .map((item) => item.role);
  const idsOnly = roles.map((item) => item.id);
  return [
    fakeRoleObject('event.registrationsOverview.all'),
    ...roles.filter((item, index) => idsOnly.indexOf(item.id) === index),
  ];
};

interface FilterObject {
  role: Role | null;
  accommodation: typeof booleanFilterOptions[number] | null;
  meals: (typeof booleanFilterOptions[number]) | null;
}

export default defineComponent({
  name: 'EventRegistrations',
  props: {
    eventId: Number,
    type: String as () => EventsRegistrationsObjectType,
  },
  computed: {
    registrations(): EventRegistration[] {
      // eslint-disable-next-line max-len
      // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
      return <EventRegistration[]> this.$store.getters['eventsRegistrations/eventRegistrations'](this.eventId, this.type);
    },
    filterObject(): FilterObject {
      return {
        role: this.roleFilterModel,
        accommodation: this.accommodationFilterModel,
        meals: this.mealsFilterModel,
      };
    },
    participantRoles(): Record<number, Role> {
      return Object.fromEntries(
        new Map(this.registrations.map(
          (registration: EventRegistration) => ([registration.id, registration.role]),
        )),
      );
    },
    applicableRoles(): Role[] {
      return [
        this.fakeRoleObject('event.types.organizer'),
        // eslint-disable-next-line max-len
        // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
        ...<Role[]> this.$store.getters['roles/eventRoles'](this.eventId),
      ];
    },
    teams(): Team[] {
      // eslint-disable-next-line max-len
      // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
      return <Team[]> this.$store.getters['eventsTeams/eventTeamsSimple'](this.eventId) ?? [];
    },
  },
  async created() {
    // Not cached -> load from API
    await this.$store.dispatch('events/loadFull', this.eventId); // for roles
    await this.$store.dispatch('roles/load');
    await this.$store.dispatch('eventsRegistrations/load', [this.eventId, this.type]);
    await this.$store.dispatch('eventsTeams/loadSimple', this.eventId);
  },
  data() {
    const outputBoolean = (val: boolean) => (val ? '✅' : '❌');
    const emptyToHyphen = (val: string | null) => (val ?? '-');
    const dietOrHyphen = (diet: DietaryRequirement | null) => (diet ? this.$tr(diet.name) : '-');
    return {
      config,
      translationPrefix: 'event.registrationsOverview.',
      roleFilterModel: null,
      accommodationFilterModel: null,
      mealsFilterModel: null,
      booleanFilterOptions,
      columns: [{
        name: 'surname', label: this.$tr('event.registrationsOverview.labels.surname'), field: (row: EventRegistration) => row.person.surname, sortable: true, align: 'left',
      }, {
        name: 'name', label: this.$tr('event.registrationsOverview.labels.name'), field: (row: EventRegistration) => row.person.name, sortable: true, align: 'left',
      }, {
        name: 'role', label: this.$tr('event.registrationsOverview.labels.role'), sortable: false, align: 'left',
      }, {
        name: 'team', label: this.$tr('event.registrationsOverview.labels.team'), field: (row: EventRegistration) => row.team?.name ?? '-', sortable: true, align: 'left',
      }, {
        name: 'note', label: this.$tr('event.registrationsOverview.labels.note'), field: 'note', format: emptyToHyphen, sortable: true, align: 'left',
      }, {
        name: 'accommodation', label: this.$tr('event.registrationsOverview.labels.accommodation'), field: 'accommodation', format: outputBoolean, sortable: false, align: 'center',
      }, {
        name: 'meals', label: this.$tr('event.registrationsOverview.labels.meals'), field: 'meals', format: outputBoolean, sortable: false, align: 'center',
      }, {
        name: 'dietary_requirements', label: this.$tr('event.registrationsOverview.labels.dietaryRequirements'), field: (row: EventRegistration) => row.person.dietary_requirement, format: dietOrHyphen, sortable: true, align: 'center',
      }],
      initialPagination: {
        sortBy: 'surname',
        descending: false,
        rowsPerPage: 20,
      },
      tableLoading: false,
    };
  },
  methods: {
    filterTableRows(rows: EventRegistration[], terms: FilterObject): EventRegistration[] {
      return rows.filter((item) => (
        (terms.role == null || terms.role.id === Infinity || terms.role.id === item.role.id)
        && (terms.accommodation == null || terms.accommodation === this.$tr('all') || ((terms.accommodation === this.$tr('yes')) === item.accommodation))
        && (terms.meals == null || terms.meals === this.$tr('all') || ((terms.meals === this.$tr('yes')) === item.meals))
      ));
    },
    changeParticipantTeam(team: Team, registrationId: number) {
      void this.updateParticipantData({
        team: team.id,
      }, registrationId)
        .then(() => {
          // Invalidate event teams for admin teams view
          this.$store.commit('eventsTeams/flushEventTeamsDetailed', this.eventId);
        });
    },
    changeParticipantRole(role: Role, registrationId: number) {
      void this.updateParticipantData({
        role: role.id !== Infinity ? role.id : 4,
      }, registrationId);
    },
    updateParticipantData(
      requestData: Record<string, number | boolean | string>,
      registrationId: number,
    ) {
      this.tableLoading = true;

      return this.$api({
        url: `registration/${registrationId}`,
        method: 'put',
        data: requestData,
      })
        .then(({
          data,
        }: AxiosResponse<EventRegistration>) => {
          this.$store.commit('eventsRegistrations/updateEventRegistration', {
            eventId: this.eventId,
            data,
          });
        })
        .finally(() => {
          this.tableLoading = false;
        });
    },
    uniqueRoles,
    fakeRoleObject,
  },
});

</script>

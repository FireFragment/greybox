<template>
  <q-page padding>
    <h1 class="text-center text-h4">{{ event ? $tr(event.name, null, false) : '-' }}</h1>
    <div class="q-px-md">
      <q-card class="horizontal-list-card">
        <q-card-section>
          <div class="text-subtitle2">{{ $tr("overview.rolesTitle") }}:</div>
        </q-card-section>
        <q-card-section class="q-pa-none">
          <q-list bordered separator>
            <q-item
              v-for="role in uniqueRoles"
              :key="role.id"
            >
              <q-item-section>
                <q-item-label caption>{{ $tr(role.name, null, false) }}</q-item-label>
                <q-item-label>
                  {{ roleRegistrationsCount(role) }}
                  <!-- Role "All" -->
                  <span v-if="role.id === Infinity">
                    ({{ accommodationCount }} {{ $tr("overview.accommodated") }})
                  </span>
                </q-item-label>
              </q-item-section>
            </q-item>
          </q-list>
        </q-card-section>
      </q-card>
    </div>
    <div class="q-pa-md">
      <q-card class="horizontal-list-card">
        <q-card-section>
          <div class="text-subtitle2">{{ $tr("overview.mealsTitle") }}:</div>
        </q-card-section>
        <q-card-section class="q-pa-none">
          <q-list bordered separator>
            <q-item
              v-for="[diet, count] in dietaryRequirements"
              :key="diet"
            >
              <q-item-section>
                <q-item-label caption class="text-capitalize-first-letter">
                  {{ $tr(diet) }}
                </q-item-label>
                <q-item-label>{{ count }}</q-item-label>
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
        :loading="tableLoading"
        color="primary"
      >
        <!-- Role header cell - filterable -->
        <template v-slot:header-cell-role2="props">
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
            <!-- Admin - show role select to edit -->
            <q-select borderless :model-value="participantRoles[props.row.id]"
                      @update:model-value="(role) => changeParticipantRole(role, props.row.id)"
                      :options="applicableRoles"
                      option-value="id" :option-label="item => $tr(item.name, null, false)"
                      :dense="true" :options-dense="true"
                      :disable="tableLoading"
                      v-if="$auth.isAdmin()" />
            <!-- Not admin, just organizer - show static role -->
            <template v-else>
              {{ props.value }}222222
            </template>
          </q-td>
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
import { EventFull, EventRegistration, DietaryRequirement } from 'src/types/event';
import { Role } from 'src/types/role';

import { defineComponent } from 'vue';
import { $tr } from 'boot/custom';
import { getAllTranslations, TranslatedString } from 'boot/i18n';
import { langs } from 'src/translation/config';
import { AxiosResponse } from 'axios';

const booleanFilterOptions = [$tr('admin.eventRegistrations.all'), $tr('admin.eventRegistrations.yes'), $tr('admin.eventRegistrations.no')];

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
    dietaryRequirements(): [(TranslatedString | string), number][] {
      if (!this.registrations) return [];

      type DietConstant = 'noMeals' | 'noRequirements';
      type Diet = DietaryRequirement | DietConstant;
      type DietAtomic = number | DietConstant; // diet ID or a constant

      // Get ID or a constant string of a diet requirement
      const dietAtomicValue = (diet: Diet): DietAtomic => (typeof diet === 'string' ? diet : diet.id);

      // Map registrations to diets
      const diets: Diet[] = this.registrations
        .map((reg) => (!reg.meals ? 'noMeals' : (reg.person.dietary_requirement ?? 'noRequirements')));

      // Get unique diets
      const dietIds: DietAtomic[] = diets.map(dietAtomicValue);
      const uniqueDiets: Diet[] = diets
        .filter((v, i) => dietIds.indexOf(typeof v === 'string' ? v : v.id) === i);

      return (
        // Get counts for all diets
        <[(TranslatedString | string), number][]>uniqueDiets
          .map((r) => [
            typeof r === 'string' ? `meals.${r}` : r.name,
            dietIds.filter((i) => dietAtomicValue(r) === i).length,
          ]))
        // Sort diets and counts
        .sort(([aDiet, aCount], [bDiet, bCount]) => {
          // No meals option at the beginning
          if (aDiet === 'noMeals') return -1;
          if (bDiet === 'noMeals') return 1;

          // Other string options (no requirements) after no meals
          if (typeof aDiet === 'string') return -1;
          if (typeof bDiet === 'string') return 1;

          // Sort other options (real DietaryRequirements) by count
          return bCount - aCount;
        });
    },
    event(): EventFull {
      // eslint-disable-next-line max-len
      // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
      return <EventFull> this.$store.getters['events/fullEvent'](this.eventId);
    },
    applicableRoles(): Role[] {
      return [
        this.fakeRoleObject('event.types.organizer'),
        // eslint-disable-next-line max-len
        // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
        ...<Role[]> this.$store.getters['roles/eventRoles'](this.eventId),
      ];
    },
    uniqueRoles(): Role[] {
      if (!this.registrations) {
        return [];
      }

      const roles: Role[] = (Object.values(this.registrations))
        .map((item) => item.role);
      const idsOnly = roles.map((item) => item.id);
      return [
        this.fakeRoleObject('admin.eventRegistrations.all'),
        ...roles.filter((item, index) => idsOnly.indexOf(item.id) === index),
      ].sort((a, b) => this.roleRegistrationsCount(b) - this.roleRegistrationsCount(a));
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
    participantRoles(): Record<number, Role> {
      return Object.fromEntries(
        new Map(this.registrations.map(
          (registration: EventRegistration) => ([registration.id, registration.role]),
        )),
      );
    },
    accommodationCount(): number {
      if (!this.registrations) {
        return 0;
      }

      return this.registrations.filter((item) => item.accommodation).length;
    },
  },
  async created() {
    // Not cached -> load from API
    await this.$store.dispatch('events/loadFull', this.eventId);
    await this.$store.dispatch('roles/load');
    await this.$store.dispatch('eventsRegistrations/load', this.eventId);
  },
  data() {
    const outputBoolean = (val: boolean) => (val ? '✅' : '❌');
    const emptyToHyphen = (val: string | null) => (val ?? '-');
    const dietOrHyphen = (diet: DietaryRequirement | null) => (diet ? this.$tr(diet.name) : '-');
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
        name: 'meals', label: this.$tr('admin.eventRegistrations.labels.meals'), field: 'meals', format: outputBoolean, sortable: false, align: 'center',
      }, {
        name: 'dietary_requirements', label: this.$tr('admin.eventRegistrations.labels.dietaryRequirements'), field: (row: EventRegistration) => row.person.dietary_requirement, format: dietOrHyphen, sortable: true, align: 'center',
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
    roleRegistrationsCount(role: Role): number {
      return this.registrations
        .filter((item) => role.id === Infinity || role.id === item.role.id).length;
    },
    fakeRoleObject: (nameTrKey: string): Role => ({
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
    }),
    changeParticipantRole(role: Role, registrationId: number) {
      this.tableLoading = true;

      this.$api({
        url: `registration/${registrationId}`,
        method: 'put',
        data: {
          role: role.id !== Infinity ? role.id : 4,
        },
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
  },
});
</script>

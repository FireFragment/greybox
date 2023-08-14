<template>
  <q-page padding>
    <h1 class="text-center text-h4">
      {{ event ? $tr(event.name, null, false) : '-' }} - {{ $tr("viewTypes.people") }}
    </h1>
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
    <event-registrations-overview v-if="event" :event="event" type="admin" />
  </q-page>
</template>

<script lang="ts">

import { EventRegistration, DietaryRequirement, EventFull } from 'src/types/event';
import { Role } from 'src/types/role';

import { defineComponent } from 'vue';
import { TranslatedString } from 'boot/i18n';
import EventRegistrationsOverview, { uniqueRoles } from 'src/components/EventRegistrations.vue';
import { $setTitle } from 'boot/custom';

export default defineComponent({
  name: 'EventRegistrations',
  components: {
    EventRegistrationsOverview,
  },
  computed: {
    registrations(): EventRegistration[] {
      // eslint-disable-next-line max-len
      // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
      return <EventRegistration[]> this.$store.getters['eventsRegistrations/eventRegistrations'](this.eventId, 'admin');
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
    uniqueRoles(): Role[] {
      // eslint-disable-next-line @typescript-eslint/no-unsafe-call
      return (<Role[]>uniqueRoles(this.registrations))
        .sort((a, b) => this.roleRegistrationsCount(b) - this.roleRegistrationsCount(a));
    },
    eventId(): number {
      const idParam: string | string[] = this.$route.params.id;
      if (typeof idParam !== 'string') {
        return 0;
      }
      return parseInt(idParam, 10);
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
    await Promise.all([
      this.$store.dispatch('events/loadFull', this.eventId),
      this.$store.dispatch('eventsRegistrations/load', [this.eventId, 'admin']),
    ]);
    $setTitle(`${<string> this.$tr(this.event.name)} - ${(<string> this.$tr('titles.admin.eventRegistrations', null, false)).toLowerCase()}`);
  },
  data() {
    return {
      translationPrefix: 'admin.events.',
    };
  },
  methods: {
    roleRegistrationsCount(role: Role): number {
      return this.registrations
        .filter((item) => role.id === Infinity || role.id === item.role.id).length;
    },
  },
});
</script>

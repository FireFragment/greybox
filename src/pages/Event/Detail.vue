<template>
  <div class="text-center" v-if="event">
    <q-card
      class="inline-block event-header"
    >
      <h1 class="text-center text-h4">
        {{ $tr('title') }} {{ $tr(event.name) }}
      </h1>
      <div
        class="text-center close-paragraphs q-p-1"
      >
        <p>
          <q-icon name="far fa-calendar-alt" class="text-primary"/>
          <EventDate :event="event" />
        </p>
        <p>
          <q-icon name="fas fa-landmark" class="text-primary"/>
          {{ event.place }}
        </p>
        <p>
          <q-icon name="far fa-bell" class="text-negative"/>
          {{ $tr('deadline') }}:
          {{ getDate(event.soft_deadline, 'D. M. YYYY H:mm') }}
        </p>

        <div v-if="$auth.organizesEvent(event.id)">
          <router-link
              :to="$translatedRouteLink({
              name: 'admin.editEvent',
              params: {
                id: event.id,
              },
            })">
            {{ $tr('admin.events.button.edit', null, false) }}
          </router-link>
          |
          <router-link
              :to="$translatedRouteLink({
              name: 'admin.editEventRoles',
              params: {
                id: event.id,
              },
            })">
            {{ $tr('admin.events.button.editRoles', null, false) }}
          </router-link>
          |
          <router-link
            :to="$translatedRouteLink({
              name: 'admin.eventRegistrations',
              params: {
                id: event.id,
              },
            })">
            {{ $tr('admin.events.button.people', null, false) }}
          </router-link>
          |
          <router-link
            :to="$translatedRouteLink({
              name: 'admin.eventTeams',
              params: {
                id: event.id,
              },
            })">
            {{ $tr('admin.events.button.teams', null, false) }}
          </router-link>
        </div>

        <q-btn
          v-if="event.hard_deadline >= now"
          class="hidden-link q-my-lg"
          :to="$translatedRouteLink({
            name: 'event-pick-type',
            params: {
              ...$route.params,
            }
          })"
          icon-right="fas fa-angle-right"
          :label="$tr('register')"
          color="primary"
          size="lg"
        />

        <p class="wysiwyg-content text-left" v-if="event.note" v-html="$tr(event.note)" />

        <q-btn
          v-if="event.note && event.hard_deadline >= now"
          class="hidden-link q-my-lg"
          :to="$translatedRouteLink({
            name: 'event-pick-type',
            params: {
              ...$route.params,
            }
          })"
          icon-right="fas fa-angle-right"
          :label="$tr('register')"
          color="primary"
          size="lg"
        />
      </div>
    </q-card>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { Event } from 'src/types/event';
import { date } from 'quasar';
import { TranslationPrefixData } from 'boot/i18n';
import EventDate from 'components/Event/EventDate.vue';
import { getNowFormatted } from 'src/router/helpers';

export default defineComponent({
  name: 'Detail',
  components: { EventDate },

  props: {
    eventId: {
      type: Number,
      required: true,
    },
  },

  data(): TranslationPrefixData {
    return {
      translationPrefix: 'event.',
    };
  },
  computed: {
    now() {
      return getNowFormatted();
    },
    event(): Event {
      // eslint-disable-next-line max-len
      // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
      return <Event> this.$store.getters['events/event'](this.eventId);
    },
  },

  methods: {
    getDate: date.formatDate,
  },
});
</script>

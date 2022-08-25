<template>
  <div class="text-center">
    <q-btn color="white" text-color="black" class="float-left q-mb-md go-back-button"
      @click="goBack()" v-if="!['event-pick-type', 'event-confirmation'].includes($route.name)">
      <q-icon size="1em" name="fas fa-arrow-left " />
    </q-btn>
    <q-card
      class="inline-block event-header"
      :class="{ smaller }"
    >
      <h1 class="text-center text-h4">
        {{ $tr('title') }} {{ $tr(event.name) }}
      </h1>
      <div
        class="text-center close-paragraphs q-p-1"
        v-if="!smaller"
      >
        <p>
          <q-icon name="far fa-calendar-alt" class="text-primary" />
          <template v-if="event.beginning.substr(0, 4) !== event.end.substr(0, 4)">
            <!-- Year is different -->
            {{ getDate(event.beginning, 'D. M. YYYY') }} - {{ getDate(event.end, 'D. M. YYYY') }}
          </template>
          <template v-else-if="event.beginning.substr(0, 7) !== event.end.substr(0, 7)">
            <!-- Month is different -->
            {{ getDate(event.beginning, 'D. M.') }} - {{ getDate(event.end, 'D. M. YYYY') }}
          </template>
          <template v-else-if="event.beginning !== event.end">
            <!-- Just day is different-->
            {{ getDate(event.beginning, 'D. M. YYYY') }} - {{ getDate(event.end, 'D. M. YYYY') }}
          </template>
          <template v-else>
            <!-- else - One day event -->
            {{ getDate(event.end, 'D. M. YYYY') }}
          </template>
        </p>
        <p>
          <q-icon name="fas fa-landmark" class="text-primary" />
          {{ event.place }}
        </p>
        <p>
          <q-icon name="far fa-bell" class="text-negative" />
          {{ $tr('deadline') }}:
          {{ getDate(event.soft_deadline, 'D. M. YYYY H:mm') }}
        </p>
        <p v-if="event.note">
          <q-icon name="fas fa-info" class="text-primary" />
          {{ $tr(event.note) }}
        </p>

        <router-link
          :to="
            $path('admin.events') +
              '/' +
              event.id +
              '-' +
              $slug($tr(event.name) + ' ' + event.place)
          "
          v-if="$auth.isAdmin() || $auth.user().organizedEventsIds?.includes(event.id)">
          Admin
        </router-link>
      </div>
    </q-card>
  </div>
</template>

<script lang="ts">
import { TranslationPrefixData } from 'boot/i18n';
import { Event } from 'src/types/event';
import { date } from 'quasar';
import { defineComponent } from 'vue';

const HeaderCardProps = {
  smaller: {
    type: Boolean,
    required: true,
  },
  event: {
    type: Object as () => Event,
    required: true,
  },
};

export default defineComponent({
  name: 'HeaderCard',
  props: HeaderCardProps,
  data(): TranslationPrefixData {
    return {
      translationPrefix: 'event.',
    };
  },
  methods: {
    getDate: date.formatDate,
    goBack: () => window.history.back(),
  },
});
</script>

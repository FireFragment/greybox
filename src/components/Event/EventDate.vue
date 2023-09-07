<template>
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
    {{ getDate(event.beginning, 'D.') }} - {{ getDate(event.end, 'D. M. YYYY') }}
  </template>
  <template v-else>
    <!-- else - One day event -->
    {{ getDate(event.end, 'D. M. YYYY') }}
  </template>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { PropType } from 'vue/dist/vue';
import { Event } from 'src/types/event';
import { date } from 'quasar';

export default defineComponent({
  name: 'EventDate',
  props: {
    event: {
      type: Object as PropType<Event>,
      required: false,
      default: null,
    },
  },

  methods: {
    getDate: date.formatDate,
  },
});
</script>

<template>
  <q-page padding>
    <div class="row">
      <template>
        <EventRegistrations :entry="registrations" />
      </template>
    </div>

  </q-page>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { AxiosResponse } from 'axios';
import { TranslationPrefixData } from 'boot/i18n';
import { EventPersonRegistrations } from 'src/types/event';
import EventRegistrations from 'components/EventRegistrations.vue';

interface SingleEventRegistrationsData extends TranslationPrefixData {
  translationPrefix: string;
  registrations: EventPersonRegistrations[];
}

export default defineComponent({
  name: 'SingleEventRegistrations',
  components: {
    EventRegistrations,
  },
  data(): SingleEventRegistrationsData {
    return {
      translationPrefix: 'user.',
      registrations: [],
    };
  },
  methods: {
    async loadRegistrations() {
      const eventId = String(this.$route.params.id);

      this.$bus.$emit('fullLoader', true);
      await this.$api({
        url: `event/${eventId}/user/${this.$auth.user()!.id}/registration`,
        method: 'get',
      })
        .then(({ data }: AxiosResponse<EventPersonRegistrations[]>) => {
          if (!data.length) {
            return;
          }
          this.registrations = data;
        });
      this.$bus.$emit('fullLoader', false);
    },
  },
  created() {
    void this.loadRegistrations();
  },
});
</script>

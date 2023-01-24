<template>
  <q-page padding>
    <div class="row">
        {{ registrations }}
        <!--<EventRegistrations :entry="registrations" />-->
    </div>
  </q-page>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { AxiosResponse } from 'axios';
import { TranslationPrefixData } from 'boot/i18n';
import { EventPersonRegistrations } from 'src/types/event';

interface SingleEventRegistrationsData extends TranslationPrefixData {
  translationPrefix: string;
  registrations: EventPersonRegistrations[];
}

export default defineComponent({
  name: 'MyRegistrationsDetail',
  components: {
    // EventRegistrations,
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

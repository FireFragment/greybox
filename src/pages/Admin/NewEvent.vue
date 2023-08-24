<template>
  <q-page padding>
    <h1 class="text-center text-h4">{{ $tr('admin.newEvent.link') }}</h1>
    <EventForm @submit="submit"/>
  </q-page>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import EventForm from 'components/Admin/EventForm.vue';

export default defineComponent({
  name: 'NewEvent',
  components: { EventForm },
  methods: {
    submit(formData: Record<string, string>) {
      this.$bus.$emit('fullLoader', true);
      this.$api({
        url: 'event',
        data: formData,
      }).then(async () => {
        // Reload simple events storage
        await this.$store.dispatch('events/load', [true, true]);
        // TODO - alert
        // TODO - redirect to roles (prices) form
      }).finally(() => {
        this.$bus.$emit('fullLoader', false);
      });
    },
  },
});
</script>

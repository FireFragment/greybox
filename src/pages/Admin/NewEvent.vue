<template>
  <q-page padding>
    <h1 class="text-center text-h4">{{ $tr('admin.newEvent.link') }}</h1>
    <EventForm/>
  </q-page>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import EventForm from 'components/Admin/EventForm.vue';

export default defineComponent({
  name: 'NewEvent',
  components: { EventForm },
});
</script>

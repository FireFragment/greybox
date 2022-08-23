<template>
  <q-page padding>
    <h1 class="text-center text-h4">{{ $tr("admin.eventRegistrations.chooseEvent") }}</h1>
    <q-btn
        class="block q-mx-auto q-mb-lg q-pa-md"
        icon="fas fa-trophy"
        color="primary"
        label="Turnaj #1"
    />
    <q-btn
        class="block q-mx-auto q-mb-lg q-pa-md"
        icon="fas fa-trophy"
        color="primary"
        label="Třetí turnaj XXV. ročníku Debatní ligy a Debate League"
    />
  </q-page>
</template>

<script>

export default {
  name: 'EventRegistrations',
  data() {
    return {};
  },
};
</script>

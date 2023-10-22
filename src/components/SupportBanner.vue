<template>
  <div class="col-12 col-sm-6 tw-mx-auto">
    <div class="q-mx-sm">
      <q-banner class="bg-primary text-white">
        <template v-slot:avatar>
          <q-icon name="fas fa-info" color="white" class="!tw-text-2xl tw-h-10"/>
        </template>
        {{ $tr('general.help', null, false) }}
        <a href="mailto:info@debatovani.cz" class="!tw-text-white">info@debatovani.cz</a>.
      </q-banner>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'SupportBanner',
});
</script>

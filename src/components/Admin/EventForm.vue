<template>
  <q-form>
    <div class="row q-col-gutter-md q-pb-sm">
      <TranslatableInput class="col-12" v-model="name" />

      <TranslatableInput class="col-12" v-model="note" type="wysiwyg" />
    </div>
  </q-form>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import TranslatableInput from 'components/Form/TranslatableInput.vue';

export default defineComponent({
  name: 'EventForm',
  components: { TranslatableInput },
  data() {
    return {
      name: {
        cs: 'Událost 1',
        en: 'Event 1',
      },
      beginning: '2019-08-21',
      end: '2019-08-23',
      place: 'Praha',
      soft_deadline: '2019-08-14 14:00:00',
      hard_deadline: '2019-08-16 20:00:00',
      note: {
        cs: 'Poznámka',
        en: 'Any comment',
      },
      invoice_cs: 'Fakturujeme vám...',
      invoice_en: 'Invoice for...',
    };
  },
});
</script>

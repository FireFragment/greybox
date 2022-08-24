<template>
  <!-- Individual or group -->
  <pick-type
    next-route="event-pick-role"
    name="type"
    :values="[{
        icon: 'user',
        color: 'btn-2',
        value: 'individual'
      },{
        icon: 'users',
        color: 'btn-3',
        value: 'group'
      }]"
  />
</template>

<script type="ts">
import { defineComponent } from 'vue';
import pickType from '../../components/Event/PickingButtons.vue';

export default defineComponent({
  name: 'PickTypePage',

  components: {
    pickType,
  },
});
</script>

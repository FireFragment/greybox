<template>
  <!-- Individual or group -->
  <picking-buttons
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

<script lang="ts">
import { defineComponent } from 'vue';
import pickingButtons from '../../components/Event/PickingButtons.vue';

export default defineComponent({
  name: 'PickType',

  components: {
    pickingButtons,
  },
});
</script>

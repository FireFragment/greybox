<template>
  <!-- Individual or group -->
  <picking-buttons
    next-route="event-pick-role"
    class="picking-role"
    name="type"
    :values="values"
  />
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { PickingButtonOptions } from 'components/Event/PickingButtons.vue';
import { Role } from 'src/types/role';
import pickingButtons from '../../components/Event/PickingButtons.vue';

export default defineComponent({
  name: 'PickRole',

  props: {
    eventId: {
      type: Number,
      required: true,
    },
  },

  components: {
    pickingButtons,
  },

  computed: {
    values(): PickingButtonOptions[] {
      // eslint-disable-next-line max-len
      // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
      return (<Role[]> this.$store.getters['roles/eventRoles'](this.eventId))
        .map((role: Role) => ({
          icon: role.icon,
          value: role.id,
          label: role.name,
        }));
    },
  },
});
</script>

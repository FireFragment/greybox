<template>
  <div>
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
    <q-btn
      v-if="$store.state.eventRegistrationForm[eventId]?.dataToSubmit.length"
      :label="$tr('event.buttons.goToCheckout')"
      type="reset"
      color="blue-9"
      class="q-mt-xl float-right"
      :to="$translatedRouteLink({
        name: 'event-checkout',
        params: {
          ...this.$route.params,
          type: $tr(`paths.eventParams.type.group`),
          checkout: this.$tr('paths.eventParams.checkout', null, false),
        }
      })"
    />
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import pickingButtons from '../../components/Event/PickingButtons.vue';

export default defineComponent({
  name: 'PickType',

  components: {
    pickingButtons,
  },

  computed: {
    eventId() {
      return this.$route.params.id;
    },
  },
});
</script>

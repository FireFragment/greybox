<template>
  <div class="row justify-center picking-buttons items-stretch q-mt-lg">
    <div class="col" v-if="values.length < 4"></div>
    <picking-button
      v-for="btn in options"
      v-bind:key="btn.value"
      :label="btn.label"
      :icon="btn.icon"
      :color="btn.color"
      @click="selectType(btn.value)"
      :auto-size="options.length > 4"
    />
    <div class="col" v-if="values.length < 4"></div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import pickingButton from './PickingButton.vue';

export default defineComponent({
  props: {
    values: [Array, Object],
    name: String,
    hideFirst: Boolean,
  },
  components: {
    pickingButton,
  },
  computed: {
    options() {
      return Object.values(this.values).filter((item) => !this.hideFirst || item.value !== 0);
    },
  },
  name: 'PickType',
  methods: {
    selectType(type: string) {
      // TODO - redirect to event-pick-role
      console.log(type, this.name, this.$route);

      void this.$router.push({
        name: 'event-pick-role',
        params: {
          ...this.$route.params,
          type,
        },
      });
    },
  },
});
</script>

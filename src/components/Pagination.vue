<template>
  <div class="col-12 q-pt-xl q-pb-lg flex flex-center" v-if="pages > 1">
    <q-pagination
      @update:modelValue="$emit('update:modelValue', $event)"
      :modelValue="modelValue"
      :max="pages"
      size="20px"
      color="primary"
      :max-pages="6"
      boundary-numbers
      :to-fn="page => $path(route) + '/' + page"
    />
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'Pagination',
  props: {
    modelValue: Number,
    pages: Number,
    route: {
      type: String,
      required: true,
    },
  },
  emits: ['update:modelValue'],
});
</script>

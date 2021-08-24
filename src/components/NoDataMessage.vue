<template>
  <div :class="{
    'col-6 offset-3': column,
    'q-px-sm': true,
  }">
    <q-banner class="bg-primary text-white q-mt-xl">
      <template v-slot:avatar>
        <q-icon name="fas fa-info" color="white" />
      </template>
      {{ message }}
    </q-banner>
  </div>
</template>

<script lang="ts">
const NoDataMessageProps = {
  message: {
    type: String,
    required: true,
  },
  column: {
    type: Boolean,
    required: false,
    default: true,
  },
};

export default {
  name: 'NoDataMessage',
  props: NoDataMessageProps,
};
</script>

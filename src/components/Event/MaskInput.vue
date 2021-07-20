<template>
  <q-input
    v-bind="$attrs"
    @focus="moveCaretToFront"
    @input="$emit('input', $event)"
  >
    <template v-slot:prepend>
      <slot name="prepend"></slot>
    </template>
    <template v-slot:append>
      <slot name="append"></slot>
    </template>
  </q-input>
</template>

<script>
export default {
  methods: {
    moveCaretToFront(event) {
      const input = event.target;

      if (input && typeof input.setSelectionRange === 'function') {
        input.setSelectionRange(0, 0);
        setTimeout(() => {
          input.setSelectionRange(0, 0);
        }, 50);
      } else {
        console.log(event);
        console.error('Missing setSelectionRange function, see logs above');
      }
    },
  },
};
</script>

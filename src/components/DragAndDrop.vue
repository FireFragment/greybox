<template>
  <!-- Prevent event listeners registration if drag&drop is not active -->
  <div
    v-on="active ? {
      dragenter: dragStart,
      dragover: dragStart,
      dragleave: dragEnd,
      drop: drop,
    } : {}"
    :class="{
      'drag-and-drop': active,
      'dragging': dragging,
    }"
    :data-dragging-text="active ? overlayText : null"
  >
    <slot></slot>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { QUploader } from 'quasar';

const DragAndDropProps = {
  overlayText: {
    type: String,
    required: true,
  },
  active: {
    type: Boolean,
    required: false,
    default: true,
  },
};

interface DragAndDropData {
  dragging: boolean;
  endBuffer: null | ReturnType<typeof setTimeout>;
}

export default defineComponent({
  name: 'DragAndDrop',
  props: DragAndDropProps,
  data(): DragAndDropData {
    return {
      dragging: false,
      endBuffer: null,
    };
  },
  methods: {
    dragStart(event: DragEvent) {
      event.preventDefault();
      this.dragging = true;

      if (this.endBuffer) {
        clearTimeout(this.endBuffer);
        this.endBuffer = null;
      }
    },
    dragEnd() {
      this.endBuffer = setTimeout(() => {
        this.dragging = false;
      }, 200);
    },
    drop(event: DragEvent) {
      event.preventDefault();
      this.dragging = false;

      if (!event.dataTransfer) {
        return;
      }

      const fileInput = this.$parent?.$refs.fileInput;

      if (!fileInput) {
        throw new Error('DragAndDrop requires parent to have QUploader as fileInput $ref');
      }

      (<QUploader>fileInput).addFiles(
        event.dataTransfer.files,
      );
    },
  },
});
</script>

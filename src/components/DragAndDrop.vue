<template>
  <div
    :class="{
      'drag-and-drop': true,
      'dragging': dragging,
    }"
    @dragenter="dragStart"
    @dragover="dragStart"
    @dragleave="dragEnd"
    @drop="drop"
    :data-dragging-text="overlayText"
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
        const error = 'DragAndDrop requires parent to have QUploader as fileInput $ref';
        console.error(error);
        throw new Error(error);
      }

      (<QUploader>fileInput).addFiles(
        event.dataTransfer.files,
      );
    },
  },
});
</script>

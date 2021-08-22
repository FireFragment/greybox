<template>
  <div class="row q-pa-lg">
    <div class="col-3 q-pa-sm" v-for="x in 10" v-bind:key="x">
      <DebateCard :victory="x % 3 === 2 ? null : !!(x % 3)" />
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, DefineComponent } from 'vue';
import DebateCard from '../components/MyDebates/DebateCard';

export default defineComponent({
  name: 'MyDebates',
  components: { DebateCard: <DefineComponent>DebateCard },
});
</script>

<template>
  <div class="row q-pa-lg">
    <div class="col-12 col-sm-6 col-md-4 col-lg-3 q-pa-sm" v-for="x in 10" v-bind:key="x">
      <DebateCard class="full-width" :victory="x % 3 === 2 ? null : !!(x % 3)"
                  :adjudicator="x % 3 === 2" />
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

<template>
  <q-page padding>
    <h1 class="text-center text-h4">{{ $tr('myDebates.title') }}</h1>
    <div class="row">
      <div class="col-12 q-px-sm">
        <h5 class="q-mt-lg q-mb-xs">Prosinec 2020</h5>
      </div>
      <div class="col-12 col-sm-6 col-md-4 col-lg-3 q-px-sm q-py-md" v-for="x in 3" v-bind:key="x">
        <DebateCard class="full-width" :victory="x % 3 === 2 ? null : !!(x % 3)"
                    :adjudicator="x % 3 === 2" />
      </div>
      <div class="col-12 q-px-sm">
        <h5 class="q-mt-lg q-mb-xs">Listopad 2020</h5>
      </div>
      <div class="col-12 col-sm-6 col-md-4 col-lg-3 q-px-sm q-py-md" v-for="x in 10" v-bind:key="x">
        <DebateCard class="full-width" :victory="x % 3 === 2 ? null : !!(x % 3)"
                    :adjudicator="x % 3 === 2" />
      </div>
    </div>
  </q-page>
</template>

<script lang="ts">
import { defineComponent, DefineComponent } from 'vue';
import DebateCard from '../components/MyDebates/DebateCard';

export default defineComponent({
  name: 'MyDebates',
  components: { DebateCard: <DefineComponent>DebateCard },
});
</script>

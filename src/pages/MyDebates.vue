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
      <Pagination v-model="currentPage" :pages="10" route="myDebates" />
    </div>
  </q-page>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import Pagination from '../components/Pagination.vue';
import DebateCard from '../components/MyDebates/DebateCard.vue';

interface MyDebatesData {
  currentPage: number;
}

export default defineComponent({
  name: 'MyDebates',
  components: {
    Pagination: <never>Pagination,
    DebateCard: <never>DebateCard,
  },
  data(): MyDebatesData {
    return {
      currentPage: 6,
    };
  },
  methods: {
    loadPage(pageParam: string | string[]) {
      if (typeof pageParam !== 'string') {
        return;
      }

      this.$bus.$emit('fullLoader', true);
      const pageParamInt = parseInt(pageParam, 10);
      const page = pageParamInt > 0 ? pageParamInt : 1;

      this.currentPage = page;

      setTimeout(() => {
        this.$api({
          url: `user/${this.$auth.user()!.id}/debate`,
          method: 'get',
        })
          .then((data) => {
            console.log(data);
          })
          .catch(() => {
            // eslint-disable-next-line @typescript-eslint/no-unsafe-call
            // this.$flash(this.$tr('removeModal.person.error'), 'error');
          })
          .finally(() => {
            this.$bus.$emit('fullLoader', false);
          });
      }, 2000);
    },
  },
  created() {
    this.loadPage(this.$route.params.page);
  },
});
</script>

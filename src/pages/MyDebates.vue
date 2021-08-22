<template>
  <q-page padding>
    <h1 class="text-center text-h4">{{ $tr('myDebates.title') }}</h1>
    <div class="row">
      <template v-for="(month, key) in debatesData" :key="key">
        <div class="col-12 q-px-sm">
          <h5 class="q-mt-lg q-mb-xs">{{ $tr(month.month) }} {{ month.year }}</h5>
        </div>
        <div
          class="col-12 col-sm-6 col-md-4 col-lg-3 q-px-sm q-py-md"
          v-for="(debate, index) in month.debates"
          :key="index"
        >
          <DebateCard class="full-width" :debate="debate" />
        </div>
      </template>
      <Pagination v-model="currentPage" :pages="10" route="myDebates" />
    </div>
  </q-page>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { TranslatedString } from 'boot/i18n';
import Pagination from '../components/Pagination.vue';
import DebateCard, { Debate } from '../components/MyDebates/DebateCard.vue';

type DebatesData = Record<string, {
  debates: Debate[]
  month: TranslatedString,
  year: number
}>;

interface MyDebatesData {
  currentPage: number;
  debatesData: DebatesData;
}

// eslint-disable-next-line @typescript-eslint/no-explicit-any
function assertDebatesData(value: any): asserts value is DebatesData {
  if (typeof value !== 'object') {
    throw new TypeError('Invalid API data');
  }

  Object.values(value)
    .forEach((month) => {
      if (
        typeof month !== 'object'
        || !month
        || !('debates' in month)
        || !('month' in month)
        || !('year' in month)
      ) {
        throw new TypeError('Invalid API data');
      }
    });
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
      debatesData: {},
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
          .then(({ data }) => {
            assertDebatesData(data);
            this.debatesData = data;
          })
          .catch(() => {
            this.$flash(this.$tr('removeModal.person.error'), 'error');
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

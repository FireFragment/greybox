<template>
  <q-page padding>
    <h1 class="text-center text-h4">{{ $tr('title') }}</h1>
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
      <Pagination v-model="currentPage" :pages="totalPages" route="myDebates" />
    </div>
  </q-page>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { TranslatedString, TranslationPrefixData } from 'boot/i18n';
import { assertDBValue, DBValue } from 'boot/custom';
import { AxiosResponse } from 'axios';
import Pagination from '../components/Pagination.vue';
import DebateCard, { Debate } from '../components/MyDebates/DebateCard.vue';

type DebatesData = Record<string, {
  debates: Debate[]
  month: TranslatedString,
  year: number
}>;

interface MyDebatesData extends TranslationPrefixData {
  currentPage: number;
  totalPages: number;
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
      currentPage: 1,
      totalPages: 1,
      debatesData: {},
      translationPrefix: 'myDebates.',
    };
  },
  methods: {
    loadPage(pageParam: string | string[]) {
      if (typeof pageParam !== 'string') {
        return;
      }

      const pageParamInt = parseInt(pageParam, 10);
      const page = pageParamInt > 0 ? pageParamInt : 1;
      const DBkey = `my-debates-page${page}`;

      this.currentPage = page;

      const cached: DBValue = this.$db(DBkey);
      if (cached) {
        assertDebatesData(cached);
        this.debatesData = cached;
        return;
      }

      this.$bus.$emit('fullLoader', true);
      this.$api({
        url: `user/${this.$auth.user()!.id}/debate?page=${page}`,
        method: 'get',
      })
        .then(({
          data: {
            data,
            lastPage,
          },
        }: AxiosResponse<{
          data: DebatesData,
          lastPage: number,
        }>) => {
          this.debatesData = data;
          assertDBValue(data);
          this.$db(DBkey, data);
          this.totalPages = lastPage;
        })
        .finally(() => {
          this.$bus.$emit('fullLoader', false);
        });
    },
  },
  created() {
    this.loadPage(this.$route.params.page);
  },
});
</script>

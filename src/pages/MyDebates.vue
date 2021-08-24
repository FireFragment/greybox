<template>
  <q-page padding>
    <h1 class="text-center text-h4">{{ $tr('title') }}</h1>
    <div class="row">
      <NoDataMessage
        v-if="Object.keys(this.debatesData).length === 0"
        :message="$tr('empty')"
      />
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
import { $tr, DBValue } from 'boot/custom';
import { AxiosError, AxiosResponse } from 'axios';
import NoDataMessage from 'components/NoDataMessage.vue';
import Pagination from 'components/Pagination.vue';
import DebateCard, { Debate } from 'components/MyDebates/DebateCard.vue';

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

export default defineComponent({
  name: 'MyDebates',
  components: {
    Pagination: <never>Pagination,
    DebateCard: <never>DebateCard,
    NoDataMessage: <never>NoDataMessage,
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
      const DBkeyData = `my-debates-page${page}`;
      const DBkeyPages = 'my-debates-total-pages';

      this.currentPage = page;

      const cached: DBValue = this.$db(DBkeyData);
      if (cached) {
        this.debatesData = <DebatesData><unknown>cached;
        this.totalPages = <number> this.$db(DBkeyPages);
        return;
      }

      this.$bus.$emit('fullLoader', true);
      this.$api({
        url: `user/${this.$auth.user()!.id}/debate?page=${page}`,
        method: 'get',
        alerts: false,
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
          this.$db(DBkeyData, <DBValue><unknown>data, true);
          this.totalPages = lastPage;
          this.$db(DBkeyPages, lastPage, true);
        })
        .catch(({ response }: AxiosError) => {
          if (response && response.status === 404) {
            // Just no debates found
            this.$db(DBkeyData, {}, true);
          } else {
            // Actual error
            this.$flash($tr('general.error', null, false), 'error');
          }
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

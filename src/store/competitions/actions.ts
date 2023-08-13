import { Action } from 'vuex';
import { AxiosResponse } from 'axios';
import { apiCall } from 'boot/api';
import { bus } from 'boot/eventBus';
import { CompetitionsState } from 'src/store/competitions/state';
import { Competition } from 'src/types/event';

export const load: Action<CompetitionsState, never> = async ({
  commit,
  state,
}) => {
  if (state.loaded || state.loading) {
    return;
  }

  commit('startLoadingCompetitions');
  bus.$emit('fullLoader', true);
  await apiCall({
    url: 'competition',
    method: 'get',
  })
    .then(({ data }: AxiosResponse<Competition[]>) => {
      commit('setCompetitions', data);
    })
    .finally(() => {
      bus.$emit('fullLoader', false);
    });
};

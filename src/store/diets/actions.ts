import { Action } from 'vuex';
import { AxiosResponse } from 'axios';
import { apiCall } from 'boot/api';
import { bus } from 'boot/eventBus';
import { DietaryRequirement } from 'src/types/event';
import { DietsState } from './state';

export const load: Action<DietsState, never> = async ({
  commit,
  state,
}) => {
  if (state.loaded || state.loading) {
    return;
  }

  commit('startLoadingDiets');
  bus.$emit('fullLoader', true);
  await apiCall({
    url: 'dietaryrequirement',
    method: 'get',
  })
    .then(({ data }: AxiosResponse<DietaryRequirement[]>) => {
      commit('setDiets', data);
    })
    .finally(() => {
      bus.$emit('fullLoader', false);
    });
};

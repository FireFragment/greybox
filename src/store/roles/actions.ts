import { Action } from 'vuex';
import { AxiosResponse } from 'axios';
import { apiCall } from 'boot/api';
import { Role, RolesState } from './state';

export const load: Action<RolesState, never> = async ({
  commit,
  state,
}) => {
  if (state.loaded || state.loading) {
    return;
  }

  commit('startLoadingRoles');

  await apiCall({
    url: 'role',
    method: 'get',
  })
    .then(({ data }: AxiosResponse<Role[]>) => {
      commit('setRoles', data);
    });
};

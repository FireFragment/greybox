import { Action } from 'vuex';
import { AxiosResponse } from 'axios';
import { apiCall } from 'boot/api';
import { RoleRaw } from 'src/types/role';
import { RolesState } from './state';

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
    .then(({ data }: AxiosResponse<RoleRaw[]>) => {
      commit('setRoles', data);
    });
};

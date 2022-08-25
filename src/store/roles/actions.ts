import { Action } from 'vuex';
import { AxiosResponse } from 'axios';
import { apiCall } from 'boot/api';
import { RoleRaw } from 'src/types/role';
import { bus } from 'boot/eventBus';
import { RolesState } from './state';

export const load: Action<RolesState, never> = async ({
  commit,
  state,
}) => {
  if (state.loaded || state.loading) {
    return;
  }

  commit('startLoadingRoles');
  bus.$emit('fullLoader', true);
  await apiCall({
    url: 'role',
    method: 'get',
  })
    .then(({ data }: AxiosResponse<RoleRaw[]>) => {
      commit('setRoles', data);
    })
    .finally(() => {
      bus.$emit('fullLoader', false);
    });
};

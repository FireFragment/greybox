import { Mutation } from 'vuex';
import { RolesData, RolesState } from './state';

export const startLoadingRoles: Mutation<RolesState> = (state) => {
  state.loading = true;
};

export const setRoles: Mutation<RolesState> = (state, value: RolesData) => {
  state.roles = value;
  state.loaded = true;
  state.loading = false;
};

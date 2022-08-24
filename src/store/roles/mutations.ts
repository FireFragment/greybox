import { Mutation } from 'vuex';
import { RoleRaw } from 'src/types/role';
import { $slugTranslation } from 'boot/custom';
import { RolesState } from './state';

export const startLoadingRoles: Mutation<RolesState> = (state) => {
  state.loading = true;
};

export const setRoles: Mutation<RolesState> = (state, value: RoleRaw[]) => {
  state.roles = value.map((role) => ({
    ...role,
    slug: $slugTranslation(role.name),
  }));
  state.loaded = true;
  state.loading = false;
};

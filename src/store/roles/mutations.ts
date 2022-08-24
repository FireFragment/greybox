import { Mutation } from 'vuex';
import { RoleRaw } from 'src/types/role';
import { $slug } from 'boot/custom';
import { RolesState } from './state';

export const startLoadingRoles: Mutation<RolesState> = (state) => {
  state.loading = true;
};

export const setRoles: Mutation<RolesState> = (state, value: RoleRaw[]) => {
  state.roles = value.map((role) => ({
    ...role,
    slug: {
      cs: $slug(role.name.cs),
      en: $slug(role.name.en),
    },
  }));
  state.loaded = true;
  state.loading = false;
};

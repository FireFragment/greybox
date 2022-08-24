import { Mutation } from 'vuex';
import { RoleRaw } from 'src/types/role';
import { $slugTranslation, $tr } from 'boot/custom';
import { RolesState } from './state';

export const startLoadingRoles: Mutation<RolesState> = (state) => {
  state.loading = true;
};

export const setRoles: Mutation<RolesState> = (state, value: RoleRaw[]) => {
  const teamName = {
    id: Infinity,
    created_at: '',
    updated_at: '',
    cs: <string>$tr('event.types.team', null, false, 'cs'),
    en: <string>$tr('event.types.team', null, false, 'en'),
  };

  state.roles = [
    {
      id: Infinity,
      name: teamName,
      slug: $slugTranslation(teamName),
      icon: 'users',
      created_at: '',
      updated_at: '',
    },
    ...value.map((role) => ({
      ...role,
      slug: $slugTranslation(role.name),
    })),
  ];
  state.loaded = true;
  state.loading = false;
};

import { Role, RolesState } from 'src/store/roles/state';

export const role = (state: RolesState) => (id: number): Role | undefined => state.roles
  .find((item) => (item.id === id));

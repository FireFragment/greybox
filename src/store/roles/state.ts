import { Role } from 'src/types/role';

export type RolesData = Role[];

export interface RolesState {
  roles: RolesData;
  loading: boolean;
  loaded: boolean;
}

export default (): RolesState => ({
  roles: [],
  loading: false,
  loaded: false,
});

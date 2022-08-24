import { DateTime } from 'src/types/general';
import { TranslatedDatabaseString, TranslatedString } from 'boot/i18n';

export interface RoleRaw {
  // eslint-disable-next-line camelcase
  created_at: DateTime;
  icon: string;
  id: number;
  name: TranslatedDatabaseString;
  // eslint-disable-next-line camelcase
  updated_at: DateTime;
}

export interface Role extends RoleRaw {
  slug: TranslatedString;
}

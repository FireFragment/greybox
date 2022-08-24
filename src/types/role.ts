import { DateTime } from 'src/types/general';
import { TranslatedDatabaseString } from 'boot/i18n';

export interface Role {
  // eslint-disable-next-line camelcase
  created_at: DateTime;
  icon: string;
  id: number;
  name: TranslatedDatabaseString;
  // eslint-disable-next-line camelcase
  updated_at: DateTime;
}

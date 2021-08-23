import { DateTime } from 'src/types/general';

export interface EventRole {
  // eslint-disable-next-line camelcase
  created_at: DateTime;
  icon: string;
  id: number;
  name: number;
  // eslint-disable-next-line camelcase
  updated_at: DateTime;
}

export interface EventPrice {
  amount: string;
  // eslint-disable-next-line camelcase
  created_at: DateTime;
  currency: string;
  description: number;
  event: number;
  id: number;
  role: EventRole;
  // eslint-disable-next-line camelcase
  updated_at: DateTime;
}

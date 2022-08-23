import { Date, DateTime } from 'src/types/general';
import { TranslatedDatabaseString } from 'boot/i18n';
import { Role } from 'src/types/role';
import { Team } from 'src/types/debate';

type EventOptionalSelect = 'opt-in' | 'opt-out' | 'none' | 'required';

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

export interface Event {
  accommodation: EventOptionalSelect;
  beginning: Date;
  // eslint-disable-next-line camelcase
  created_at: DateTime;
  // eslint-disable-next-line camelcase
  email_required: boolean;
  end: Date;
  // eslint-disable-next-line camelcase
  hard_deadline: DateTime;
  id: number;
  // eslint-disable-next-line camelcase
  invoice_text: TranslatedDatabaseString;
  meals: EventOptionalSelect;
  // eslint-disable-next-line camelcase
  membership_required: boolean;
  name: TranslatedDatabaseString;
  note: TranslatedDatabaseString | null;
  pds: boolean;
  place: string;
  // eslint-disable-next-line camelcase
  soft_deadline: DateTime;
  // eslint-disable-next-line camelcase
  updated_at: DateTime;
  fullyLoaded: boolean;
}

export interface EventFull extends Event {
  dietaryRequirements: string[];
  prices: EventPrice[];
}

export interface Person {
  id: number;
  name: string;
  surname: string;
  email: string;
  institution: string;
  // eslint-disable-next-line camelcase
  old_greybox_id: number;
  birthdate: Date;
  // eslint-disable-next-line camelcase
  dietary_requirement: string;
  // eslint-disable-next-line camelcase
  id_number: string;
  street: string;
  city: string;
  zip: string;
  school: string;
  // eslint-disable-next-line camelcase
  school_year: number;
  // eslint-disable-next-line camelcase
  speaker_status: string;
  note: string;
  // eslint-disable-next-line camelcase
  created_at: DateTime;
  // eslint-disable-next-line camelcase
  updated_at: DateTime;
}

export interface EventRegistration {
  id: number;
  person: Person;
  note: string;
  event: number;
  role: Role;
  accommodation: boolean;
  meals: boolean;
  confirmed: boolean;
  team: Team;
  // eslint-disable-next-line camelcase
  registered_by: number;
  invoice: number;
  // eslint-disable-next-line camelcase
  created_at: DateTime;
  // eslint-disable-next-line camelcase
  updated_at: DateTime;
}

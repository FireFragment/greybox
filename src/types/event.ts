import { Date, DateTime } from 'src/types/general';
import { TranslatedDatabaseString, TranslatedString } from 'boot/i18n';
import { Role } from 'src/types/role';
import { Team } from 'src/types/debate';
import { Person, User } from 'boot/auth';

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

export interface DietaryRequirement {
  id: number;
  name: TranslatedDatabaseString;
  order: number;
  // eslint-disable-next-line camelcase
  created_at: DateTime;
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
  note: TranslatedString | null;
}

export interface Event {
  accommodation: EventOptionalSelect;
  beginning: Date;
  // eslint-disable-next-line camelcase
  created_at: DateTime;
  current?: boolean;
  // eslint-disable-next-line camelcase
  email_required: boolean;
  end: Date;
  // eslint-disable-next-line camelcase
  hard_deadline: DateTime;
  id: number;
  // eslint-disable-next-line camelcase
  invoice_text: TranslatedDatabaseString;
  meals: EventOptionalSelect;
  novices: boolean;
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
  dietaryRequirements: DietaryRequirement[];
  prices: EventPrice[];
}

export interface EventRegistration {
  id: number;
  person: Person;
  note: string;
  event: number;
  role: Role;
  accommodation: boolean;
  meals: boolean;
  novice: boolean;
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

export interface DetailedEventRegistration {
  id: number;
  person: Person;
  note: string;
  event: Event;
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

export interface EventPersonRegistrations {
  event: EventFull;
  registrations: EventRegistration[];
}

export interface EventTeam {
  team: Team;
  members: Person[];
  // eslint-disable-next-line camelcase
  registered_by: User;
  warnings: string[];
}

import { Date, DateTime } from 'src/types/general';
import { TranslatedDatabaseString } from 'boot/i18n';
import { EventPrice } from 'src/types/event';

type EventOptionalSelect = 'opt-in' | 'opt-out' | 'none' | 'required';

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
}

export interface EventFull extends Event {
  dietaryRequirements: string[];
  prices: EventPrice[];
}

export type EventsData = {
  [key: number]: Event
};

export type EventsFullData = {
  [key: number]: EventFull
};

export interface EventsState {
  events: EventsData;
  eventsFull: EventsFullData;
  loading: boolean;
  loaded: boolean;
}

export default (): EventsState => ({
  events: {},
  eventsFull: {},
  loading: false,
  loaded: false,
});

import { Date, DateTime } from 'src/types/general';
import { TranslatedDatabaseString } from 'boot/i18n';

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
  note: string | null;
  pds: boolean;
  place: string;
  // eslint-disable-next-line camelcase
  soft_deadline: DateTime;
}

export type EventsData = {
  [key: number]: Event
};

export interface EventsState {
  events: EventsData;
  loading: boolean;
  loaded: boolean;
}

export default (): EventsState => ({
  events: [],
  loading: false,
  loaded: false,
});

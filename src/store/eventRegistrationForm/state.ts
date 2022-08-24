import { Date } from 'src/types/general';

export interface EventRegistrationPerson {
  name: string | null;
  surname: string | null;
  // eslint-disable-next-line camelcase
  dietary_requirement: number | null,
  email?: string;
  // eslint-disable-next-line camelcase
  school_year?: number;
  birthdate?: Date | null;
  // eslint-disable-next-line camelcase
  id_number?: string | null;
  street?: string | null;
  city?: string | null;
  zip?: string | null;
  // eslint-disable-next-line camelcase
  speaker_status?: string | null;
}

export interface EventRegistrationRegistration {
  person: null;
  event: number;
  role: number;
  accommodation: boolean;
  meals: boolean;
  team?: number;
  teamName?: string;
  note: string;
}

export interface EventRegistrationAutofillState {
  id: number;
  edited: boolean;
}

export interface EventRegistrationItem {
  person: EventRegistrationPerson;
  registration: EventRegistrationRegistration;
  autofill: EventRegistrationAutofillState | false;
}

export interface EventRegistrationFormState {
  dataToSubmit: EventRegistrationItem[]
}

export default (): EventRegistrationFormState => ({
  dataToSubmit: [],
});

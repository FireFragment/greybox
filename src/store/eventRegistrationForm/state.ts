import { Date, DateTime } from 'src/types/general';

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
  person: number | null;
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

export interface InvoiceLine {
  name: string;
  quantity: number;
  // eslint-disable-next-line camelcase
  unit_name: string;
  // eslint-disable-next-line camelcase
  unit_price: string;
}

export interface Invoice {
  // eslint-disable-next-line camelcase
  taxable_fulfillment_due: Date;
  client: number;
  number: string;
  status: string;
  // eslint-disable-next-line camelcase
  issued_on: Date;
  // eslint-disable-next-line camelcase
  due_on: Date;
  currency: string;
  language: string;
  total: string;
  // eslint-disable-next-line camelcase
  paid_amount: string;
  // eslint-disable-next-line camelcase
  updated_at: DateTime;
  // eslint-disable-next-line camelcase
  created_at: DateTime;
  id: number;
  // eslint-disable-next-line camelcase
  qr_url: string;
  // eslint-disable-next-line camelcase
  qr_full_url: string;
  // eslint-disable-next-line camelcase
  pdf_url: string;
  // eslint-disable-next-line camelcase
  pdf_full_url: string;
}

export interface BillingClient {
  id: number;
  name: string;
  street: string | null;
  street2: string | null;
  city: string | null;
  zip: string | null;
  country: string;
  // eslint-disable-next-line camelcase
  registration_no: string | null;
  // eslint-disable-next-line camelcase
  full_name: string | null;
  email: string | null;
  user: number;
  // eslint-disable-next-line camelcase
  created_at: DateTime;
  // eslint-disable-next-line camelcase
  updated_at: DateTime;
}

export interface EventRegistrationConfirmation {
  invoiceLines: InvoiceLine[];
  totalAmount: number;
  client: BillingClient | null;
  invoice: Invoice | null;
}

export interface EventRegistrationFormState {
  dataToSubmit: EventRegistrationItem[];
  confirmation: EventRegistrationConfirmation | null;
}

export default (): EventRegistrationFormState => ({
  dataToSubmit: [],
  confirmation: null,
});

import { Mutation } from 'vuex';
import {
  EventRegistrationConfirmation,
  EventRegistrationFormState,
  EventRegistrationItem,
} from 'src/store/eventRegistrationForm/state';

export const addEventRegistration: Mutation<EventRegistrationFormState> = (
  state, value: EventRegistrationItem,
) => {
  state.dataToSubmit.push(value);
};

export const setRegistrationPersonId: Mutation<EventRegistrationFormState> = (
  state, value: {
    registrationIndex: number,
    personId: number,
  },
) => {
  state.dataToSubmit[value.registrationIndex].registration.person = value.personId;
};

export const setRegisteredData: Mutation<EventRegistrationFormState> = (
  state, value: {
    registrationIndex: number,
    data: Record<string, never>,
  },
) => {
  state.dataToSubmit[value.registrationIndex].registered_data = value.data;
};

export const removeEventRegistration: Mutation<EventRegistrationFormState> = (
  state, index: number,
) => {
  state.dataToSubmit.splice(index, 1);
};

export const confirmRegistration: Mutation<EventRegistrationFormState> = (
  state, confirmation: EventRegistrationConfirmation,
) => {
  state.dataToSubmit = [];
  state.confirmation = confirmation;
};

export const flushEventRegistrationForms: Mutation<EventRegistrationFormState> = (
  state,
) => {
  state.dataToSubmit = [];
  state.confirmation = null;
};

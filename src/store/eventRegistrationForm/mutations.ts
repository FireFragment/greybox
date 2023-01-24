import { Mutation } from 'vuex';
import {
  EventRegistrationConfirmation,
  EventRegistrationFormState,
  EventRegistrationItem,
} from 'src/store/eventRegistrationForm/state';

export const addEventRegistration: Mutation<EventRegistrationFormState> = (
  state, [eventId, value]: [number, EventRegistrationItem],
) => {
  if (!state[eventId]) {
    state[eventId] = {
      dataToSubmit: [],
      confirmation: null,
    };
  }

  state[eventId].dataToSubmit.push(value);
};

export const setRegistrationPersonId: Mutation<EventRegistrationFormState> = (
  state, [eventId, value]: [number, {
    registrationIndex: number,
    personId: number,
  }],
) => {
  state[eventId].dataToSubmit[value.registrationIndex].registration.person = value.personId;
};

export const setRegisteredData: Mutation<EventRegistrationFormState> = (
  state, [eventId, value]: [number, {
    registrationIndex: number,
    data: Record<string, never>,
  }],
) => {
  state[eventId].dataToSubmit[value.registrationIndex].registered_data = value.data;
};

export const removeEventRegistration: Mutation<EventRegistrationFormState> = (
  state, [eventId, index]: [number, number],
) => {
  state[eventId].dataToSubmit.splice(index, 1);
};

export const confirmRegistration: Mutation<EventRegistrationFormState> = (
  state, [eventId, confirmation]: [number, EventRegistrationConfirmation],
) => {
  state[eventId] = {
    dataToSubmit: [],
    confirmation,
  };
};

export const flushAllEventRegistrationForms: Mutation<EventRegistrationFormState> = (
  state,
) => {
  Object.assign(state, {});
};

export const removeDietaryRequirements: Mutation<EventRegistrationFormState> = (
  state, [eventId, index]: [number, number],
) => {
  delete state[eventId].dataToSubmit[index].person.dietary_requirement;
};

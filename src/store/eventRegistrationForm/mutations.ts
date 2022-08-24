import { Mutation } from 'vuex';
import {
  EventRegistrationFormState,
  EventRegistrationItem,
} from 'src/store/eventRegistrationForm/state';

export const addEventRegistration: Mutation<EventRegistrationFormState> = (
  state, value: EventRegistrationItem,
) => {
  state.dataToSubmit.push(value);
};

export const removeEventRegistration: Mutation<EventRegistrationFormState> = (
  state, index: number,
) => {
  state.dataToSubmit.splice(index, 1);
};

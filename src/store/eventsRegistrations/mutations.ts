import { Mutation } from 'vuex';
import { EventRegistration } from 'src/types/event';
import {
  eventRegistraionObjectTypes,
  EventsRegistrationsObjectType,
  EventsRegistrationsState,
} from './state';

export const startLoadingEventsRegistrations: Mutation<EventsRegistrationsState> = (
  state,
  type: EventsRegistrationsObjectType,
) => {
  state[type].loading = true;
};

export const setEventRegistrations: Mutation<EventsRegistrationsState> = (
  state, value: {
    eventId: number,
    data: EventRegistration[],
    type: EventsRegistrationsObjectType,
  },
) => {
  const { eventId, data, type } = value;
  state[type].events[eventId] = data;
  state[type].loading = false;
};

export const updateEventRegistration: Mutation<EventsRegistrationsState> = (
  state, value: {
    eventId: number,
    data: EventRegistration,
  },
) => {
  const { eventId, data } = value;
  eventRegistraionObjectTypes.forEach((type: EventsRegistrationsObjectType) => {
    if (state[type].events[eventId]) {
      state[type].events[eventId] = [
        data,
        ...state[type].events[eventId]
          .filter((registration) => registration.id !== data.id),
      ];
    }
  });
};

export const flushEventRegistrations: Mutation<EventsRegistrationsState> = (
  state,
) => {
  eventRegistraionObjectTypes.forEach((type: EventsRegistrationsObjectType) => {
    state[type].events = {};
    state[type].loading = false;
  });
};

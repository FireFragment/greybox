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
    type: EventsRegistrationsObjectType,
  },
) => {
  state[value.type].events[value.eventId] = [
    value.data,
    ...state[value.type].events[value.eventId]
      .filter((registration) => registration.id !== value.data.id),
  ];
};

export const flushEventRegistrations: Mutation<EventsRegistrationsState> = (
  state,
) => {
  eventRegistraionObjectTypes.forEach((type: EventsRegistrationsObjectType) => {
    state[type].events = {};
    state[type].loading = false;
  });
};

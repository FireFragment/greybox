import { Mutation } from 'vuex';
import { EventRegistration } from 'src/types/event';
import { EventsRegistrationsState } from './state';

export const startLoadingEventsRegistrations: Mutation<EventsRegistrationsState> = (state) => {
  state.loading = true;
};

export const setEventRegistrations: Mutation<EventsRegistrationsState> = (
  state, value: {
    eventId: number,
    data: EventRegistration[],
  },
) => {
  state.events[value.eventId] = value.data;
  state.loading = false;
};

export const updateEventRegistration: Mutation<EventsRegistrationsState> = (
  state, value: {
    eventId: number,
    data: EventRegistration,
  },
) => {
  state.events[value.eventId] = [
    value.data,
    ...state.events[value.eventId].filter((registration) => registration.id !== value.data.id),
  ];
};

export const flushEventRegistrations: Mutation<EventsRegistrationsState> = (
  state,
) => {
  state.events = {};
  state.loading = false;
};

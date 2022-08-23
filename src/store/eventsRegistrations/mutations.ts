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

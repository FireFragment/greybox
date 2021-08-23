import { Mutation } from 'vuex';
import { Event, EventsState } from './state';

export const startLoadingEvents: Mutation<EventsState> = (state) => {
  state.loading = true;
};

export const setEvents: Mutation<EventsState> = (state, value: Event[]) => {
  state.events = value;
  state.loaded = true;
  state.loading = false;
};

import { Mutation } from 'vuex';
import { EventFull, EventsData, EventsState } from './state';

export const startLoadingEvents: Mutation<EventsState> = (state) => {
  state.loading = true;
};

export const setEvents: Mutation<EventsState> = (state, value: EventsData) => {
  state.events = value;
  state.loaded = true;
  state.loading = false;
};

export const setFullEvent: Mutation<EventsState> = (state, value: EventFull) => {
  state.eventsFull[value.id] = {
    ...value,
    fullyLoaded: true,
  };
};

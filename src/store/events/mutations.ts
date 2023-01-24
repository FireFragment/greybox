import { Mutation } from 'vuex';
import { Event, EventFull } from 'src/types/event';
import { EventsData, EventsState } from './state';

export const startLoadingEvents: Mutation<EventsState> = (state) => {
  state.loading = true;
};

export const startLoadingAllEvents: Mutation<EventsState> = (state) => {
  state.loadingAll = true;
};

export const setEvents: Mutation<EventsState> = (state, value: EventsData) => {
  state.events = value;
  state.loaded = true;
  state.loading = false;
};

export const setAllEvents: Mutation<EventsState> = (state, value: Event[]) => {
  state.eventsAll = value;
  state.loadedAll = true;
  state.loadingAll = false;
};

export const setFullEvent: Mutation<EventsState> = (state, value: EventFull) => {
  state.eventsFull[value.id] = {
    ...value,
    fullyLoaded: true,
  };
};

export const flushPersonalEvents: Mutation<EventsState> = (
  state,
) => {
  state.eventsAll = [];
  state.loadedAll = false;
};

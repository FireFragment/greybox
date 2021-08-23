import { Event, EventFull, EventsState } from './state';

export const eventsArray = (state: EventsState): Event[] => Object.values(state.events);

export const fullEvent = (state: EventsState) => (id: number): EventFull => state.eventsFull[id];

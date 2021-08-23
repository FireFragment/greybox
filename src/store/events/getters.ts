import { Event, EventFull, EventsState } from './state';

export const eventsArray = (state: EventsState): Event[] => Object.values(state.events);

export const event = (state: EventsState) => (id: number): Event => state.events[id];

export const fullEvent = (state: EventsState) => (id: number): EventFull => state.eventsFull[id];

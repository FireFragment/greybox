import { Event, EventFull } from 'src/types/event';
import { EventsState } from './state';

export const eventsArray = (state: EventsState): Event[] => Object.values(state.events);

export const event = (state: EventsState) => (id: number): Event => state.events[id];

export const allEvents = (state: EventsState): Event[] => state.eventsAll;

export const fullEvent = (state: EventsState) => (id: number): EventFull => state.eventsFull[id];

export const filteredEvents = (state: EventsState) => (
  filter: ((eventId: number) => boolean),
): Event[] => Object.values(state.events).filter((e) => filter(e.id));

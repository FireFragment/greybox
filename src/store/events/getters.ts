import { Event, EventFull } from 'src/types/event';
import { date } from 'quasar';
import { EventsState } from './state';

// Get only events whose hard deadline has not passed yet
export const eventsBeforeRegistration = (state: EventsState): Event[] => Object.values(state.events)
  .filter((e: Event) => e.hard_deadline > date.formatDate(Date.now(), 'YYYY-MM-DD HH:mm:ss'));

export const eventsClosed = (state: EventsState): Event[] => Object.values(state.events)
  .filter((e: Event) => e.hard_deadline <= date.formatDate(Date.now(), 'YYYY-MM-DD HH:mm:ss'));

export const eventsCurrent = (state: EventsState): Event[] => Object.values(state.events);

export const event = (state: EventsState) => (id: number): Event => state.events[id];

export const allEvents = (state: EventsState): Event[] => state.eventsAll;

export const fullEvent = (state: EventsState) => (id: number): EventFull => state.eventsFull[id];

export const filteredEvents = (state: EventsState) => (
  filter: ((eventId: number) => boolean),
): Event[] => Object.values(state.events).filter((e) => filter(e.id));

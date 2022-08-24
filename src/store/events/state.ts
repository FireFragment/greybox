import { Event, EventFull } from 'src/types/event';

export type EventsData = {
  [key: number]: Event
};

export type EventsFullData = {
  [key: number]: EventFull
};

export interface EventsState {
  events: EventsData;
  eventsFull: EventsFullData;
  loading: boolean;
  loaded: boolean;
}

export default (): EventsState => ({
  events: {},
  eventsFull: {},
  loading: false,
  loaded: false,
});

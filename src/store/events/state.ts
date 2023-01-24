import { Event, EventFull } from 'src/types/event';

export type EventsData = {
  [key: number]: Event
};

export type EventsFullData = {
  [key: number]: EventFull
};

export interface EventsState {
  events: EventsData;
  loading: boolean;
  loaded: boolean;
  eventsFull: EventsFullData;
  eventsAll: EventsData;
  loadingAll: boolean;
  loadedAll: boolean;
}

export default (): EventsState => ({
  events: {}, // simple data of all current events
  loading: false,
  loaded: false,
  eventsFull: {}, // full data of all current events
  eventsAll: {}, // simple data of all events the user has sent registrations to
  loadingAll: false,
  loadedAll: false,
});

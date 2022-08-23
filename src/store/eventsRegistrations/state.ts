import { EventRegistration } from 'src/types/event';

export type EventsRegistrationsData = Record<number, EventRegistration[]>;

export interface EventsRegistrationsState {
  events: EventsRegistrationsData;
  loading: boolean;
}

export default (): EventsRegistrationsState => ({
  events: {},
  loading: false,
});

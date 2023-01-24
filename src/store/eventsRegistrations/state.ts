import { EventRegistration } from 'src/types/event';

export type EventsRegistrationsData = Record<number, EventRegistration[]>;

interface EventsRegistrationsObject {
  events: EventsRegistrationsData;
  loading: boolean;
}

export const eventRegistraionObjectTypes = ['admin', 'user'];

export type EventsRegistrationsObjectType = typeof eventRegistraionObjectTypes[number];

export type EventsRegistrationsState = Record<EventsRegistrationsObjectType,
  EventsRegistrationsObject>;

export default (): EventsRegistrationsState => ({
  admin: {
    events: {},
    loading: false,
  },
  user: {
    events: {},
    loading: false,
  },
});

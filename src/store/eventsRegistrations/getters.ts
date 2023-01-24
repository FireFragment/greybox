import { EventRegistration } from 'src/types/event';
import { EventsRegistrationsObjectType, EventsRegistrationsState } from './state';

export const eventRegistrations = (state: EventsRegistrationsState) => (
  eventId: number,
  type: EventsRegistrationsObjectType,
): EventRegistration[] => state[type].events[eventId];

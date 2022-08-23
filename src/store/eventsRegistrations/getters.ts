import { EventRegistration } from 'src/types/event';
import { EventsRegistrationsState } from './state';

export const eventRegistrations = (state: EventsRegistrationsState) => (
  eventId: number,
): EventRegistration[] => state.events[eventId];

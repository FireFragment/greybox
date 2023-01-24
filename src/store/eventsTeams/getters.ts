import { EventTeam } from 'src/types/event';
import { EventsTeamsState } from './state';

export const eventTeams = (state: EventsTeamsState) => (
  eventId: number,
): EventTeam[] => state.events[eventId];

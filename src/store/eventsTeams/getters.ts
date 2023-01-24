import { EventTeam } from 'src/types/event';
import { EventsTeamsState } from './state';

export const eventTeamsDetails = (state: EventsTeamsState) => (
  eventId: number,
): EventTeam[] => state.detailedTeams.events[eventId];

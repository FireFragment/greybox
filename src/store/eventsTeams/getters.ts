import { EventTeam } from 'src/types/event';
import { Team } from 'src/types/debate';
import { EventsTeamsState } from './state';

export const eventTeamsDetailed = (state: EventsTeamsState) => (
  eventId: number,
): EventTeam[] => state.detailedTeams.events[eventId];

export const eventTeamsSimple = (state: EventsTeamsState) => (
  eventId: number,
): Team[] => state.simpleTeams.events[eventId];

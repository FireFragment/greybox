import { EventTeam } from 'src/types/event';
import { Team } from 'src/types/debate';

export type EventsTeamsData = Record<number, EventTeam[]>;

export type SimpleEventTeamsData = Record<number, Team[]>;

export interface EventsTeamsState {
  detailedTeams: {
    events: EventsTeamsData;
    loading: boolean;
  };
  simpleTeams: {
    events: SimpleEventTeamsData;
    loading: boolean;
  };
}

export default (): EventsTeamsState => ({
  detailedTeams: {
    events: {},
    loading: false,
  },
  simpleTeams: {
    events: {},
    loading: false,
  },
});

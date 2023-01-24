import { EventTeam } from 'src/types/event';

export type EventsTeamsData = Record<number, EventTeam[]>;

export interface EventsTeamsState {
  events: EventsTeamsData;
  loading: boolean;
}

export default (): EventsTeamsState => ({
  events: {},
  loading: false,
});

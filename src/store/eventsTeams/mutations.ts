import { Mutation } from 'vuex';
import { EventTeam } from 'src/types/event';
import { EventsTeamsState } from './state';

export const startLoadingEventsTeamsDetails: Mutation<EventsTeamsState> = (state) => {
  state.detailedTeams.loading = true;
};

export const setEventTeamsDetails: Mutation<EventsTeamsState> = (
  state, value: {
    eventId: number,
    data: EventTeam[],
  },
) => {
  state.detailedTeams.events[value.eventId] = value.data;
  state.detailedTeams.loading = false;
};

export const flushEventTeamsDetails: Mutation<EventsTeamsState> = (
  state, eventId: number,
) => {
  delete state.detailedTeams.events[eventId];
};

export const flushAllEventTeams: Mutation<EventsTeamsState> = (
  state,
) => {
  state.detailedTeams = {
    events: {},
    loading: false,
  };
  state.simpleTeams = {
    events: {},
    loading: false,
  };
};

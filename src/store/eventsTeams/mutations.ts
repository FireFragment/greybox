import { Mutation } from 'vuex';
import { EventTeam } from 'src/types/event';
import { Team } from 'src/types/debate';
import { EventsTeamsState } from './state';

export const startLoadingEventsTeamsDetailed: Mutation<EventsTeamsState> = (state) => {
  state.detailedTeams.loading = true;
};

export const startLoadingEventsTeamsSimple: Mutation<EventsTeamsState> = (state) => {
  state.simpleTeams.loading = true;
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

export const setEventTeamsSimple: Mutation<EventsTeamsState> = (
  state, value: {
    eventId: number,
    data: Team[],
  },
) => {
  state.simpleTeams.events[value.eventId] = value.data;
  state.simpleTeams.loading = false;
};

export const flushEventTeamsDetailed: Mutation<EventsTeamsState> = (
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

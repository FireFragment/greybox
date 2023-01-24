import { Mutation } from 'vuex';
import { EventTeam } from 'src/types/event';
import { EventsTeamsState } from './state';

export const startLoadingEventsTeams: Mutation<EventsTeamsState> = (state) => {
  state.loading = true;
};

export const setEventTeams: Mutation<EventsTeamsState> = (
  state, value: {
    eventId: number,
    data: EventTeam[],
  },
) => {
  state.events[value.eventId] = value.data;
  state.loading = false;
};

export const flushEventTeams: Mutation<EventsTeamsState> = (
  state, eventId: number,
) => {
  delete state.events[eventId];
};

export const flushAllEventTeams: Mutation<EventsTeamsState> = (
  state,
) => {
  state.events = {};
  state.loading = false;
};

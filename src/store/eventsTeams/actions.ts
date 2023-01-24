import { Action } from 'vuex';
import { bus } from 'boot/eventBus';
import { AxiosResponse } from 'axios';
import { apiCall } from 'boot/api';
import { EventTeam } from 'src/types/event';
import { EventsTeamsState } from 'src/store/eventsTeams/state';

export const loadDetailed: Action<EventsTeamsState, never> = async ({
  commit,
  state,
}, eventId: number) => {
  if (state.detailedTeams.loading || state.detailedTeams.events[eventId]) {
    return;
  }

  commit('startLoadingEventsTeamsDetailed');

  bus.$emit('fullLoader', true);

  await apiCall({
    url: `event/${eventId}/team/detail`,
    method: 'get',
  })
    .then(({ data }: AxiosResponse<EventTeam[]>) => {
      commit('setEventTeamsDetails', {
        eventId,
        data,
      });
    })
    .finally(() => {
      bus.$emit('fullLoader', false);
    });
};

export const loadSimple: Action<EventsTeamsState, never> = async ({
  commit,
  state,
}, eventId: number) => {
  if (state.simpleTeams.loading || state.simpleTeams.events[eventId]) {
    return;
  }

  commit('startLoadingEventsTeamsSimple');

  bus.$emit('fullLoader', true);

  await apiCall({
    url: `event/${eventId}/team`,
    method: 'get',
  })
    .then(({ data }: AxiosResponse<EventTeam[]>) => {
      commit('setEventTeamsSimple', {
        eventId,
        data,
      });
    })
    .finally(() => {
      bus.$emit('fullLoader', false);
    });
};

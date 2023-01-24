import { Action } from 'vuex';
import { bus } from 'boot/eventBus';
import { AxiosResponse } from 'axios';
import { apiCall } from 'boot/api';
import { EventTeam } from 'src/types/event';
import { EventsTeamsState } from 'src/store/eventsTeams/state';

export const load: Action<EventsTeamsState, never> = async ({
  commit,
  state,
}, eventId: number) => {
  if (state.loading || state.events[eventId]) {
    return;
  }

  commit('startLoadingEventsTeams');

  bus.$emit('fullLoader', true);

  await apiCall({
    url: `event/${eventId}/team`,
    method: 'get',
  })
    .then(({ data }: AxiosResponse<EventTeam[]>) => {
      commit('setEventTeams', {
        eventId,
        data,
      });
    })
    .finally(() => {
      bus.$emit('fullLoader', false);
    });
};

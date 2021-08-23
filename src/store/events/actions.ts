import { Action } from 'vuex';
import { bus } from 'boot/eventBus';
import { AxiosResponse } from 'axios';
import { $isPDS, $makeIdObject } from 'boot/custom';
import { apiCall } from 'boot/api';
import { Event, EventsState } from './state';

export const load: Action<EventsState, never> = async ({
  commit,
  state,
}, loading: boolean = true) => {
  if (state.loaded || state.loading) {
    return;
  }

  commit('startLoadingEvents');

  if (loading) {
    bus.$emit('fullLoader', true);
  }

  await apiCall({
    url: 'event',
    sendToken: false,
    method: 'get',
  })
    .then(({ data }: AxiosResponse<Event[]>) => {
      commit('setEvents', $makeIdObject(
        data.filter((event) => event.pds === $isPDS),
      ));
    })
    .finally(() => {
      if (loading) {
        bus.$emit('fullLoader', false);
      }
    });
};

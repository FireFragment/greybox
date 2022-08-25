import { Action } from 'vuex';
import { bus } from 'boot/eventBus';
import { AxiosResponse } from 'axios';
import { apiCall } from 'boot/api';
import { EventRegistration } from 'src/types/event';
import { EventsRegistrationsState } from 'src/store/eventsRegistrations/state';

export const load: Action<EventsRegistrationsState, never> = async ({
  commit,
  state,
}, eventId: number) => {
  if (state.loading || state.events[eventId]) {
    return;
  }

  commit('startLoadingEventsRegistrations');

  bus.$emit('fullLoader', true);

  await apiCall({
    url: `event/${eventId}/registration`,
    method: 'get',
  })
    .then(({ data }: AxiosResponse<EventRegistration[]>) => {
      commit('setEventRegistrations', {
        eventId,
        data,
      });
    })
    .finally(() => {
      bus.$emit('fullLoader', false);
    });
};

import { Action } from 'vuex';
import { bus } from 'boot/eventBus';
import { AxiosResponse } from 'axios';
import { apiCall } from 'boot/api';
import { EventRegistration } from 'src/types/event';
import {
  EventsRegistrationsObjectType,
  EventsRegistrationsState,
} from 'src/store/eventsRegistrations/state';
import { user } from 'boot/auth';

export const load: Action<EventsRegistrationsState, never> = async (
  { commit, state },
  [eventId, type]: [number, EventsRegistrationsObjectType],
) => {
  const apiUrl = type === 'admin' ? eventId : `${eventId}/user/${user()?.id ?? 0}`;

  if (state[type].loading || state[type].events[eventId]) {
    return;
  }

  commit('startLoadingEventsRegistrations', type);

  bus.$emit('fullLoader', true);

  await apiCall({
    url: `event/${apiUrl}/registration`,
    method: 'get',
  })
    .then(({ data }: AxiosResponse<EventRegistration[]>) => {
      commit('setEventRegistrations', {
        eventId,
        data,
        type,
      });
    })
    .finally(() => {
      bus.$emit('fullLoader', false);
    });
};

import { createStore } from 'vuex';
import { RolesState } from 'src/store/roles/state';
import eventRegistrationForm from 'src/store/eventRegistrationForm';
import { EventsRegistrationsState } from './eventsRegistrations/state';
import config from '../config';
import events from './events';
import roles from './roles';
import eventsRegistrations from './eventsRegistrations';
import { EventsState } from './events/state';

export interface State {
  events: EventsState;
  eventsRegistrations: EventsRegistrationsState;
  roles: RolesState;
}

const store = createStore({
  modules: {
    eventRegistrationForm,
    events,
    eventsRegistrations,
    roles,
  },
  strict: config.debug,
});

export const flushUserData = () => {
  store.commit('eventRegistrationForm/flushEventRegistrationForms');
  store.commit('eventsRegistrations/flushEventRegistrations');
};

export default store;

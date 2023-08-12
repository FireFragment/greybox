import { createStore } from 'vuex';
import { RolesState } from 'src/store/roles/state';
import eventRegistrationForm from 'src/store/eventRegistrationForm';
import eventsTeams from 'src/store/eventsTeams';
import { EventsRegistrationsState } from './eventsRegistrations/state';
import config from '../config';
import diets from './diets';
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
    diets,
    eventRegistrationForm,
    events,
    eventsRegistrations,
    eventsTeams,
    roles,
  },
  strict: config.debug,
});

export const flushUserData = () => {
  store.commit('eventRegistrationForm/flushAllEventRegistrationForms');
  store.commit('eventsRegistrations/flushEventRegistrations');
  store.commit('eventsTeams/flushAllEventTeams');
  store.commit('events/flushPersonalEvents');
};

export default store;

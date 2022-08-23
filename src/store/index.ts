import { createStore } from 'vuex';
import { RolesState } from 'src/store/roles/state';
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

export default () => createStore({
  modules: {
    events,
    eventsRegistrations,
    roles,
  },
  strict: config.debug,
});

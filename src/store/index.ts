import { createStore } from 'vuex';
import config from '../config';
import events from './events';
import roles from './roles';
import { EventsState } from './events/state';

export interface State {
  events: EventsState
}

export default () => createStore({
  modules: {
    events,
    roles,
  },
  strict: config.debug,
});

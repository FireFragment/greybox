import { createStore } from 'vuex';
import config from '../config';
import events from './events';
import { EventsState } from './events/state';

export interface State {
  events: EventsState
}

export default () => createStore({
  modules: {
    events,
  },
  strict: config.debug,
});

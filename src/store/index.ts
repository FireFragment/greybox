import { createStore } from 'vuex';
import config from '../config';
import test from './test';
import { TestState } from './test/state';

export interface State {
  test: TestState
}

export default () => createStore({
  modules: {
    test,
  },
  strict: config.debug,
});

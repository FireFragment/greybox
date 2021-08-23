import { Action } from 'vuex';
import { TestState } from 'src/store/test/state';

export const testAction: Action<TestState, never> = ({ commit }) => {
  // Load products from API or something
  commit('updateDrawerState', 'actionValue');
};

import { Mutation } from 'vuex';
import { TestState } from 'src/store/test/state';

export const updateDrawerState: Mutation<TestState> = (state, value: string) => {
  state.drawerState = value;
};

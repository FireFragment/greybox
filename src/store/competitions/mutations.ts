import { Mutation } from 'vuex';
import { Competition } from 'src/types/event';
import { CompetitionsState } from './state';

export const startLoadingCompetitions: Mutation<CompetitionsState> = (state) => {
  state.loading = true;
};

export const setCompetitions: Mutation<CompetitionsState> = (state, value: Competition[]) => {
  state.competitions = value;
  state.loaded = true;
  state.loading = false;
};

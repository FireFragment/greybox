import { Mutation } from 'vuex';
import { DietaryRequirement } from 'src/types/event';
import { DietsState } from './state';

export const startLoadingDiets: Mutation<DietsState> = (state) => {
  state.loading = true;
};

export const setDiets: Mutation<DietsState> = (state, value: DietaryRequirement[]) => {
  state.diets = value;
  state.loaded = true;
  state.loading = false;
};

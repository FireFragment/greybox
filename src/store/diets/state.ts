import { DietaryRequirement } from 'src/types/event';

export type DietsData = DietaryRequirement[];

export interface DietsState {
  diets: DietsData;
  loading: boolean;
  loaded: boolean;
}

export default (): DietsState => ({
  diets: [],
  loading: false,
  loaded: false,
});

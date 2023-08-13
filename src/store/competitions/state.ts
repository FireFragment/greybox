import { Competition } from 'src/types/event';

export type CompetitionsData = Competition[];

export interface CompetitionsState {
  competitions: CompetitionsData;
  loading: boolean;
  loaded: boolean;
}

export default (): CompetitionsState => ({
  competitions: [],
  loading: false,
  loaded: false,
});

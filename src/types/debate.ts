import { DateTime } from 'src/types/general';

export interface Ballot {
  adjudicator: string;
  url: string;
}

export interface Debate {
  affirmativeTeam: string;
  ballots: Ballot[];
  canUploadBallot: boolean;
  date: string;
  link: string;
  motion: string;
  negativeTeam: string;
  oldGreyboxId: string;
  result: string;
  role: string;
  score: string;
  win: boolean | null;
}

export interface Team {
  id: number;
  institution: string | null;
  name: string;
  // eslint-disable-next-line camelcase
  registered_by: number;
  // eslint-disable-next-line camelcase
  created_at: DateTime;
  // eslint-disable-next-line camelcase
  updated_at: DateTime;
}

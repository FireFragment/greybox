export interface Ballot {
  adjudicator: string;
  url: string;
}

export interface Debate {
  affirmativeTeam: string;
  ballots: Ballot[];
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

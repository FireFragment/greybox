export interface EventsState {
  events: string[];
}

export default (): EventsState => ({
  events: [],
});

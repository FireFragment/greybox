export interface TestState {
  drawerState: string;
}

export default (): TestState => ({
  drawerState: 'defaultValue',
});

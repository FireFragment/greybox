import { boot } from 'quasar/wrappers';
import EventBus from '../event-bus';

export default boot(({ app }) => {
  app.config.globalProperties.$bus = EventBus;
});

export { EventBus };

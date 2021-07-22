import { boot } from 'quasar/wrappers';

type EventCallbackData = Record<string, never>;
type EventCallback = (data: EventCallbackData) => void;

interface EventCallbacks {
  [key: string]: EventCallback[]
}

interface Events {
  events: EventCallbacks
}

// Source: https://stackoverflow.com/a/64045893
class EventBus implements Events {
  events: EventCallbacks = {};

  $on(eventName: string, fn: EventCallback) {
    this.events[eventName] = this.events[eventName] || [];
    this.events[eventName].push(fn);
  }

  $off(eventName: string, fn: EventCallback) {
    if (this.events[eventName]) {
      for (let i = 0; i < this.events[eventName].length; i += 1) {
        if (this.events[eventName][i] === fn) {
          this.events[eventName].splice(i, 1);
          break;
        }
      }
    }
  }

  $emit(eventName: string, data: EventCallbackData) {
    if (this.events[eventName]) {
      this.events[eventName].forEach((fn) => fn(data));
    }
  }
}

const bus: EventBus = new EventBus();

export default boot(({ app }) => {
  app.config.globalProperties.$bus = bus;
});

export { bus };

import { route } from 'quasar/wrappers';
import {
  createRouter,
  createWebHashHistory,
  createWebHistory,
  RouteLocationNormalized,
  RouteRecordNormalized,
} from 'vue-router';
import { i18n } from 'boot/i18n';
import routes from './routes';

// Set app locale based on route language
export const setLocaleToRoute = (currentRoute: RouteLocationNormalized): void => {
  const isAlias: boolean = currentRoute.matched.find(
    (item: RouteRecordNormalized) => item.aliasOf,
  ) !== undefined;

  i18n.global.locale = isAlias ? 'en' : 'cs';
};

export default route(() => {
  const createHistory = process.env.VUE_ROUTER_MODE === 'history' ? createWebHistory : createWebHashHistory;

  const Router = createRouter({
    scrollBehavior: () => ({
      left: 0,
      top: 0,
    }),
    routes,

    // Leave this as is and make changes in quasar.conf.js instead!
    // quasar.conf.js -> build -> vueRouterMode
    // quasar.conf.js -> build -> publicPath
    history: createHistory(
      process.env.MODE === 'ssr' ? void 0 : process.env.VUE_ROUTER_BASE,
    ),
  });

  Router.afterEach((to) => {
    setLocaleToRoute(to);
  });

  return Router;
});

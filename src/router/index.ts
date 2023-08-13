import { route } from 'quasar/wrappers';
import {
  createRouter,
  createWebHashHistory,
  createWebHistory,
  RouteLocationNormalized,
  RouteRecordNormalized,
} from 'vue-router';
import { i18n, TranslatedString } from 'boot/i18n';
import { Lang } from 'src/translation/config';
import { $setTitle } from 'src/boot/custom';
import routes from './routes';

export const primaryRouteLang: Lang = 'cs';
export const aliasRouteLang: Lang = 'en';

// Set app locale based on route language
export const setLocaleToRoute = (currentRoute: RouteLocationNormalized): void => {
  const isAlias: boolean = currentRoute.matched.find(
    (item: RouteRecordNormalized) => item.aliasOf,
  ) !== undefined;

  i18n.global.locale = isAlias ? aliasRouteLang : primaryRouteLang;
};

const createHistory = process.env.VUE_ROUTER_MODE === 'history' ? createWebHistory : createWebHashHistory;
export const Router = createRouter({
  scrollBehavior: () => ({
    left: 0,
    top: 0,
  }),
  routes,

  history: createHistory(
    process.env.MODE === 'ssr' ? void 0 : process.env.VUE_ROUTER_BASE,
  ),
});

export default route(() => {
  Router.afterEach((to) => {
    setLocaleToRoute(to);
    try {
      const lang = i18n.global.locale;
      const title: TranslatedString = <TranslatedString> to.meta.title;
      $setTitle(title[lang]);
    } catch (_) {
      $setTitle();
    }
  });

  return Router;
});

import { RouteRecordRaw } from 'vue-router';
import { adminMiddleware, loggedInMiddleware, notLoggedInMiddleware } from './middlewares';

// Translations
import CZroutes from '../translation/cs/paths.json';
import ENroutes from '../translation/en/paths.json';

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    component: () => import('layouts/Default.vue'),
    children: [
      {
        path: CZroutes.home,
        alias: ENroutes.home,
        name: 'home',
        component: () => import('pages/Home.vue'),
      },
      {
        path: CZroutes.about,
        alias: ENroutes.about,
        name: 'about',
        component: () => import('pages/About.vue'),
      },
      {
        path: `${CZroutes.tournament}/:id-:slug`,
        alias: `${ENroutes.tournament}/:id-:slug`,
        name: 'tournament',
        component: () => import('pages/Event.vue'),
      },

      // My debates
      {
        // TODO - type routes & therefore fix weird errors below
        // eslint-disable-next-line @typescript-eslint/no-unsafe-assignment
        path: `${CZroutes.myDebates}/:page?`,
        // eslint-disable-next-line @typescript-eslint/no-unsafe-assignment
        alias: `${ENroutes.myDebates}/:page?`,
        name: 'myDebates',
        component: () => import('pages/MyDebates.vue'),
        beforeEnter: loggedInMiddleware,
      },

      // Admin
      {
        // eslint-disable-next-line @typescript-eslint/no-unsafe-assignment
        path: CZroutes.admin.events,
        // eslint-disable-next-line @typescript-eslint/no-unsafe-assignment
        alias: ENroutes.admin.events,
        name: 'admin.events',
        component: () => import('pages/Admin/Events.vue'),
        beforeEnter: adminMiddleware,
      },
      {
        // eslint-disable-next-line @typescript-eslint/restrict-template-expressions
        path: `${CZroutes.admin.events}/:id-:slug`,
        // eslint-disable-next-line @typescript-eslint/restrict-template-expressions
        alias: `${ENroutes.admin.events}/:id-:slug`,
        name: 'admin.eventRegistrations',
        component: () => import('pages/Admin/EventRegistrations.vue'),
        beforeEnter: adminMiddleware,
      },

      // Auth
      {
        path: CZroutes.auth.login,
        alias: ENroutes.auth.login,
        name: 'auth.login',
        component: () => import('pages/Auth/Login.vue'),
        beforeEnter: notLoggedInMiddleware,
      },
      {
        path: CZroutes.auth.signUp,
        alias: ENroutes.auth.signUp,
        name: 'auth.signUp',
        component: () => import('pages/Auth/SignUp.vue'),
        beforeEnter: notLoggedInMiddleware,
      },
      {
        path: CZroutes.auth.logout,
        alias: ENroutes.auth.logout,
        name: 'auth.logout',
        component: () => import('pages/Auth/Logout.vue'),
        beforeEnter: loggedInMiddleware,
      },
      {
        path: CZroutes.auth.passwordReset,
        alias: ENroutes.auth.passwordReset,
        name: 'auth.passwordReset',
        component: () => import('pages/Auth/PasswordReset.vue'),
        beforeEnter: notLoggedInMiddleware,
      },
      {
        path: `${CZroutes.auth.passwordReset}/:token`,
        alias: `${ENroutes.auth.passwordReset}/:token`,
        name: 'auth.newPassword',
        component: () => import('pages/Auth/NewPassword.vue'),
        beforeEnter: notLoggedInMiddleware,
      },
      {
        path: CZroutes.auth.accountSettings,
        alias: ENroutes.auth.accountSettings,
        name: 'auth.accountSettings',
        component: () => import('pages/Auth/AccountSettings.vue'),
        beforeEnter: loggedInMiddleware,
      },
      {
        // eslint-disable-next-line max-len
        // eslint-disable-next-line @typescript-eslint/no-unsafe-assignment,@typescript-eslint/no-unsafe-member-access
        path: CZroutes.user.currentRegistrations,
        // eslint-disable-next-line max-len
        // eslint-disable-next-line @typescript-eslint/no-unsafe-assignment,@typescript-eslint/no-unsafe-member-access
        alias: ENroutes.user.currentRegistrations,
        name: 'user.currentRegistrations',
        component: () => import('pages/User/CurrentRegistrations.vue'),
        beforeEnter: loggedInMiddleware,
      },
    ],
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('layouts/Default.vue'),
    children: [{
      path: '',
      component: () => import('pages/404.vue'),
    }],
  },
];

export default routes;

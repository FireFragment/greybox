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
      {
        path: `${CZroutes.tournament}/:id-:slug/jednotlivec`,
        alias: `${ENroutes.tournament}/:id-:slug/individual`,
        name: 'tournament-individual',
        component: () => import('pages/Event/Individual.vue'),
      },
      {
        path: `${CZroutes.tournament}/:id-:slug/skupina`,
        alias: `${ENroutes.tournament}/:id-:slug/group`,
        name: 'tournament-group',
        component: () => import('pages/Event/Group.vue'),
      },
      {
        path: `${CZroutes.tournament}/:id-:slug/:type/debater`,
        alias: `${ENroutes.tournament}/:id-:slug/:type/debater`,
        name: 'tournament-debater',
        component: () => import('pages/Event/Debater.vue'),
      },
      {
        path: `${CZroutes.tournament}/:id-:slug/:type/tym`,
        alias: `${ENroutes.tournament}/:id-:slug/:type/team`,
        name: 'tournament-team',
        component: () => import('pages/Event/Team.vue'),
      },
      {
        path: `${CZroutes.tournament}/:id-:slug/:type/rozhodci`,
        alias: `${ENroutes.tournament}/:id-:slug/:type/judge`,
        name: 'tournament-judge',
        component: () => import('pages/Event/Judge.vue'),
      },
      {
        path: `${CZroutes.tournament}/:id-:slug/:type/dobrovolnik`,
        alias: `${ENroutes.tournament}/:id-:slug/:type/volunteer`,
        name: 'tournament-volunteer',
        component: () => import('pages/Event/Volunteer.vue'),
      },
      {
        path: `${CZroutes.tournament}/:id-:slug/:type/:role/prihlaseni`,
        alias: `${ENroutes.tournament}/:id-:slug/:type/:role/signup`,
        name: 'tournament-signup',
        component: () => import('pages/Event/Signup.vue'),
      },
      {
        path: `${CZroutes.tournament}/:id-:slug/:type/:role/potvrzeni`,
        alias: `${ENroutes.tournament}/:id-:slug/:type/:role/checkout`,
        name: 'tournament-checkout',
        component: () => import('pages/Event/Checkout.vue'),
      },

      // Admin
      {
        path: CZroutes.admin.eventRegistrations,
        alias: ENroutes.admin.eventRegistrations,
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
        props: true,
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

import { RouteRecordRaw, RouterView } from 'vue-router';
import {
  anyEventOrganizerMiddleware,
  eventOrganizerMiddleware,
  loggedInMiddleware,
  notLoggedInMiddleware,
} from './middlewares';

interface Routes {
  [key: string]: string | Routes
}

// Translations
import CZroutesJson from '../translation/cs/paths.json';
import ENroutesJson from '../translation/en/paths.json';

const CZroutes: Routes = <Routes>CZroutesJson;
const ENroutes: Routes = <Routes>ENroutesJson;

const eventTypes = (routes: Routes) => Object.values((<Routes>routes.eventParams).type).join('|');

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    component: () => import('layouts/Default.vue'),
    children: [
      {
        path: <string>CZroutes.home,
        alias: <string>ENroutes.home,
        name: 'home',
        component: () => import('pages/Home.vue'),
      },
      {
        path: <string>CZroutes.about,
        alias: <string>ENroutes.about,
        name: 'about',
        component: () => import('pages/About.vue'),
      },
      {
        path: `${<string>CZroutes.event}/:id-:slug`,
        alias: `${<string>ENroutes.event}/:id-:slug`,
        name: 'event',
        component: () => import('pages/Event.vue'),
        children: [{
          path: '',
          alias: '',
          name: 'event-pick-type',
          meta: {
            translationName: 'event',
          },
          component: () => import('pages/Event/PickType.vue'),
        }, {
          path: `:type(${eventTypes(CZroutes)})`,
          alias: `:type(${eventTypes(ENroutes)})`,
          component: RouterView,
          children: [{
            path: '',
            alias: '',
            name: 'event-pick-role',
            meta: {
              translationName: 'event',
            },
            component: () => import('pages/Event/PickRole.vue'),
          }, {
            path: ':role',
            alias: ':role',
            name: 'event-register-form',
            meta: {
              translationName: 'event',
            },
            component: () => import('pages/Event/RegisterForm.vue'),
          }, {
            path: `${<string>(<Routes>CZroutes.eventParams).checkout}`,
            alias: `${<string>(<Routes>ENroutes.eventParams).checkout}`,
            name: 'event-checkout',
            meta: {
              translationName: 'event',
            },
            component: () => import('pages/Event/Checkout.vue'),
          }, {
            path: `${<string>(<Routes>CZroutes.eventParams).confirmation}`,
            alias: `${<string>(<Routes>ENroutes.eventParams).confirmation}`,
            name: 'event-confirmation',
            meta: {
              translationName: 'event',
            },
            component: () => import('pages/Event/Confirmation.vue'),
          }],
        }],
      },

      // My debates
      {
        // TODO - type routes & therefore fix weird errors below
        path: `${<string>CZroutes.myDebates}/:page?`,
        alias: `${<string>ENroutes.myDebates}/:page?`,
        name: 'myDebates',
        component: () => import('pages/MyDebates.vue'),
        beforeEnter: loggedInMiddleware,
      },

      // Admin
      {
        path: <string>(<Routes>CZroutes.admin).events,
        alias: <string>(<Routes>ENroutes.admin).events,
        component: RouterView,
        children: [{
          path: '',
          alias: '',
          name: 'admin.events',
          beforeEnter: anyEventOrganizerMiddleware,
          component: () => import('pages/Admin/Events.vue'),
        }, {
          path: ':id-:slug',
          alias: ':id-:slug',
          name: 'admin.eventRegistrations',
          meta: {
            translationName: 'admin.events',
          },
          beforeEnter: eventOrganizerMiddleware,
          component: () => import('pages/Admin/EventRegistrations.vue'),
        }],
      },
      // Auth
      {
        path: <string>(<Routes>CZroutes.auth).login,
        alias: <string>(<Routes> ENroutes.auth).login,
        name: 'auth.login',
        component: () => import('pages/Auth/Login.vue'),
        beforeEnter: notLoggedInMiddleware,
      },
      {
        path: <string>(<Routes>CZroutes.auth).signUp,
        alias: <string>(<Routes> ENroutes.auth).signUp,
        name: 'auth.signUp',
        component: () => import('pages/Auth/SignUp.vue'),
        beforeEnter: notLoggedInMiddleware,
      },
      {
        path: <string>(<Routes>CZroutes.auth).logout,
        alias: <string>(<Routes> ENroutes.auth).logout,
        name: 'auth.logout',
        component: () => import('pages/Auth/Logout.vue'),
        beforeEnter: loggedInMiddleware,
      },
      {
        path: <string>(<Routes>CZroutes.auth).passwordReset,
        alias: <string>(<Routes> ENroutes.auth).passwordReset,
        name: 'auth.passwordReset',
        component: () => import('pages/Auth/PasswordReset.vue'),
        beforeEnter: notLoggedInMiddleware,
      },
      {
        path: `${<string>(<Routes>CZroutes.auth).passwordReset}/:token`,
        alias: `${<string>(<Routes>ENroutes.auth).passwordReset}/:token`,
        name: 'auth.newPassword',
        component: () => import('pages/Auth/NewPassword.vue'),
        beforeEnter: notLoggedInMiddleware,
      },
      {
        path: <string>(<Routes>CZroutes.auth).accountSettings,
        alias: <string>(<Routes> ENroutes.auth).accountSettings,
        name: 'auth.accountSettings',
        component: () => import('pages/Auth/AccountSettings.vue'),
        beforeEnter: loggedInMiddleware,
      },
      {
        path: <string>(<Routes>CZroutes.user).currentRegistrations,
        alias: <string>(<Routes> ENroutes.user).currentRegistrations,
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

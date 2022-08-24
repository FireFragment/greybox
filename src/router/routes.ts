import { RouteRecordRaw, RouterView } from 'vue-router';
import { adminMiddleware, loggedInMiddleware, notLoggedInMiddleware } from './middlewares';

interface Routes {
  [key: string]: string | Routes
}

// Translations
import CZroutesJson from '../translation/cs/paths.json';
import ENroutesJson from '../translation/en/paths.json';

const CZroutes: Routes = <Routes>CZroutesJson;
const ENroutes: Routes = <Routes>ENroutesJson;

const eventTypes = (routes: Routes) => Object.values((<Routes>routes.eventParams).type).join('|');
const eventRoles = (routes: Routes) => Object.values((<Routes>routes.eventParams).role).join('|');

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
          name: 'event-pick-role',
          meta: {
            translationName: 'event',
          },
          component: () => import('pages/Event/PickType.vue'),
          children: [
            {
              path: `:role(${eventRoles(CZroutes)})`,
              alias: `:role(${eventRoles(ENroutes)})`,
              name: 'event-register-form',
              meta: {
                translationName: 'event',
              },
              component: () => import('pages/Event/PickType.vue'),
            }, {
              path: `${<string>(<Routes>CZroutes.eventParams).checkout}`,
              alias: `${<string>(<Routes>ENroutes.eventParams).checkout}`,
              name: 'event-checkout',
              meta: {
                translationName: 'event',
              },
              component: () => import('pages/Event/PickType.vue'),
            }, {
              path: `${<string>(<Routes>CZroutes.eventParams).registered}`,
              alias: `${<string>(<Routes>ENroutes.eventParams).registered}`,
              name: 'event-registered',
              meta: {
                translationName: 'event',
              },
              component: () => import('pages/Event/PickType.vue'),
            },
          ],
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
        beforeEnter: adminMiddleware,
        children: [{
          path: '',
          alias: '',
          name: 'admin.events',
          component: () => import('pages/Admin/Events.vue'),
        }, {
          // TODO - check if has admin access to this specific event
          path: ':id-:slug',
          alias: ':id-:slug',
          name: 'admin.eventRegistrations',
          meta: {
            translationName: 'admin.events',
          },
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

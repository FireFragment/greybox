import { RouteRecordRaw, RouterView } from 'vue-router';
import { adminMiddleware, loggedInMiddleware, notLoggedInMiddleware } from './middlewares';

// Translations
import CZroutes from '../translation/cs/paths.json';
import ENroutes from '../translation/en/paths.json';

// eslint-disable-next-line @typescript-eslint/no-unsafe-member-access
const eventTypes = (routes: any) => Object.values(routes.eventParams.type).join('|');
// eslint-disable-next-line @typescript-eslint/no-unsafe-member-access
const eventRoles = (routes: any) => Object.values(routes.eventParams.role).join('|');

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
        // eslint-disable-next-line @typescript-eslint/restrict-template-expressions
        path: `${CZroutes.event}/:id-:slug`,
        // eslint-disable-next-line @typescript-eslint/restrict-template-expressions
        alias: `${ENroutes.event}/:id-:slug`,
        name: 'event',
        component: RouterView,
        children: [{
          path: '',
          alias: '',
          name: 'event-pick-type',
          component: () => import('pages/Event/PickType.vue'),
        }, {
          path: `:type(${eventTypes(CZroutes)})`,
          alias: `:type(${eventTypes(ENroutes)})`,
          name: 'event-pick-role',
          component: () => import('pages/Event/Debater.vue'),
        }, {
          path: `:type(${eventTypes(CZroutes)})/:role(${eventRoles(CZroutes)})`,
          alias: `:type(${eventTypes(ENroutes)})/:role(${eventRoles(ENroutes)})`,
          name: 'event-register-form',
          component: () => import('pages/Event/Debater.vue'),
        }, {
          // eslint-disable-next-line max-len
          // eslint-disable-next-line @typescript-eslint/no-unsafe-member-access,@typescript-eslint/restrict-template-expressions
          path: `:type(${eventTypes(CZroutes)})/${CZroutes.eventParams.checkout}`,
          // eslint-disable-next-line max-len
          // eslint-disable-next-line @typescript-eslint/no-unsafe-member-access,@typescript-eslint/restrict-template-expressions
          alias: `:type(${eventTypes(ENroutes)})/${ENroutes.eventParams.checkout}`,
          name: 'event-checkout',
          component: () => import('pages/Event/Debater.vue'),
        }, {
          // eslint-disable-next-line max-len
          // eslint-disable-next-line @typescript-eslint/no-unsafe-member-access,@typescript-eslint/restrict-template-expressions
          path: `:type(${eventTypes(CZroutes)})/${CZroutes.eventParams.registered}`,
          // eslint-disable-next-line max-len
          // eslint-disable-next-line @typescript-eslint/no-unsafe-member-access,@typescript-eslint/restrict-template-expressions
          alias: `:type(${eventTypes(ENroutes)})/${ENroutes.eventParams.registered}`,
          name: 'event-registered',
          component: () => import('pages/Event/Debater.vue'),
        }],
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
        component: () => import('components/EmptyNestedRouteParent.vue'),
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

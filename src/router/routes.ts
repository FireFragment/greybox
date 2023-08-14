import { RouteRecordRaw, RouterView } from 'vue-router';
import {
  adminMiddleware,
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
import CZtitlesJson from '../translation/cs/titles.json';
import ENtitlesJson from '../translation/en/titles.json';

const CZroutes: Routes = <Routes>CZroutesJson;
const ENroutes: Routes = <Routes>ENroutesJson;
const CZtitles: Routes = <Routes>CZtitlesJson;
const ENtitles: Routes = <Routes>ENtitlesJson;

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
        meta: {
          title: {
            cs: <string>CZtitles.about,
            en: <string>ENtitles.about,
          },
        },
      },
      {
        path: `${<string>CZroutes.event}/:id`,
        alias: `${<string>ENroutes.event}/:id`,
        name: 'event',
        component: () => import('pages/Event.vue'),
        children: [{
          path: '',
          alias: '',
          name: 'event-detail',
          meta: {
            translationName: 'event',
          },
          component: () => import('pages/Event/Detail.vue'),
        }, {
          path: <string>(<Routes>CZroutes.eventParams).pickRole,
          alias: <string>(<Routes>ENroutes.eventParams).pickRole,
          name: 'event-pick-type',
          meta: {
            translationName: 'event',
            title: {
              cs: <string>CZtitles.event,
              en: <string>ENtitles.event,
            },
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
              title: {
                cs: <string>CZtitles.event,
                en: <string>ENtitles.event,
              },
            },
            component: () => import('pages/Event/PickRole.vue'),
          }, {
            path: ':role',
            alias: ':role',
            name: 'event-register-form',
            meta: {
              translationName: 'event',
              title: {
                cs: <string>CZtitles.event,
                en: <string>ENtitles.event,
              },
            },
            component: () => import('pages/Event/RegisterForm.vue'),
          }, {
            path: `:checkout(${<string>(<Routes>CZroutes.eventParams).checkout})`,
            alias: `:checkout(${<string>(<Routes>ENroutes.eventParams).checkout})`,
            name: 'event-checkout',
            meta: {
              translationName: 'event',
              title: {
                cs: <string>CZtitles.event,
                en: <string>ENtitles.event,
              },
            },
            component: () => import('pages/Event/Checkout.vue'),
          }, {
            path: `:confirmation(${<string>(<Routes>CZroutes.eventParams).confirmation})`,
            alias: `:confirmation(${<string>(<Routes>ENroutes.eventParams).confirmation})`,
            name: 'event-confirmation',
            meta: {
              translationName: 'event',
              title: {
                cs: <string>CZtitles.event,
                en: <string>ENtitles.event,
              },
            },
            component: () => import('pages/Event/Confirmation.vue'),
          }],
        }],
      },

      // Admin
      {
        path: <string>(<Routes>CZroutes.admin).events,
        alias: <string>(<Routes>ENroutes.admin).events,
        component: RouterView,
        meta: {
          title: {
            cs: <string>(<Routes>CZtitles.admin).events,
            en: <string>(<Routes>ENtitles.admin).events,
          },
        },
        children: [{
          path: '',
          alias: '',
          name: 'admin.events',
          beforeEnter: anyEventOrganizerMiddleware,
          component: () => import('pages/Admin/Events.vue'),
        }, {
          path: ':id',
          alias: ':id',
          component: RouterView,
          beforeEnter: eventOrganizerMiddleware,
          children: [{
            path: <string>(<Routes>(<Routes>CZroutes.admin).eventViewTypes).edit,
            alias: <string>(<Routes>(<Routes>ENroutes.admin).eventViewTypes).edit,
            name: 'admin.editEvent',
            meta: {
              translationName: 'admin.events',
              // Array of other param values
              additionalTranslations: [
                'admin.eventViewTypes.edit',
              ],
            },
            component: () => import('pages/Admin/EditEvent.vue'),
          }, {
            path: <string>(<Routes>(<Routes>CZroutes.admin).eventViewTypes).editRoles,
            alias: <string>(<Routes>(<Routes>ENroutes.admin).eventViewTypes).editRoles,
            name: 'admin.editEventRoles',
            meta: {
              translationName: 'admin.events',
              // Array of other param values
              additionalTranslations: [
                'admin.eventViewTypes.editRoles',
              ],
            },
            component: () => import('pages/Admin/EditEventRoles.vue'),
          }, {
            path: <string>(<Routes>(<Routes>CZroutes.admin).eventViewTypes).people,
            alias: <string>(<Routes>(<Routes>ENroutes.admin).eventViewTypes).people,
            name: 'admin.eventRegistrations',
            meta: {
              translationName: 'admin.events',
              // Array of other param values
              additionalTranslations: [
                'admin.eventViewTypes.people',
              ],
            },
            component: () => import('pages/Admin/EventRegistrations.vue'),
          }, {
            path: <string>(<Routes>(<Routes>CZroutes.admin).eventViewTypes).teams,
            alias: <string>(<Routes>(<Routes>ENroutes.admin).eventViewTypes).teams,
            name: 'admin.eventTeams',
            meta: {
              translationName: 'admin.events',
              // Array of other param values
              additionalTranslations: [
                'admin.eventViewTypes.teams',
              ],
            },
            component: () => import('pages/Admin/EventTeams.vue'),
          }],
        }],
      },
      {
        path: <string>(<Routes>CZroutes.admin).newEvent,
        alias: <string>(<Routes>ENroutes.admin).newEvent,
        name: 'admin.newEvent',
        component: () => import('pages/Admin/NewEvent.vue'),
        beforeEnter: adminMiddleware,
        meta: {
          title: {
            cs: <string>CZtitles.newEvent,
            en: <string>ENtitles.newEvent,
          },
        },
      },
      // Auth
      {
        path: <string>(<Routes>CZroutes.auth).login,
        alias: <string>(<Routes>ENroutes.auth).login,
        name: 'auth.login',
        component: () => import('pages/Auth/Login.vue'),
        beforeEnter: notLoggedInMiddleware,
        meta: {
          title: {
            cs: <string>CZtitles.login,
            en: <string>ENtitles.login,
          },
        },
      },
      {
        path: <string>(<Routes>CZroutes.auth).signUp,
        alias: <string>(<Routes>ENroutes.auth).signUp,
        name: 'auth.signUp',
        component: () => import('pages/Auth/SignUp.vue'),
        beforeEnter: notLoggedInMiddleware,
        meta: {
          title: {
            cs: <string>CZtitles.signUp,
            en: <string>ENtitles.signUp,
          },
        },
      },
      {
        path: <string>(<Routes>CZroutes.auth).logout,
        alias: <string>(<Routes>ENroutes.auth).logout,
        name: 'auth.logout',
        component: () => import('pages/Auth/Logout.vue'),
        beforeEnter: loggedInMiddleware,
        meta: {
          title: {
            cs: <string>CZtitles.logout,
            en: <string>ENtitles.logout,
          },
        },
      },
      {
        path: <string>(<Routes>CZroutes.auth).passwordReset,
        alias: <string>(<Routes>ENroutes.auth).passwordReset,
        name: 'auth.passwordReset',
        component: () => import('pages/Auth/PasswordReset.vue'),
        beforeEnter: notLoggedInMiddleware,
        meta: {
          title: {
            cs: <string>CZtitles.passwordReset,
            en: <string>ENtitles.passwordReset,
          },
        },
      },
      {
        path: `${<string>(<Routes>CZroutes.auth).passwordReset}/:token`,
        alias: `${<string>(<Routes>ENroutes.auth).passwordReset}/:token`,
        name: 'auth.newPassword',
        component: () => import('pages/Auth/NewPassword.vue'),
        beforeEnter: notLoggedInMiddleware,
        meta: {
          title: {
            cs: <string>CZtitles.passwordReset,
            en: <string>ENtitles.passwordReset,
          },
        },
      },
      {
        path: <string>(<Routes>CZroutes.auth).accountSettings,
        alias: <string>(<Routes>ENroutes.auth).accountSettings,
        name: 'auth.accountSettings',
        component: () => import('pages/Auth/AccountSettings.vue'),
        beforeEnter: loggedInMiddleware,
        meta: {
          title: {
            cs: <string>CZtitles.accountSettings,
            en: <string>ENtitles.accountSettings,
          },
        },
      },
      // User
      {
        path: `${<string>CZroutes.myDebates}/:page?`,
        alias: `${<string>ENroutes.myDebates}/:page?`,
        name: 'myDebates',
        component: () => import('src/pages/User/MyDebates.vue'),
        beforeEnter: loggedInMiddleware,
        meta: {
          title: {
            cs: <string>CZtitles.myDebates,
            en: <string>ENtitles.myDebates,
          },
        },
      },
      {
        path: <string>(<Routes>CZroutes.user).myRegistrations,
        alias: <string>(<Routes>ENroutes.user).myRegistrations,
        beforeEnter: loggedInMiddleware,
        meta: {
          title: {
            cs: <string>CZtitles.myRegistrations,
            en: <string>ENtitles.myRegistrations,
          },
        },
        children: [{
          path: '',
          alias: '',
          meta: {
            translationName: 'user.myRegistrations',
            isHistorical: false,
          },
          name: 'user.myRegistrations',
          component: () => import('pages/User/MyRegistrations.vue'),
        }, {
          path: <string>(<Routes>CZroutes.user).historicalRegistrations,
          alias: <string>(<Routes>ENroutes.user).historicalRegistrations,
          meta: {
            translationName: 'user.myRegistrations',
            isHistorical: true,
          },
          name: 'user.myRegistrationsHistorical',
          component: () => import('pages/User/MyRegistrations.vue'),
        }, {
          path: ':id',
          alias: ':id',
          meta: {
            translationName: 'user.myRegistrations',
          },
          name: 'user.myRegistrationsDetail',
          component: () => import('pages/User/MyRegistrationsDetail.vue'),
        }],
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
    meta: {
      title: {
        cs: <string>CZtitles.notFound,
        en: <string>ENtitles.notFound,
      },
    },
  },
];

export default routes;

import Vue from "vue";
import Router from "vue-router";
import DefaultLayout from "./layouts/Default.vue";
import Home from "./pages/Home.vue";
import About from "./pages/About.vue";
import Event from "./pages/Event.vue";
import Login from "./pages/Auth/Login.vue";
import SignUp from "./pages/Auth/SignUp.vue";
import Logout from "./pages/Auth/Logout.vue";
import PasswordReset from "./pages/Auth/PasswordReset.vue";
import NotFound404 from "./pages/404.vue";

import EventRegistrations from "./pages/Admin/EventRegistrations";

import CZroutes from "./translation/cs/paths";
import ENroutes from "./translation/en/paths";
import NewPassword from "./pages/Auth/NewPassword";

Vue.use(Router);

export default new Router({
  base: process.env.NODE_ENV === "production" ? "/greybox/registrace/" : "/",
  mode: process.env.NODE_ENV === "production" ? "history" : "hash",
  routes: [
    {
      path: "/",
      component: DefaultLayout,
      children: [
        {
          path: CZroutes.home,
          alias: ENroutes.home,
          name: "home",
          component: Home
        },
        {
          path: CZroutes.about,
          alias: ENroutes.about,
          name: "about",
          component: About
        },
        {
          path: CZroutes.tournament + "/:id-:slug",
          alias: ENroutes.tournament + "/:id-:slug",
          name: "tournament",
          component: Event
        },

        // Admin
        {
          path: CZroutes.admin.eventRegistrations,
          alias: ENroutes.admin.eventRegistrations,
          name: "event-registrations",
          component: EventRegistrations
        },

        // Auth
        {
          path: CZroutes.auth.login,
          alias: ENroutes.auth.login,
          name: "login",
          component: Login,
          props: true
        },
        {
          path: CZroutes.auth.signUp,
          alias: ENroutes.auth.signUp,
          name: "sign-up",
          component: SignUp
        },
        {
          path: CZroutes.auth.logout,
          alias: ENroutes.auth.logout,
          name: "logout",
          component: Logout
        },
        {
          path: CZroutes.auth.passwordReset,
          alias: ENroutes.auth.passwordReset,
          name: "password-reset",
          component: PasswordReset
        },
        {
          path: CZroutes.auth.passwordReset + "/:token",
          alias: ENroutes.auth.passwordReset + "/:token",
          name: "new-password",
          component: NewPassword
        }
      ]
    },

    // 404 not found
    {
      path: "*",
      component: DefaultLayout,
      children: [
        {
          path: "",
          name: "404",
          component: NotFound404
        }
      ]
    }
  ]
});

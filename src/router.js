import Vue from "vue";
import Router from "vue-router";
import DefaultLayout from "./layouts/Default.vue";
import Home from "./pages/Home.vue";
import About from "./pages/About.vue";
import Tournament from "./pages/Tournament.vue";
import SignIn from "./pages/Auth/SignIn.vue";
import SignUp from "./pages/Auth/SignUp.vue";
import SignOut from "./pages/Auth/SignOut.vue";
import PasswordReset from "./pages/Auth/PasswordReset.vue";

Vue.use(Router);

export default new Router({
  routes: [
    {
      path: "/",
      component: DefaultLayout,
      children: [
        {
          path: "",
          name: "home",
          component: Home
        },
        {
          path: "/o-webu",
          name: "about",
          component: About
        },
        {
          path: "/turnaj/:id-:slug",
          name: "tournament",
          component: Tournament
        },
        {
          path: "/prihlaseni",
          name: "sign-in",
          component: SignIn
        },
        {
          path: "/sign-up",
          name: "sign-up",
          component: SignUp
        },
        {
          path: "/sign-out",
          name: "sign-out",
          component: SignOut
        },
        {
          path: "/password-reset",
          name: "password-reset",
          component: PasswordReset
        }
      ]
    }
  ]
});

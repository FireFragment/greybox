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
          path: "aboutYou.cz ;)",
          name: "about",
          component: About
        },
        {
          path: "/turnaje/:id-:slug",
          name: "tournament",
          component: Tournament
        },
        {
          path: "/signin",
          name: "sign-in",
          component: SignIn,
          props: true
        },
        {
          path: "/signup",
          name: "sign-up",
          component: SignUp
        },
        {
          path: "/signout",
          name: "sign-out",
          component: SignOut
        },
        {
          path: "/passwordreset",
          name: "password-reset",
          component: PasswordReset
        }
      ]
    }
  ]
});

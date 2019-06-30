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
console.log(Vue.prototype);
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
          path: Vue.prototype.$tr("paths.about"),
          name: "about",
          component: About
        },
        {
          path: "/" + this.$tr("paths.tournament") + "/:id-:slug",
          name: "tournament",
          component: Tournament
        },
        {
          path: "/" + this.$tr("paths.login"),
          name: "sign-in",
          component: SignIn,
          props: true
        },
        {
          path: "/" + this.$tr("paths.signup"),
          name: "sign-up",
          component: SignUp
        },
        {
          path: "/" + this.$tr("paths.signout"),
          name: "sign-out",
          component: SignOut
        },
        {
          path: "/" + this.$tr("paths.passwordReset"),
          name: "password-reset",
          component: PasswordReset
        }
      ]
    }
  ]
});

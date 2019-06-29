import Vue from "vue";
import Router from "vue-router";
import DefaultLayout from "./layouts/Default.vue";
import Home from "./views/Home.vue";
import About from "./views/About.vue";
import Tournament from "./views/Tournament.vue";

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
          path: "/turnaj",
          name: "tournament",
          component: Tournament
        }
      ]
    }
  ]
});

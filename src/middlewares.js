import Vue from "vue";

const Middlewares = {
  admin: (to, from, next) => {
    // Not admin -> send to homepage
    if (!Vue.prototype.$auth.isAdmin()) next({ name: "home" });
    // Admin -> continue
    else next();
  }
};

export default Middlewares;

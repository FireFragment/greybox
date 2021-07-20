import { NavigationGuardWithThis } from 'vue-router';

export const adminMiddleware: NavigationGuardWithThis<undefined> = (
  to, from, next,
) => {
  /*
  if (!Vue.prototype.isAdmin()) {
    // Not admin -> send to homepage
    next({ name: 'home' });
  } else {
    // Admin -> continue
    next();
  }
  */
  next();
};

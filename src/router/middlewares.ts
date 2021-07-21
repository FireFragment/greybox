import { isAdmin, isLoggedIn } from 'src/boot/auth';
import { NavigationGuardWithThis } from 'vue-router';

export const adminMiddleware: NavigationGuardWithThis<undefined> = (
  to, from, next,
) => {
  if (!isAdmin()) {
    next({ name: 'home' });
  } else {
    next();
  }
};

export const loggedInMiddleware: NavigationGuardWithThis<undefined> = (
  to, from, next,
) => {
  if (!isLoggedIn()) {
    next({ name: 'home' });
  } else {
    next();
  }
};

export const notLoggedInMiddleware: NavigationGuardWithThis<undefined> = (
  to, from, next,
) => {
  if (isLoggedIn()) {
    next({ name: 'home' });
  } else {
    next();
  }
};

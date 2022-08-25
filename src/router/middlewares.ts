import { isAdmin, isLoggedIn, user } from 'src/boot/auth';
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

export const eventOrganizerMiddleware: NavigationGuardWithThis<undefined> = (
  to, from, next,
) => {
  const eventId = to.params.id;
  const isValidId = typeof eventId === 'string' && !Number.isNaN(eventId);

  if (isValidId
    && (isAdmin() || user()?.organizedEventsIds.includes(parseInt(<string>eventId, 10)))) {
    next();
  } else {
    next({ name: 'home' });
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

import { isAdmin } from 'src/boot/auth';
import { NavigationGuardWithThis } from 'vue-router';

export const adminMiddleware: NavigationGuardWithThis<undefined> = (
  to, from, next,
) => {
  // eslint-disable-next-line @typescript-eslint/no-unsafe-call
  if (!isAdmin()) {
    // Not admin -> send to homepage
    next({ name: 'home' });
  } else {
    // Admin -> continue
    next();
  }
};

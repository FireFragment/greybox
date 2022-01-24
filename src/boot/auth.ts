import { boot } from 'quasar/wrappers';
import { AxiosResponse } from 'axios';
import { Router } from 'src/router';
import { $path } from 'boot/custom';
import { apiCall } from './api';

type UserRole = 'admin' | 'none';

interface Person {
  name: string
  surname: string
  email: string
  // eslint-disable-next-line camelcase
  school_year: number
  birthdate: string
  // eslint-disable-next-line camelcase
  id_number: string
  street: string
  city: string
  zip: string
  // eslint-disable-next-line camelcase
  dietary_requirement: number
  // eslint-disable-next-line camelcase
  speaker_status: string
  note: string
  // eslint-disable-next-line camelcase
  updated_at: string
  // eslint-disable-next-line camelcase
  created_at: string
  id: number
}

interface User {
  admin: boolean
  apiToken: string
  // eslint-disable-next-line camelcase
  created_at: string
  id: 292
  // eslint-disable-next-line camelcase
  id_token: string
  person: Person | null
  // eslint-disable-next-line camelcase
  person_id: number | null
  // eslint-disable-next-line camelcase
  preferred_locale: string
  role: UserRole
  // eslint-disable-next-line camelcase
  updated_at: string
  username: string
}

export interface LoginData {
  username: string
  password: string
  isSignUp: boolean
}

interface Auth {
  login: (data: LoginData) => Promise<User | null>
  logout: (redirectHome?: boolean) => void
  user: () => User | null,
  getToken: () => string | null
  isLoggedIn: () => boolean
  isAdmin: () => boolean
}

const localStorageKey = 'greyboxAuthData';

const saveUserData = (data: User) => {
  localStorage.setItem(localStorageKey, JSON.stringify(data));
};

const login = (credentials: LoginData): Promise<User | null> => new Promise(
  (resolve, reject) => {
    const {
      username,
      password,
    } = credentials;
    apiCall({
      url: 'login',
      sendToken: false,
      data: {
        username,
        password,
      },
      alerts: false,
    })
      .then(({ data }: AxiosResponse<User>) => {
        saveUserData(data);
        resolve(data);
      })
      .catch(reject);
  },
);

export const logout = async (redirectHome: boolean = true) => {
  localStorage.removeItem(localStorageKey);

  // TODO - flush personal DB once it is connected to Vuex

  if (redirectHome) {
    await Router.replace($path('home'));
  } else {
    // Soft reload current page
    const currentPath = Router.currentRoute.value;

    // Redirect here before route switch to avoid redundant redirect error
    const midRedirect = currentPath.name === 'home' ? 'about' : 'home';
    await Router.push($path(midRedirect));
    await Router.replace({ path: currentPath.path });
  }
};

export const user = (): User | null => {
  const data = localStorage.getItem(localStorageKey);
  if (!data) return null;
  // eslint-disable-next-line @typescript-eslint/no-unsafe-return
  return JSON.parse(data);
};

export const getToken = (): string | null => user()?.apiToken ?? null;

export const isLoggedIn = (): boolean => getToken() !== null;
export const isAdmin = (): boolean => isLoggedIn() && user()?.role === 'admin';

const auth: Auth = {
  login,
  logout,
  user,
  getToken,
  isLoggedIn,
  isAdmin,
};

export default boot(({ app }) => {
  app.config.globalProperties.$auth = auth;

  if (!auth.isLoggedIn()) {
    return;
  }

  // Check if saved token is valid
  apiCall({
    url: 'user/logged',
    method: 'get',
  })
    .then(({ data }: AxiosResponse<User>) => {
      saveUserData(data);
    })
    .catch(async () => {
      await logout(false);
    });
});

// Required for TypeScript to work with global properties
declare module '@vue/runtime-core' {
  export interface ComponentCustomProperties {
    $auth: Auth
  }
}

export { auth };

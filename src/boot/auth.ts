/* eslint-disable no-use-before-define */
import { boot } from 'quasar/wrappers';
import { useRouter } from 'vue-router';

interface Auth {
  check: () => boolean
  token: string | null
  user: () => {
    id: number
    username: string
  },
  logout: () => void
  login: () => Promise<string>
  fetchUser: () => Promise<string>
}

const check = (): boolean => auth.token !== null;

const logout = async () => {
  auth.token = null;
  const router = useRouter();
  await router.replace({ name: 'home' });
};

const login = (): Promise<string> => new Promise<string>((resolve, reject) => {
  // TODO - send login
  console.log('logging in');
  auth.token = 'test';
  resolve('string');
});

const fetchUser = (): Promise<string> => new Promise<string>((resolve, reject) => {
  // TODO - fetch user
  resolve('string');
});

const auth: Auth = {
  check,
  token: null,
  user: () => ({
    id: 5,
    username: 'kuxik009@gmail.com',
  }),
  logout,
  login,
  fetchUser,
};

export default boot(({ app }) => {
  app.config.globalProperties.$auth = auth;
});

export { auth };

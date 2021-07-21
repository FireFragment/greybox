import { boot } from 'quasar/wrappers';

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

const check = (): boolean => {
  // TODO - check user
  // eslint-disable-next-line no-use-before-define
  console.log(auth);
  return true;
};

const logout = () => {
  // TODO - logout
};

const login = (): Promise<string> => new Promise<string>((resolve, reject) => {
  // TODO - send login
  reject('string');
});

const fetchUser = (): Promise<string> => new Promise<string>((resolve, reject) => {
  // TODO - fetch user
  reject('string');
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
  // app.config.globalProperties.$auth = auth;
});

export { auth };

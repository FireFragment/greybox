import axios, {
  AxiosError, AxiosInstance, AxiosPromise, AxiosRequestConfig, Method,
} from 'axios';
import { $flash, $tr } from 'boot/custom';
import { getToken, isLoggedIn, logout } from 'boot/auth';
import { boot } from 'quasar/wrappers';
import config from '../config';

const apiSettings = config.api;

interface RequestOptionsAlertMessages {
  error: string | null
  success: string | null
}

type RequestOptionsAlerts = RequestOptionsAlertMessages | false;

interface ApiCallOptionsArgument extends AxiosRequestConfig {
  sendToken?: boolean
  alerts?: RequestOptionsAlerts
  headers?: Record<string, string | null>
}

interface ApiCallOptions extends AxiosRequestConfig {
  url: string,
  baseURL: string,
  data: never | Record<never, never> | never[],
  method: Method,
  sendToken: boolean
  alerts: RequestOptionsAlerts
  headers: Record<string, string | null>
}

const flashResponseMessages = (
  alerts: RequestOptionsAlerts,
  request: AxiosPromise,
): void => {
  if (!alerts) return;

  const {
    error,
    success,
  } = alerts;

  if (error) {
    request.catch(() => $flash(error, 'error'));
  }

  if (success) {
    void request.then(() => $flash(success, 'done'));
  }
};

const logoutOnTokenFail = (
  request: AxiosPromise,
): void => {
  request.catch(async (result: AxiosError<string | Record<never, never>>) => {
    const { response } = result;
    if (!response) return;
    const {
      data,
      status,
    } = response;
    if (status === 401 && data === 'Unauthorized.') {
      await logout();
    }
  });
};

const apiCall = (options: ApiCallOptionsArgument): AxiosPromise => {
  const defaultError = $tr('general.error', null, false);
  if (typeof defaultError !== 'string') {
    throw new Error('Error needs to be string');
  }
  const defaults: ApiCallOptions = {
    url: '',
    baseURL: apiSettings.baseURL,
    data: {},
    method: 'post',
    headers: {},
    sendToken: true,
    alerts: {
      success: null,
      error: defaultError,
    },
  };

  const requestOptions: ApiCallOptions = { ...defaults, ...options };

  requestOptions.url = `${apiSettings.baseURL}${requestOptions.url}`;

  if (requestOptions.sendToken && isLoggedIn()) {
    requestOptions.headers.Authorization = getToken();
  }

  const request: AxiosPromise = axios(requestOptions);
  flashResponseMessages(requestOptions.alerts, request);

  if (config.debug) {
    request.catch((data) => {
      // eslint-disable-next-line no-console
      console.error(data);
    });
  }

  logoutOnTokenFail(request);

  return request;
};

declare module '@vue/runtime-core' {
  interface ComponentCustomProperties {
    $axios: AxiosInstance
    $api: (options: ApiCallOptionsArgument) => AxiosPromise
  }
}

export default boot(({ app }) => {
  app.config.globalProperties.$axios = axios;
  app.config.globalProperties.$api = apiCall;
});

export { apiCall };

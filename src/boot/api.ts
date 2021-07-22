/* eslint-disable */
import config from '../config';
import axios, { AxiosPromise } from 'axios';
import { $flash, $tr } from 'boot/custom';
import { getToken, isLoggedIn, logout } from 'boot/auth';
import { boot } from 'quasar/wrappers';

const apiSettings = config.api;

interface RequestOptionsAlerts {
  error: string | null
  success: string | null
}

const flashResponseMessages = (
  alerts: RequestOptionsAlerts | false,
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
    request.then(() => $flash(success, 'done'));
  }
};

const logoutOnTokenFail = (
  request: AxiosPromise,
): void => {
  request.catch(async (request) => {
    const {
      response: {
        status,
        data,
      },
    } = request;
    if (status === 401 && data === 'Unauthorized.') {
      await logout();
    }
  });
};

const apiCall = (options: any) => {
  const defaults = {
    url: '',
    baseURL: apiSettings.baseURL,
    data: {},
    method: 'post',
    headers: {},
    sendToken: true,
    alerts: {
      success: null,
      error: $tr('general.error', null, false),
    },
  };

  let requestOptions = { ...defaults, ...options };

  requestOptions.url = apiSettings.baseURL + requestOptions.url;

  if (requestOptions.sendToken && isLoggedIn()) {
    requestOptions.headers['Authorization'] = getToken() + '5';
  }

  const request = axios(requestOptions);
  flashResponseMessages(requestOptions.alerts, request);

  if (config.debug) {
    request.catch(data => {
      console.error(data);
    });
  }

  logoutOnTokenFail(request);

  return request;
};

export default boot(({ app }) => {
  app.config.globalProperties.$api = apiCall;
});

export { apiCall };


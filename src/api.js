/* eslint-disable */
import config from './config';
import axios from 'axios';
import { $flash, $tr } from 'boot/custom';
import { logout } from 'boot/auth';

const apiSettings = config.api;

function apiCall(options) {
  const defaults = {
    url: '',
    baseURL: apiSettings.baseURL,
    data: {},
    sendToken: true,
    method: 'post',
    headers: {},
    alerts: {
      success: null,
      error: $tr('general.error', null, false)
    }
  };

  let requestOptions = { ...defaults, ...options };

  requestOptions.url = apiSettings.baseURL + requestOptions.url;

  if (requestOptions.sendToken && this.$auth.isLoggedIn()) {
    requestOptions.headers['Authorization'] = this.$auth.getToken() + '5';
  }

  const request = axios(requestOptions);

  // Request alerts
  if (requestOptions.alerts) {
    if (requestOptions.alerts.success) {
      request.then(data => {
        let message = requestOptions.alerts.success;

        if (message === true) {
          message = false;
          if (data.response && data.response.data && data.response.data.message) {
            message = data.response.data.message;
          } else if (data.message) message = data.message;
        }

        if (message) $flash(message, 'done');
      });
    }

    if (requestOptions.alerts.error) {
      request.catch(data => {
        let message = requestOptions.alerts.error;

        if (message === true) {
          message = false;
          if (data.response && data.response.data && data.response.data.message) {
            message = data.response.data.message;
          } else if (data.message) message = data.message;
        }

        if (message) $flash(message, 'error');
      });
    }
  }

  if (config.debug) {
    request.catch(data => {
      console.error(data);
    });
  }

  request.catch(async (request) => {
    const { response: { status, data } } = request;
    if (status === 401 && data === 'Unauthorized.') {
      await logout();
    }
  });

  return request;
}

export default apiCall;

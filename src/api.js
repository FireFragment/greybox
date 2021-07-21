/* eslint-disable */
import config from './config';
import axios from 'axios';

let apiSettings = config.api;

function apiCall(options) {
  let defaults = {
    url: '',
    baseURL: apiSettings.baseURL,
    data: {},
    sendToken: true,
    method: 'post',
    headers: {},
    alerts: {
      success: null,
      error: this.$tr('general.error', null, false)
    }
  };

  let requestOptions = { ...defaults, ...options };

  requestOptions.url = apiSettings.baseURL + requestOptions.url;

  if (requestOptions.sendToken && this.$auth.check()) {
    requestOptions.headers['Authorization'] = this.$auth.token;
  }

  let request = axios(requestOptions);

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

        if (message) this.$flash(message, 'done');
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

        if (message) this.$flash(message, 'error');
      });
    }
  }

  if (config.debug) {
    request.catch(data => {
      console.error(data);
    });
  }

  return request;
}

export default apiCall;

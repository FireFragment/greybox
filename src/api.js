import config from "./config";
import axios from "axios";

let apiSettings = config.api;

function apiCall(options) {
  let defaults = {
    url: "",
    baseURL: apiSettings.baseURL,
    data: {},
    sendToken: true,
    method: "post",
    headers: {},
    alerts: {
      success: null,
      error: "An error had occured, please try again."
    }
  };

  let requestOptions = { ...defaults, ...options };

  requestOptions.url = apiSettings.baseURL + requestOptions.url;

  if (requestOptions.sendToken) void 0; // TODO - add user's saved token to request

  let request = axios(requestOptions);

  // Request alerts
  if (requestOptions.alerts) {
    if (requestOptions.alerts.success)
      request.then(data => {
        let message = requestOptions.alerts.success;

        if (message === true) {
          message = false;
          if (data.response && data.response.data && data.response.data.message)
            message = data.response.data.message;
          else if (data.message) message = data.message;
        }

        if (message)
          this.$q.notify({
            color: "green",
            icon: "fas fa-check",
            message,
            position: "top-right",
            timeout: Math.random() * 5000 + 3000
          });
      });

    if (requestOptions.alerts.error)
      request.catch(data => {
        let message = requestOptions.alerts.error;

        if (message === true) {
          message = false;
          if (data.response && data.response.data && data.response.data.message)
            message = data.response.data.message;
          else if (data.message) message = data.message;
        }

        if (message)
          this.$q.notify({
            color: "red",
            icon: "fas fa-times",
            message,
            position: "top-right",
            timeout: Math.random() * 5000 + 3000
          });
      });
  }

  return request;
}

export default apiCall;

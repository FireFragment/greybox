import config from "./config";
import axios from "axios";

let apiSettings = config.api;

function apiCall(options) {
  let defaults = {
    url: "",
    baseURL: apiSettings.baseURL,
    data: {},
    sendToken: false,
    headers: {}
  };

  let requestOptions = { ...defaults, ...options };

  requestOptions.url = apiSettings.baseURL + requestOptions.url;

  return axios(requestOptions);
}

export default apiCall;

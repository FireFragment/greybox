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
    headers: {}
  };

  let requestOptions = { ...defaults, ...options };

  requestOptions.url = apiSettings.baseURL + requestOptions.url;

  if (requestOptions.sendToken) void 0; // TODO - add user's saved token to request

  return axios(requestOptions);
}

export default apiCall;

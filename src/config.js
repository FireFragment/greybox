export default {
  debug: process.env.VUE_APP_MODE !== 'production',
  api: {
    baseURL: process.env.VUE_APP_API_URL + "api/"
  }
};

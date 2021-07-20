export default {
  debug: process.env.MODE !== 'production',
  api: {
    /* eslint-disable-next-line */
    baseURL: `${process.env.API_URL}api/`,
  },
};

export default {
  debug: process.env.MODE !== 'production',
  oldGreyboxUrl: 'https://debatovani.cz/greybox/',
  api: {
    /* eslint-disable-next-line */
    baseURL: `${process.env.API_URL}api/`,
  },
};

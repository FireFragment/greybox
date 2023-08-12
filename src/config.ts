export default {
  debug: process.env.MODE !== 'production',
  oldGreyboxUrl: 'https://debatovani.cz/greybox/',
  debaterRoleId: 1,
  judgeRoleId: 2,
  api: {
    /* eslint-disable-next-line */
    baseURL: `${process.env.API_URL}api/`,
  },
};

module.exports = {
  publicPath: process.env.VUE_APP_BASE_ROUTE,
  outputDir: process.env.VUE_APP_BUILD_DIR,
  pluginOptions: {
    quasar: {}
  },
  transpileDependencies: [/[\\/]node_modules[\\/]quasar[\\/]/]
};

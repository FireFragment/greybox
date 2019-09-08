module.exports = {
  publicPath: process.env.NODE_ENV === "production" ? "/greybox/registrace/" : "",
  outputDir: "registrace",
  pluginOptions: {
    quasar: {}
  },
  transpileDependencies: [/[\\/]node_modules[\\/]quasar[\\/]/]
};

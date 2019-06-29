import Vue from "vue";
import axios from "axios";
import VueAxios from "vue-axios";
import App from "./App.vue";
import router from "./router";
import "./quasar";
import apiCall from "./api";
import config from "./config";
import VueAuth from "@d0whc3r/vue-auth-plugin";

Vue.config.productionTip = false;

// Localization
import VueI18n from "vue-i18n";

Vue.use(VueI18n);
Vue.use(VueAxios, axios);

// Import localization data from JSONs
import i18nConfig from "./translation/config.json";
let translationFiles = i18nConfig.files;
let languageData = i18nConfig.languages;

let translations = {};

for (let locale in languageData) {
  //let item = languageData[locale];
  let languageObject = {};

  for (let index in translationFiles) {
    // Dot notation file path
    let file = translationFiles[index];

    // Path to file (containing slashes)
    let filePath = file.replace(/\./g, "/");

    // Actual translation object
    let content = require("./translation/" + locale + "/" + filePath + ".json");

    // Convert deep paths intohttps://github.com/gruntjs/grunt-contrib-sass actual objects
    let depth = file.split(".");

    if (depth.length > 1) {
      for (let i = depth.length - 1; i > 0; i--) {
        content = {
          [depth[i]]: content
        };
      }
    }

    if (!languageObject[depth[0]]) languageObject[depth[0]] = {};

    for (let index in content) languageObject[depth[0]][index] = content[index];
  }

  translations[locale] = languageObject;
}

const i18n = new VueI18n({
  locale: i18nConfig.default,
  fallbackLocale: i18nConfig.fallback,
  messages: translations,
  silentFallbackWarn: !config.debug,
  silentTranslationWarn: !config.debug
});

// Mixins
Vue.mixin({
  data() {
    return {
      apiSettings: config.api
    };
  },
  methods: {
    // Translation simplification
    // If translationPrefix data is set in component, it will be automcatically placed in front of translation keys
    tr: function(key, options = null, usePrefix = true) {
      let prefix = this.translationPrefix;

      // Use prefix
      if (prefix && usePrefix && options !== false) key = prefix + key;

      return this.$t(key, options);
    },

    stringToHslColor: function(str, s, l) {
      let hash = 0;
      for (let i = 0; i < str.length; i++) {
        hash = str.charCodeAt(i) + ((hash << 5) - hash);
      }

      let h = hash % 360;

      return "hsl(" + h + ", " + s + "%, " + l + "%)";
    },

    // API
    api: apiCall,

    // Get logged user's data
    $user: function() {
      let data = localStorage.getItem("loggedUserData");
      if (!data) return null;

      return JSON.parse(data);
    },

    // Flash
    $flash: function(message, type = "info", icon = false) {
      let color = null;
      if (type === "success" || type === "done") {
        color = "green";
        if (!icon) icon = "check";
      } else if (type === "error" || type === "fail") {
        color = "red";
        if (!icon) icon = "times";
      }

      return this.$q.notify({
        color,
        icon: icon ? "fas fa-" + icon : null,
        message: message,
        position: "top-right",
        timeout: 3500
      });
    }
  }
});

// Auth
Vue.router = router;
Vue.use(VueAuth, {
  tokenStore: "localStorage",
  loginData: {
    url: config.api.baseURL + "login",
    method: "POST",
    redirect: "/",
    headerToken: "Authorization",
    fetchUser: false
  },
  authRedirect: "/login",
  authMeta: "auth",
  rolesVar: "roles",
  tokenDefaultName: "vue_auth_token",
  userDefaultName: "vue_auth_user",
  headerTokenReplace: "{auth_token}",
  tokenType: "",

  logoutData: {
    redirect: "/"
  }
});

new Vue({
  router,
  i18n,
  render: h => h(App)
}).$mount("#app");

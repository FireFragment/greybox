import Vue from "vue";
import Bugsnag from "@bugsnag/js";
import BugsnagPluginVue from "@bugsnag/plugin-vue";
import axios from "axios";
import VueAxios from "vue-axios";
import App from "./App.vue";
import "./quasar";
import apiCall from "./api";
import config from "./config";
import VueAuth from "@d0whc3r/vue-auth-plugin";
import smartformModule from "@smartform.cz/smartform";
import SlideUpDown from "vue-slide-up-down";

Vue.config.productionTip = false;

// Initialize SmartForms
smartformModule.load();
window.smartform.beforeInit = () => {
  window.smartform.setClientId("8ndPcVUJ5B");
};

// Bugsnag error monitoring
if (process.env.VUE_APP_BUGSNAG_KEY) {
  Bugsnag.start({
    apiKey: process.env.VUE_APP_BUGSNAG_KEY,
    plugins: [new BugsnagPluginVue()],
    releaseStage: process.env.VUE_APP_STAGE
  });

  Bugsnag.getPlugin("vue").installVueErrorHandler(Vue);
}

// Localization
import VueI18n from "vue-i18n";

Vue.use(VueI18n);
Vue.use(VueAxios, axios);
Vue.use(require("vue-moment"));
Vue.component("slide-up-down", SlideUpDown);

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
const DB_DELETION_CONSTANT = "DELETE-THIS-DATABASE-ITEM"; // when DB item is set to this value, it will be deleted
Vue.mixin({
  data() {
    return {
      apiSettings: config.api,
      env: process.env,
      DB_DEL: DB_DELETION_CONSTANT
    };
  },
  methods: {
    // Translation simplification
    // If translationPrefix data is set in component, it will be automcatically placed in front of translation keys
    $tr: function(key, options = null, usePrefix = true) {
      // Translate object received from API
      if (typeof key === "object") {
        let locale = this.$i18n ? this.$i18n.locale : i18nConfig.default;

        if (!key || !key[locale]) return "";

        return key[locale];
      }

      // Translate string from JSON
      let prefix = this.translationPrefix;

      // Use prefix
      if (prefix && usePrefix && options !== false) key = prefix + key;

      return this.$t(key, options);
    },

    // Get path to route
    $path: function(route) {
      return "/" + this.$tr("paths." + route, null, false);
    },

    $stringToHslColor: function(str, s = 50, l = 60) {
      let hash = 0;
      for (let i = 0; i < str.length; i++) {
        hash = str.charCodeAt(i) + ((hash << 5) - hash);
      }

      let h = hash % 360;

      return "hsl(" + h + ", " + s + "%, " + l + "%)";
    },

    // API
    $api: apiCall,

    // Flash
    $flash: function(message, type = "info", icon = false, timeout = 3500) {
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
        html: true,
        position: "top-right",
        timeout,
        closeBtn: "-"
      });
    },

    // Create slug from string
    // Source: https://codepen.io/tatthien/pen/xVBxZQ
    $slug: function(original) {
      const a =
        "àáäâãåăæąçćčđďèéěėëêęğǵḧìíïîįłḿǹńňñòóöôœøṕŕřßşśšșťțùúüûǘůűūųẃẍÿýźžż·/_,:;";
      const b =
        "aaaaaaaaacccddeeeeeeegghiiiiilmnnnnooooooprrsssssttuuuuuuuuuwxyyzzz------";
      const p = new RegExp(a.split("").join("|"), "g");

      return original
        .toString()
        .toLowerCase()
        .replace(/\s+/g, "-") // Replace spaces with -
        .replace(p, c => b.charAt(a.indexOf(c))) // Replace special characters
        .replace(/&/g, "-and-") // Replace & with 'and'
        .replace(/[^\w-]+/g, "") // Remove all non-word characters
        .replace(/--+/g, "-") // Replace multiple - with single -
        .replace(/^-+/, "") // Trim - from start of text
        .replace(/-+$/, ""); // Trim - from end of text
    },

    // Convert array of objects into object of objects (with IDs as keys)
    $makeIdObject(array) {
      let result = {};

      array.map(item => {
        result[item.id] = item;
      });

      return result;
    },

    // Simple global database interface

    // @key: Index for the value to be stored under
    // @value: null = read value; undefined = delete value; other = set value
    // @personal: true = value will be uncached on logout
    $db(key, value = null, personal = false) {
      let dbKey = personal ? "dbPersonal" : "db";

      // Workaround for personal GET and DELETE
      if (
        (value === null || value === DB_DELETION_CONSTANT) &&
        typeof Vue.prototype[dbKey][key] == "undefined"
      )
        dbKey = "dbPersonal";

      // Select request
      if (value === null) return Vue.prototype[dbKey][key];

      // Delete request
      if (value === DB_DELETION_CONSTANT)
        return Vue.delete(Vue.prototype[dbKey], key);

      // Insert/update request
      return Vue.set(Vue.prototype[dbKey], key, value);
    },

    // Show basic confirm dialog
    $confirm(settings) {
      let defaults = {
        class: "simple-confirm-dialog",
        title: this.$tr("general.confirmModal.title", null, false),
        message: null,
        cancel: this.$tr("general.confirmModal.cancel", null, false),
        ok: {
          label: this.$tr("general.confirmModal.confirm", null, false)
        }
      };

      // Merge settings with defaults
      settings = { ...defaults, ...settings };

      if (settings.confirm) settings.ok.label = settings.confirm;

      return this.$q.dialog(settings);
    }
  }
});

// Auth
import router from "./router";
Vue.router = router;
Vue.use(VueAuth, {
  tokenStore: "localStorage",
  loginData: {
    url: config.api.baseURL + "login",
    method: "POST",
    headerToken: "Authorization"
  },
  fetchData: {
    url: config.api.baseURL + "user",
    method: "GET"
  },
  authRedirect: () => this.$path("auth.login"),
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

// Custom auth facade
Vue.prototype.$auth.isAdmin = () => {
  let $auth = Vue.prototype.$auth;
  return $auth.check() && $auth.user() && $auth.user().role === "admin";
};

// Custom cache DB mechanism
Vue.prototype.db = {};
Vue.prototype.dbPersonal = {};

// $isPDS bool
Vue.prototype.$isPDS = process.env.VUE_APP_IS_PDS === "true";

new Vue({
  router,
  i18n,
  render: h => h(App),
  mounted() {
    let path = this.$route.path;
    let route = this.$route.matched[1];
    let regex = route.regex;

    // Actual path doesn't match primary route
    // -> alias was accessed
    // -> switch language
    if (!path.match(regex)) this.$i18n.locale = "en";
  }
}).$mount("#app");

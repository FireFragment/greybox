import Vue from "vue";

import "./styles/quasar.styl";
import "quasar/dist/quasar.ie.polyfills";
import lang from "quasar/lang/cs.js";
import "@quasar/extras/fontawesome-v5/fontawesome-v5.css";
import Quasar from "quasar";

Vue.use(Quasar, {
  config: {
    loadingBar: {
      color: "light-blue-5"
    }
  },
  lang: lang,
  extras: ["mdi-v5", "fontawesome-v5"],
  framework: {
    iconSet: "fontawesome-v5"
  }
});

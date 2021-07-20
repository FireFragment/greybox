/* eslint-disable */
import { boot } from 'quasar/wrappers';
import { createI18n } from 'vue-i18n';
import config from "../config";

// Import localization data from JSONs
import i18nConfig from '../translation/config.json';

const translationFiles = i18nConfig.files;
const languageData = i18nConfig.languages;

let translations: Record<string, any> = {};

for (let locale in languageData) {
  //let item = languageData[locale];
  let languageObject: Record<string, any> = {};

  for (let index in translationFiles) {
    // Dot notation file path
    let file = translationFiles[index];

    // Path to file (containing slashes)
    let filePath = file.replace(/\./g, '/');

    // Actual translation object
    let content = require(`../translation/${locale}/${filePath}.json`);

    // Convert deep paths intohttps://github.com/gruntjs/grunt-contrib-sass actual objects
    let depth = file.split('.');

    if (depth.length > 1) {
      for (let i = depth.length - 1; i > 0; i--) {
        content = {
          [depth[i]]: content,
        };
      }
    }

    if (!languageObject[depth[0]]) languageObject[depth[0]] = {};

    for (let index in content) languageObject[depth[0]][index] = content[index];
  }

  translations[locale] = languageObject;
}

// EN is default with PDS
if (process.env.IS_PDS === 'true') i18nConfig.default = 'en';

const i18n = createI18n({
  locale: i18nConfig.default,
  fallbackLocale: i18nConfig.fallback,
  messages: translations,
  silentFallbackWarn: !config.debug,
  silentTranslationWarn: !config.debug,
});

export default boot(({ app }) => {
  // Set i18n instance on app
  app.use(i18n);
});

export { i18n };

import { boot } from 'quasar/wrappers';
import { createI18n } from 'vue-i18n';
import { setLocaleToRoute } from 'src/router';
import { DateTime } from 'src/types/general';
import config from '../config';

// Import localization data from JSONs
import i18nConfig from '../translation/config';

// EN is default with PDS
if (process.env.IS_PDS === 'true') i18nConfig.default = 'en';

export interface TranslationPrefixData {
  translationPrefix: string
}

export interface TranslatedString {
  cs: string;
  en: string;
}

export interface TranslatedDatabaseString extends TranslatedString {
  id: number;
  // eslint-disable-next-line camelcase
  created_at: DateTime;
  // eslint-disable-next-line camelcase
  updated_at: DateTime;
}

const i18n = createI18n({
  locale: i18nConfig.default,
  fallbackLocale: i18nConfig.fallback,
  messages: i18nConfig.messages,
  silentFallbackWarn: !config.debug,
  silentTranslationWarn: !config.debug,
});

export default boot(({
  app,
  router,
}) => {
  // Set i18n instance on app
  app.use(i18n);

  void router.isReady()
    .then(() => {
      setLocaleToRoute(router.currentRoute.value);
    });
});

export { i18n };

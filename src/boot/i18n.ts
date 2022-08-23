import { boot } from 'quasar/wrappers';
import { createI18n } from 'vue-i18n';
import { DateTime } from 'src/types/general';
import { $isPDS, $tr, $path } from 'boot/custom';
import { Router } from 'src/router';
import { user } from 'src/boot/auth';
import config from '../config';
// Import localization data from JSONs
import i18nConfig from '../translation/config';
import { apiCall } from './api';

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

export const switchLocale = async (locale: string) => {
  if (i18n.global.locale === locale) return;

  // update preference
  const userObj = user();
  if (userObj) {
    userObj.preferred_locale = locale;
    apiCall({
      url: `user/${userObj.id}`,
      sendToken: true,
      method: 'put',
      data: {
        preferred_locale: locale,
      },
    });
  }

  // current URL
  const originalPath = $tr(
    `paths.${String(Router.currentRoute.value.name)}`,
  );

  // change locale
  i18n.global.locale = locale;

  // new URL
  // console.log(Router);
  const newPath = $tr(`paths.${String(Router.currentRoute.value.name)}`);

  // get URL from router
  // console.log(Router);
  const url = { ...Router.currentRoute.value };

  // Redirect here before route switch to avoid redundant redirect error
  let midRedirect = 'about';
  // Homepage cases
  if (originalPath === '') {
    url.path = '/en/';
  } else if (newPath === '') {
    url.path = '/';
  } else {
    // replace url in router with localized one
    url.path = url.path.replace(String(originalPath), String(newPath));
    midRedirect = 'home';
  }

  await Router.push(
    $path(midRedirect),
  );

  // go to new url
  await Router.replace({
    path: url.path,
  });
};

export default boot(({
  app,
  router,
}) => {
  // Set i18n instance on app
  app.use(i18n);

  void router.isReady()
    .then(() => {
      // PDS mode, homepage -> switch language to EN (without changing route)
      if ($isPDS && router.currentRoute.value.name === 'home') {
        i18n.global.locale = 'en';
      }
    });
});

export { i18n };

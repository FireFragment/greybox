import { boot } from 'quasar/wrappers';
import { Quasar } from 'quasar';
import { createI18n } from 'vue-i18n';
import { DateTime } from 'src/types/general';
import {
  $isPDS, $tr, $path, TranslationValue,
} from 'boot/custom';
import { Router } from 'src/router';
import { user } from 'src/boot/auth';
import { translateLink } from 'src/router/helpers';
import config from '../config';
// Import localization data from JSONs
import i18nConfig, { Lang, langs } from '../translation/config';
import { apiCall } from './api';

export interface TranslationPrefixData {
  translationPrefix: string
}

export type TranslatedString = Record<Lang, string>;

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

export const getAllTranslations = (key: string): TranslatedString => (
  <TranslatedString>Object.fromEntries(
    new Map(langs.map((lang) => ([lang, $tr(key, null, false, lang)]))),
  )
);

export const translationMatchesInAnyLanguage = (
  key: string | TranslatedString, comparison: string,
): boolean => (
  $tr(key, null, false, 'cs') === comparison
  || $tr(key, null, false, 'en') === comparison
);

// Replaces all segments by other language values in paths localization
// (if not found, segment stays the same - e.g. for ID)
export const getCurrentRouteTranslatedPath = (): TranslationValue => $tr(
  `paths.${String(Router.currentRoute.value.meta.translationName ?? Router.currentRoute.value.name)}`,
);

export const switchQuasarLanguage = async (locale: Lang): Promise<void> => {
  // change quasar language (for components labels etc)
  const langIso = locale === 'en' ? `${locale}-US` : locale;
  try {
    await import(
      `quasar/lang/${langIso}`
    ).then(({ default: lang }) => {
      Quasar.lang.set(lang);
    });
  } catch (err) {
    void (0);
  }
};

export const switchLocale = async (locale: Lang): Promise<void> => {
  // eslint-disable-next-line @typescript-eslint/no-unsafe-member-access, no-console
  // console.log(Router);
  // console.log(`paths.${String(Router.currentRoute.value.meta.translationName)}`);
  // console.log(getCurrentRouteTranslatedPath());
  // update preference
  const userObj = user();
  if (userObj) {
    userObj.preferred_locale = locale;
    void apiCall({
      url: `user/${userObj.id}`,
      sendToken: true,
      method: 'put',
      data: {
        preferred_locale: locale,
      },
    });
  }

  void switchQuasarLanguage(locale);

  // current URL
  const originalPath = <string>getCurrentRouteTranslatedPath();

  // change locale
  i18n.global.locale = locale;

  // new URL
  const newPath = <string>getCurrentRouteTranslatedPath();
  const currentPath = Router.currentRoute.value.path;

  // Redirect here before route switch to avoid redundant redirect error
  await Router.push(
    $path(originalPath === '' || newPath === '' ? 'about' : 'home'),
  );

  // go to new url
  await Router.replace({
    path: translateLink(originalPath, newPath, currentPath),
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

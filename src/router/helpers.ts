import { LocationAsRelativeRaw, MatcherLocationAsPath } from 'vue-router';
import { i18n } from 'boot/i18n';
import i18nConfig, { Lang } from 'src/translation/config';
import { aliasRouteLang, primaryRouteLang, Router } from 'src/router/index';
import { $tr, TranslationValue } from 'boot/custom';

export const getRouteTranslatedPath = (name: string, locale: Lang): TranslationValue => $tr(
  `paths.${name}`,
  null,
  false,
  locale,
);

export const translatedRouteLink = (
  route: string | LocationAsRelativeRaw,
): MatcherLocationAsPath => {
  // TODO - extract common functionality from switchLocale
  const {
    locale,
  } = i18n.global;
  const linkInDefaultLang = Router.resolve(route);

  const activeLocale = (locale || i18nConfig.default);

  if (activeLocale === primaryRouteLang) {
    return {
      path: linkInDefaultLang.path,
    };
  }

  const translationKey = typeof route === 'string' ? route : <string>(linkInDefaultLang.meta?.translationName ?? route.name);

  // current URL
  const originalPath = getRouteTranslatedPath(translationKey, primaryRouteLang);

  // new URL
  const newPath = getRouteTranslatedPath(translationKey, aliasRouteLang);

  // get URL from router
  const url = { ...linkInDefaultLang };

  // Homepage cases
  if (originalPath === '') {
    url.path = '/en/';
  } else if (newPath === '') {
    url.path = '/';
  } else {
    // replace url in router with localized one
    url.path = url.path.replace(String(originalPath), String(newPath));
  }

  return {
    path: url.path,
  };
};

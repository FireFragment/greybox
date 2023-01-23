import { LocationAsRelativeRaw, MatcherLocationAsPath } from 'vue-router';
import { i18n } from 'boot/i18n';
import i18nConfig, { Lang, Translations } from 'src/translation/config';
import { aliasRouteLang, primaryRouteLang, Router } from 'src/router/index';
import { $tr, TranslationValue } from 'boot/custom';

export const getRouteTranslatedPath = (name: string, locale: Lang): TranslationValue => $tr(
  `paths.${name}`,
  null,
  false,
  locale,
);

type TranslatedUrlPart = {
  primary: string;
  alias: string;
};

// Replace path in primary translation language for an alias
export const translateLink = (
  parts: TranslatedUrlPart[],
  originalLink: string,
): string => {
  const { primary: primaryPath, alias: aliasPath } = parts[0];

  // Homepage cases
  if (primaryPath === '') {
    return `/${aliasRouteLang}/`;
  }
  if (aliasPath === '') {
    return '/';
  }

  // Replace all parts of a URL
  return parts.reduce((acc, { primary, alias }) => acc.replace(primary, alias), originalLink);
};

export const translatedRouteLink = (
  route: string | LocationAsRelativeRaw,
): MatcherLocationAsPath => {
  const {
    locale,
  } = i18n.global;
  const linkInDefaultLang = Router.resolve(route);

  const activeLocale = (locale || i18nConfig.default);

  // Active locale is primary language -> return path
  if (activeLocale === primaryRouteLang) {
    return {
      path: linkInDefaultLang.path,
    };
  }

  // Get main path translation key
  const translationKey = typeof route === 'string'
    ? route
    : <string>(linkInDefaultLang.meta?.translationName ?? route.name);

  // Get additional translation keys for params etc.
  const translationKeys = [
    translationKey,
    ...(<string[]>linkInDefaultLang.meta?.additionalTranslations ?? []),
  ];

  const translatedParts: TranslatedUrlPart[] = translationKeys
    .map((key) => ({
      primary: <string>getRouteTranslatedPath(key, primaryRouteLang), // current URL
      alias: <string>getRouteTranslatedPath(key, aliasRouteLang), // new URL
    }));

  return {
    path: translateLink(translatedParts, linkInDefaultLang.path),
  };
};

export const findValueInNestedObject = (obj: Translations | string, value: string, path: string)
// eslint-disable-next-line consistent-return
: string | void => {
  if (typeof obj === 'string') return;
  // eslint-disable-next-line no-restricted-syntax
  for (const elem of Object.keys(obj)) {
    const curr: Translations | string = obj[elem];
    if (typeof curr === 'object' && curr !== null) {
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
      const retval = findValueInNestedObject(curr, value, `${path + elem}.`);
      // eslint-disable-next-line consistent-return
      if (retval) return retval;
    // eslint-disable-next-line consistent-return
    } else if (curr === value) return `${path}${elem}`;
  }
};

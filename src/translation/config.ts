import { user } from 'src/boot/auth';

// Kinda hard to dynamically generate this list (both Kuxa and Laďa tried)
const files: string[] = [
  'general', 'auth', 'paths', 'event', 'admin', 'myDebates', 'user', 'titles', 'validation',
];

// Nested object
export interface Translations {
  [path: string]: Translations | string
}

const loadMessages = (locale: string): Translations => {
  const messages: Translations = {};

  files.forEach((file) => {
    // eslint-disable-next-line max-len
    // eslint-disable-next-line global-require,import/no-dynamic-require,@typescript-eslint/no-unsafe-return,@typescript-eslint/no-unsafe-assignment
    messages[file] = require(`./${locale}/${file}.json`);
  });

  return messages;
};

export const langs = ['cs', 'en'] as const;
export type Lang = typeof langs[number];

export const primaryLocale: Lang = 'cs';

export const defaultLocale: Lang = user()?.preferred_locale ?? 'cs';
export const fallbackLocale: Lang = defaultLocale === 'cs' ? 'en' : 'cs';

export default {
  default: defaultLocale,
  fallback: fallbackLocale,
  languages: {
    cs: {
      en: 'Czech',
      native: 'Čeština',
    },
    en: {
      en: 'English',
      native: 'English',
    },
  } as Record<Lang, Record<string, string>>,
  messages: {
    cs: loadMessages('cs'),
    en: loadMessages('en'),
  },
};

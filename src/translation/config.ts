import { user } from 'src/boot/auth';

const files: string[] = [
  'general', 'auth', 'paths', 'event', 'admin', 'myDebates', 'user',
];

// Nested object
interface Translations {
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

const defaultLocale = user()?.preferred_locale ?? 'cs';
const fallbackLocale = defaultLocale === 'cs' ? 'en' : 'cs';

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
  },
  messages: {
    cs: loadMessages('cs'),
    en: loadMessages('en'),
  },
};

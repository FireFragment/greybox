const files: string[] = [
  'general', 'auth', 'paths', 'tournament', 'admin',
];

// TODO - better typing
const loadMessages = (locale: string): Record<string, any> => {
  const messages: Record<string, any> = {};

  files.forEach((file) => {
    // eslint-disable-next-line max-len
    // eslint-disable-next-line global-require,import/no-dynamic-require,@typescript-eslint/no-unsafe-return,@typescript-eslint/no-unsafe-assignment
    messages[file] = require(`./${locale}/${file}.json`);
  });

  return messages;
};

export default {
  default: 'cs',
  fallback: 'en',
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

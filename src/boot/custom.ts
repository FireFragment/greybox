/* eslint-disable */
const Bugsnag = require('@bugsnag/js');
const BugsnagPluginVue = require('@bugsnag/plugin-vue');
import config from '../config';
import { Notify } from 'quasar';

const smartformModule = require('@smartform.cz/smartform');
import { boot } from 'quasar/wrappers';
import { i18n } from 'boot/i18n';
import i18nConfig from '../translation/config.json';

export const $tr = function (key: string, options: Record<string, unknown> | null = null, usePrefix = true) {
  // Translate object received from API
  const {
    locale,
    t,
    tm,
  } = i18n.global;
  if (typeof key === 'object') {
    // @ts-ignore
    let activeLocale: string = locale || i18nConfig.default;

    if (!key || !key[activeLocale]) return '';

    return key[activeLocale];
  }

  // Translate string from JSON
  // @ts-ignore
  let prefix = this ? this.$.data.translationPrefix : null;

  // Use prefix
  if (prefix && usePrefix) key = prefix + key;

  if (options !== null) {
    return t(key, options);
  }
  return tm(key);
};

export const $path = function (route: string) {
  return '/' + $tr(`paths.${route}`, null, false);
};

export const $flash = function (message: string, type: string = 'info', icon: string | undefined = undefined, timeout: number = 3500) {
  let color: string | undefined = undefined;
  if (type === 'success' || type === 'done') {
    color = 'green';
    if (!icon) icon = 'check';
  } else if (type === 'error' || type === 'fail') {
    color = 'red';
    if (!icon) icon = 'times';
  }

  return Notify.create({
    color,
    icon: icon ? 'fas fa-' + icon : undefined,
    message: message,
    html: true,
    position: 'top-right',
    timeout,
    closeBtn: '-',
  });
}

export default boot(({ app }) => {
  // $isPDS bool
  app.config.globalProperties.$isPDS = process.env.IS_PDS === 'true';

  // Initialize SmartForms
  smartformModule.load();
  // @ts-ignore
  window.smartform.beforeInit = () => {
    // @ts-ignore
    window.smartform.setClientId('8ndPcVUJ5B');
  };

  // Bugsnag error monitoring
  if (typeof process.env.BUGSNAG_KEY === 'string') {
    Bugsnag.start({
      apiKey: process.env.BUGSNAG_KEY,
      plugins: [new BugsnagPluginVue()],
      releaseStage: process.env.STAGE,
    });

    const bugsnagVue = Bugsnag.getPlugin('vue');
    app.use(bugsnagVue);
  }

  // Mixins
  const DB_DELETION_CONSTANT = 'DELETE-THIS-DATABASE-ITEM'; // when DB item is set to this value, it will be deleted
  app.mixin({
    data() {
      return {
        apiSettings: config.api,
        env: process.env.FULL_ENV,
        DB_DEL: DB_DELETION_CONSTANT,
      };
    },
    methods: {
      // Translation simplification
      // If translationPrefix data is set in component, it will be automcatically placed in front of translation keys
      $tr,

      // Check if translation exists
      $trExists: function (key: string, usePrefix: boolean = true) {
        // Translate string from JSON
        let prefix = this ? this.$.data.translationPrefix : null;

        // Use prefix
        if (prefix && usePrefix) key = prefix + key;

        return this.$te(key);
      },

      // Get path to route
      $path,

      $stringToHslColor: function (str: string, s: number = 100) {
        let hash = 0;
        for (let i = 0; i < str.length; i++) {
          hash = str.charCodeAt(i) + ((hash << 5) - hash);
        }

        const h = hash % 360;
        const l = Math.abs((h * 100) % 40) + 20;

        return 'hsl(' + h + ', ' + s + '%, ' + l + '%)';
      },

      // Flash
      $flash,

      // Create slug from string
      // Source: https://codepen.io/tatthien/pen/xVBxZQ
      $slug: function (original: string) {
        const a =
          'àáäâãåăæąçćčđďèéěėëêęğǵḧìíïîįłḿǹńňñòóöôœøṕŕřßşśšșťțùúüûǘůűūųẃẍÿýźžż·/_,:;';
        const b =
          'aaaaaaaaacccddeeeeeeegghiiiiilmnnnnooooooprrsssssttuuuuuuuuuwxyyzzz------';
        const p = new RegExp(a.split('')
          .join('|'), 'g');

        return original
          .toString()
          .toLowerCase()
          .replace(/\s+/g, '-') // Replace spaces with -
          .replace(p, (c: string) => b.charAt(a.indexOf(c))) // Replace special characters
          .replace(/&/g, '-and-') // Replace & with 'and'
          .replace(/[^\w-]+/g, '') // Remove all non-word characters
          .replace(/--+/g, '-') // Replace multiple - with single -
          .replace(/^-+/, '') // Trim - from start of text
          .replace(/-+$/, ''); // Trim - from end of text
      },

      // Convert array of objects into object of objects (with IDs as keys)
      $makeIdObject(array: any) {
        let result: Record<string | number, any> = {};

        array.map((item: Record<string, string | number>) => {
          result[item.id] = item;
        });

        return result;
      },

      // Simple global database interface

      // @key: Index for the value to be stored under
      // @value: null = read value; undefined = delete value; other = set value
      // @personal: true = value will be uncached on logout
      $db(key: any, value = null, personal: boolean = false) {
        let dbKey = personal ? 'dbPersonal' : 'db';

        // Workaround for personal GET and DELETE
        if (
          (value === null || value === DB_DELETION_CONSTANT) &&
          typeof app.config.globalProperties[dbKey][key] == 'undefined'
        ) {
          dbKey = 'dbPersonal';
        }

        // Select request
        if (value === null) return app.config.globalProperties[dbKey][key];

        // Delete request
        if (value === DB_DELETION_CONSTANT) {
          return delete app.config.globalProperties[dbKey][key];
        }

        // Insert/update request
        return app.config.globalProperties[dbKey][key] = value;
      },

      // Show basic confirm dialog
      $confirm(settings: any) {
        let defaults = {
          class: 'simple-confirm-dialog',
          title: this.$tr('general.confirmModal.title', null, false),
          message: null,
          cancel: this.$tr('general.confirmModal.cancel', null, false),
          ok: {
            label: this.$tr('general.confirmModal.confirm', null, false),
          },
        };

        // Merge settings with defaults
        settings = { ...defaults, ...settings };

        if (settings.confirm) settings.ok.label = settings.confirm;
        return this.$q.dialog(settings);
      },
    },
  });

  /*
  app.config.globalProperties.$auth = {
    check: () => true,
    user: () => ({
      id: 5,
      username: 'kuxik009@gmail.com',
    }),
    token: () => {},
  };
  */
  // Auth
  /*
  app.use(VueAuth, {
    loginData: {
      headerToken: 'Authorization',
    },
    fetchData: {
      url: config.api.baseURL + 'user',
      method: 'GET',
    },
    authRedirect: () => $path('auth.login'),
  });
  */

  // Custom cache DB mechanism
  app.config.globalProperties.db = {};
  app.config.globalProperties.dbPersonal = {};

});

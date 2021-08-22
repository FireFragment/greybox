import { boot } from 'quasar/wrappers';

const validateEmail = (email: string): boolean => {
  const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(String(email)
    .toLowerCase());
};

interface Validators {
  validateEmail: (email: string) => boolean
}

const validators: Validators = {
  validateEmail,
};

// Required for TypeScript to work with global properties
declare module '@vue/runtime-core' {
  interface ComponentCustomProperties {
    $validators: Validators
  }
}

export default boot(({ app }) => {
  app.config.globalProperties.$validators = validators;
});

export { validators };

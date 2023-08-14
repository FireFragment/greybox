import { boot } from 'quasar/wrappers';
import { $tr } from 'boot/custom';

const validateEmail = (email: string): boolean => {
  const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(String(email)
    .toLowerCase());
};

const email = (val: string): boolean | string => validateEmail(val) || <string> $tr('general.form.errors.email', null, false);

const nonEmpty = (val: string): boolean | string => (val && val.length > 0) || <string> $tr('general.form.errors.nonEmpty', null, false);

type Validator = (val: string) => boolean | string;

type Validators = Record<'email' | 'nonEmpty', Validator>;

const validators: Validators = {
  email,
  nonEmpty,
};

// Required for TypeScript to work with global properties
declare module '@vue/runtime-core' {
  interface ComponentCustomProperties {
    $validators: Validators;
  }
}

export default boot(({ app }) => {
  app.config.globalProperties.$validators = validators;
});

export { validators };

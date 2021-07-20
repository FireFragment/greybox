<template>
  <q-select
    outlined
    :model-value="value"
    @update:model-value="$emit('input', $event)"
    :options="options"
    :option-label="item => (typeof item === 'object' ? $tr(item.label) : item)"
    :label="$tr('general.form.fields.country')"
    class="q-pt-sm q-mb-sm col-12 col-md-4"
    input-debounce="0"
    hide-selected
    fill-input
    lazy-rules
    use-input
    @filter="filterFn"
  >
  </q-select>
</template>

<script>
export default {
  name: "CountrySelect",
  props: ["value"],
  data() {
    return {
      options: [],
      allOptions: [],

      // Default countries based on locale
      defaultCountries: {
        cs: "CZ"
      }
    };
  },
  created() {
    this.loadCountries().then(data => {
      this.allOptions = data;

      let value = this.value;

      // Pick default option based on selected locale
      if (this.defaultCountries[this.$i18n.locale] && !value)
        value = this.defaultCountries[this.$i18n.locale];

      if (value && typeof value === "string")
        this.$emit("input", this.getCountryByCode(value));

      this.options = data;
    });
  },
  methods: {
    loadCountries() {
      return new Promise((resolve, reject) => {
        if (this.$db("countries-select"))
          return resolve(this.$db("countries-select"));

        this.$api({
          url: "country",
          method: "get"
        })
          .then(d => {
            let countries = [];
            let data = d.data;

            for (let code of Object.keys(data)) {
              countries.push({
                value: code,
                label: data[code]
              });
            }

            this.$db("countries-select", countries);
            resolve(countries);
          })
          .catch(reject);
      });
    },

    getCountryByCode(code) {
      let filtered = this.allOptions.filter(item => item.value === code);

      if (filtered.length) return filtered[0];

      return null;
    },

    filterFn(val, update) {
      val = val.trim();
      if (val === "") {
        update(() => {
          this.options = this.allOptions;
        });
        return;
      }

      update(() => {
        const needle = val.toLowerCase();
        this.options = this.allOptions.filter(item => {
          if (typeof item === "object")
            return (
              this.$tr(item.label)
                .toLowerCase()
                .includes(needle) || item.value.toLowerCase().includes(needle)
            );
          return false;
        });
      });
    }
  }
};
</script>

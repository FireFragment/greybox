<template>
  <q-dialog :value="visible" @input="stateChange">
    <q-card class="dialog-small">
      <q-card-section class="row items-center">
        <div class="text-h6">
          {{ $tr("modal.title") }}
        </div>
        <q-space />
        <q-btn icon="close" flat round dense v-close-popup />
      </q-card-section>

      <q-card-section>
        <q-form ref="q-form">
          <div class="row q-col-gutter-md q-pb-sm">
            <q-input
              outlined
              :label="$tr('fields.name') + ' *'"
              class="col-12 q-pb-md"
              lazy-rules
              v-model="values.name"
              :rules="[
                val =>
                  (val && val.length > 0) ||
                  $tr('general.form.fieldError', null, false)
              ]"
            >
              <template v-slot:prepend>
                <q-icon name="fas fa-user" />
              </template>
            </q-input>
            <q-input
              outlined
              :label="$tr('fields.email')"
              class="col-12"
              v-model="values.email"
            >
              <template v-slot:prepend>
                <q-icon name="fas fa-at" />
              </template>
            </q-input>
            <q-input
              v-model="values.registration_no"
              outlined
              :label="$tr('fields.registration_no')"
              class="col-12"
            >
              <template v-slot:prepend>
                <q-icon name="fas fa-hashtag" />
              </template>
            </q-input>

            <q-input
              v-model="values.street"
              outlined
              :label="$tr('fields.street')"
              class="col-12"
              :input-class="
                'smartform-street-and-number ' + 'smartform-instance-' + _uid
              "
              lazy-rules
            >
              <template v-slot:prepend>
                <q-icon name="fas fa-home" />
              </template>
            </q-input>

            <q-input
              v-model="values.city"
              outlined
              :label="$tr('fields.city')"
              class="col-7"
              :input-class="'smartform-city ' + 'smartform-instance-' + _uid"
              lazy-rules
              hint=""
            >
              <template v-slot:prepend>
                <q-icon name="fas fa-city" />
              </template>
            </q-input>

            <q-input
              v-model="values.zip"
              outlined
              :label="$tr('fields.zip')"
              class="col-5"
              :input-class="'smartform-zip ' + 'smartform-instance-' + _uid"
              mask="### ##"
              fill-mask="_"
              :hint="$tr('fieldNotes.example') + ' 796 01'"
              lazy-rules
            >
              <template v-slot:prepend>
                <q-icon name="fas fa-file-archive" />
              </template>
            </q-input>
          </div>
        </q-form>
      </q-card-section>
      <q-card-actions class="float-actions">
        <q-btn
          label="Odstranit údaje"
          color="red"
          flat
          :class="{ hide: !client }"
        />
        <q-btn label="Uložit" color="primary" />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script>
import { EventBus } from "../../event-bus";

export default {
  name: "BillingEditDialog",
  props: {
    visible: Boolean,
    client: Object
  },
  data() {
    return {
      translationPrefix: "tournament.checkout.billing.",
      values: {
        name: null,
        email: null,
        registration_no: null,
        street: null,
        city: null,
        zip: null
      }
    };
  },
  created() {
    // Smartform autocomplete select
    EventBus.$on("smartform", data => {
      // If instance ID is this form
      if (data.instance.substr(-(this._uid + "").length) == this._uid)
        this.values[data.field] = data.value;
    });
  },
  methods: {
    stateChange(isVisible) {
      if (isVisible) this.$nextTick(this._dialogMounted);

      return this.$emit("state-change", isVisible);
    },

    _dialogMounted() {
      Object.keys(this.values).forEach(key => {
        this.values[key] =
          this.client && this.client[key] ? this.client[key] : null;
      });

      this._initSmartform();
    },

    _initSmartform() {
      // Renitialize smartform
      window.smartform.rebindAllForms(true, () => {
        // Loop through instances
        window.smartform.getInstanceIds().forEach(id => {
          let instance = window.smartform.getInstance(id);

          // Set limit to 3 results for every field
          [
            "smartform-street-and-number",
            "smartform-city",
            "smartform-zip"
          ].forEach(input => {
            instance.getBox(input).setLimit(3);
          });

          // Run this callback on selection
          instance.setSelectionCallback((element, value, fieldType) => {
            let field = fieldType.substr("10");

            let varName = field !== "street-and-number" ? field : "street";

            // Emit global event so other form instances can receive it
            EventBus.$emit("smartform", {
              instance: id,
              field: varName,
              value: value
            });
          });
        });
      });
    }
  }
};
</script>

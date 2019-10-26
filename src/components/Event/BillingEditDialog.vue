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
              class="col-12"
              lazy-rules
              :rules="[
                val =>
                  (val && val.length > 0) ||
                  $tr('general.form.fieldError', null, false)
              ]"
            />
            <q-input outlined :label="$tr('fields.full_name')" class="col-12">
              <template v-slot:prepend>
                <q-icon name="fas fa-user" />
              </template>
            </q-input>
            <q-input outlined :label="$tr('fields.email')" class="col-12">
              <template v-slot:prepend>
                <q-icon name="fas fa-at" />
              </template>
            </q-input>
            <q-input
              outlined
              :label="$tr('fields.registration_no')"
              class="col-12"
            >
              <template v-slot:prepend>
                <q-icon name="fas fa-hashtag" />
              </template>
            </q-input>

            <q-input
              outlined
              :label="$tr('fields.street') + ' *'"
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
              outlined
              :label="$tr('fields.city') + ' *'"
              class="q-pt-sm col-7"
              :input-class="'smartform-city ' + 'smartform-instance-' + _uid"
              lazy-rules
            >
              <template v-slot:prepend>
                <q-icon name="fas fa-city" />
              </template>
            </q-input>

            <q-input
              outlined
              :label="$tr('fields.zip') + ' *'"
              class="q-pt-sm col-5"
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

            <!-- TODO coutnry select -->
          </div>
        </q-form>
      </q-card-section>
      <q-card-actions class="bg-white text-teal">
        <q-btn label="Odstranit údaje" />
        <q-btn label="Uložit" />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script>
export default {
  name: "BillingEditDialog",
  props: {
    visible: Boolean,
    client: Object
  },
  data() {
    return {
      translationPrefix: "tournament.checkout.billing."
    };
  },
  methods: {
    stateChange(value) {
      return this.$emit("state-change", value);
    }
  }
};
</script>

<template>
  <div>
    <q-card flat bordered class="my-card bg-grey-1">
      <q-card-section class="header">
        <div class="row items-center no-wrap">
          <div class="col">
            <q-btn
              color="grey-7"
              round
              flat
              :icon="'fas fa-' + (hideContent ? 'plus' : 'minus')"
              class="float-left"
              size="sm"
              @click="hideContent = !hideContent"
            />
            <div class="text-h6" v-if="$refs.form">
              {{ $refs.form.name }} {{ $refs.form.surname }}
            </div>
          </div>

          <div class="col-auto">
            <q-btn color="grey-7" round flat icon="fas fa-trash" size="sm" />
          </div>
        </div>
      </q-card-section>
      <slide-up-down :active="!hideContent" :duration="500">
        <q-card-section>
          <form-fields ref="form" />
        </q-card-section>
      </slide-up-down>
    </q-card>
  </div>
</template>

<script>
import formFields from "./FormFields";
export default {
  components: {
    formFields
  },
  data() {
    return {
      hideContent: false
    };
  },
  name: "TeamPersonCard"
};
</script>

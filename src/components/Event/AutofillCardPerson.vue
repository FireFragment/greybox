<template>
  <q-item
    :clickable="!registered"
    :v-ripple="!registered"
    dense
    :class="'q-pt-sm q-pb-sm' + (registered ? ' registered' : '')"
    @click="$emit('click')"
    @mouseenter="$emit('mouseenter')"
    @mouseleave="$emit('mouseleave')"
  >
    <q-item-section>
      {{ person.name + " " + person.surname }}
    </q-item-section>
    <q-item-section avatar>
      <q-avatar
        :style="'background-color: ' + $stringToHslColor(person.name)"
        :class="{ deleting: showDeleteButton }"
        size="30px"
        @click.stop="$emit('deletePerson')"
      >
        <q-tooltip anchor="center left" self="center right" :offset="[10, 10]">
          {{ $tr("removeTooltip") }}
        </q-tooltip>
        <img src="https://cdn.quasar.dev/img/avatar.png" v-if="!true" />
        <template>{{ person.name.substr(0, 1).toUpperCase() }}</template>
        <q-icon name="fas fa-trash" />
      </q-avatar>
    </q-item-section>
  </q-item>
</template>

<script>
export default {
  name: "AutofillCardPerson",

  props: ["person", "showDeleteButton", "registered"],

  data() {
    return {
      translationPrefix: "tournament.autofill."
    };
  }
};
</script>

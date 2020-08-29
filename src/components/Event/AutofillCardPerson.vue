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
        size="30px"
        @click.stop="$emit('deletePerson')"
      >
        <img src="https://cdn.quasar.dev/img/avatar.png" v-if="!true" />
        <template>{{ person.name.substr(0, 1).toUpperCase() }}</template>
        <q-icon
          :style="'background-color: ' + $stringToHslColor(person.name)"
          :class="{ shown: showDeleteButton }"
          name="fas fa-trash"
        />
      </q-avatar>
    </q-item-section>
  </q-item>
</template>

<script>
export default {
  name: "AutofillCardPerson",

  props: ["person", "showDeleteButton", "registered"]
};
</script>

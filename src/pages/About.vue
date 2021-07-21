<template>
  <q-page padding>
    <h1 class="text-center text-h4">{{ $tr("general.aboutUs") }}</h1>
    <p>
      {{ $tr("general.aboutUsText") }}
      <a href="mailto:info@debatovani.cz">info@debatovani.cz</a>
    </p>
  </q-page>
</template>

<script>
export default {
  name: 'PageAbout',
};
</script>

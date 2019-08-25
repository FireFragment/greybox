<template>
  <div class="team-form">
    <person-card />
  </div>
</template>

<script>
import personCard from "./TeamPersonCard";
export default {
  components: {
    personCard
  },
  name: "TeamForm"
};
</script>

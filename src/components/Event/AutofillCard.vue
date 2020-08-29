<template>
  <q-list
    class="rounded-borders shadow-2 q-pb-sm bg-white debaters-sticky-card"
  >
    <q-scroll-area style="height: calc(100vh - 130px);">
      <div
        class="empty-info"
        v-if="
          !registeredPeople.length &&
            !notRegisteredPeople.length &&
            !showLoading
        "
      >
        {{ $tr("empty") }}
      </div>
      <q-item-label header>{{ $tr("title") }}</q-item-label>

      <autofill-card-person
        v-for="pastLogin in notRegisteredPeople"
        v-bind:key="'autofill-' + pastLogin.id"
        @click="selectPerson(pastLogin)"
        @mouseenter="showDeleteButton = pastLogin"
        @mouseleave="showDeleteButton = null"
        @deletePerson="deletePerson"
        :person="pastLogin"
        :showDeleteButton="showDeleteButton === pastLogin"
      />

      <template v-if="registeredPeople.length">
        <q-separator spaced />
        <q-item-label header>{{ $tr("registered") }}</q-item-label>

        <autofill-card-person
          v-for="pastLogin in registeredPeople"
          v-bind:key="'autofill-' + pastLogin.id"
          @mouseenter="showDeleteButton = pastLogin"
          @mouseleave="showDeleteButton = null"
          @deletePerson="deletePerson"
          :person="pastLogin"
          :showDeleteButton="showDeleteButton === pastLogin"
          :registered="true"
        />
      </template>
    </q-scroll-area>
    <q-inner-loading :showing="showLoading">
      <q-spinner color="primary" size="3em" />
    </q-inner-loading>
  </q-list>
</template>
<script>
import AutofillCardPerson from "./AutofillCardPerson";
import { EventBus } from "../../event-bus";

export default {
  data() {
    return {
      pastLogins: [],
      showLoading: true,
      translationPrefix: "tournament.autofill.",
      showDeleteButton: null
    };
  },

  components: {
    AutofillCardPerson
  },

  props: ["eventId"],

  methods: {
    selectPerson(person) {
      this.$emit("person-selected", person);
    },

    deletePerson() {
      let person = this.showDeleteButton;

      this.$confirm({
        confirm: this.$tr("general.confirmModal.remove", null, false),
        message: this.$tr("removeModal.title")
      }).onOk(() => {
        EventBus.$emit("fullLoader", true);

        this.$api({
          url: "deletedautofill",
          method: "post",
          alerts: false,
          data: {
            person: person.id
          }
        })
          .then(() => {
            // Remove removed person from cache
            this.pastLogins = this.pastLogins.filter(
              item => item.id !== person.id
            );
            this.$db("autofillDebaters", this.pastLogins, true);

            this.$flash(this.$tr("removeModal.success"), "success");
          })
          .catch(() => {
            this.$flash(this.$tr("removeModal.error"), "error");
          })
          .finally(() => {
            EventBus.$emit("fullLoader", false);
          });
      });
    }
  },

  computed: {
    // Peopla in autofill already registered for this tournament
    registeredPeople() {
      return this.pastLogins.filter(item => item.registered);
    },
    notRegisteredPeople() {
      return this.pastLogins.filter(item => !item.registered);
    }
  },

  created() {
    let cached = this.$db("autofillDebaters");

    if (cached) {
      this.showLoading = false;
      return (this.pastLogins = cached);
    }

    this.$api({
      url: "user/" + this.$auth.user().id + "/person/event/" + this.eventId,
      method: "get"
    })
      .then(d => {
        this.pastLogins = d.data;
        this.$db("autofillDebaters", this.pastLogins, true);
      })
      .finally(() => {
        this.showLoading = false;
      });
  }
};
</script>

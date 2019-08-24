<template>
  <q-page padding v-if="event">
    <h1 class="text-center text-h4">Registrace na turnaj {{ event.name }}</h1>
    <div class="text-center close-paragraphs q-p-1">
      <p>
        <template
          v-if="event.beginning.substr(0, 4) !== event.end.substr(0, 4)"
        >
          <!-- Year is different -->
          {{ event.beginning | moment("D. M. Y") }} - </template
        ><template
          v-else-if="event.beginning.substr(0, 7) !== event.end.substr(0, 7)"
        >
          <!-- Month is different -->
          {{ event.beginning | moment("D. M.") }} - </template
        ><template v-else-if="event.beginning !== event.end">
          <!-- Just day is different-->
          {{ event.beginning | moment("D.") }}-</template
        >{{ event.end | moment("D. M. Y") }},
        <!-- else - One day event -->
        {{ event.place }}
      </p>
      <p>
        {{ $tr("deadline") }}:
        {{ event.soft_deadline | moment("D. M. Y H:mm") }}
      </p>
      <p v-if="event.note">{{ $tr("note") }}: {{ event.note }}</p>
    </div>

    <pick-type
      v-if="!type"
      name="type"
      :values="[
        {
          label: 'Jednotlivec',
          icon: 'user',
          color: 'primary',
          value: 'single'
        },
        {
          label: 'Skupina',
          icon: 'users',
          color: 'blue-9',
          value: 'group'
        }
      ]"
      @selected="typePicked"
    />
    <pick-type
      v-else-if="role === null"
      name="role"
      :values="roles"
      @selected="typePicked"
      :hideFirst="type === 'single'"
    />
    <div class="row q-col-gutter-md reverse" v-else>
      <div class="col-12 col-sm-4 col-md-6">
        <debatersCard @debaterSelected="debaterSelected" />
      </div>
      <div class="col-12 col-sm-8 col-md-6">
        <!-- TODO - pokud je role 0 (tým), zobrazit zvláštní screenu -->
        <form-fields @submit="sendForm" :autofill="autofillData" />
      </div>
    </div>
  </q-page>
</template>

<script>
import debatersCard from "../components/Event/DebatersCard";
import formFields from "../components/Event/FormFields";
import pickType from "../components/Event/PickType";

export default {
  name: "Event",

  components: {
    debatersCard,
    formFields,
    pickType
  },

  data() {
    return {
      event: null,
      type: null, // single/group
      role: null,
      roles: {
        0: {
          value: 0,
          label: "Tým",
          icon: "users",
          color: "primary"
        }
      },
      autofillData: null,
      translationPrefix: "tournament."
    };
  },

  created() {
    this.getEventObject();

    // Load events from cache if available
    let cached = this.$db("rolesList");
    if (cached) return (this.roles = cached);

    this.$api({
      url: "role",
      method: "get"
    }).then(d => {
      let colors = {
        1: "blue-9",
        2: "indigo-6",
        3: "cyan-9",
        4: "green"
      };

      for (let role of d.data)
        this.roles[role.id] = {
          value: role.id,
          label: role.name,
          icon: role.icon,
          color: colors[role.id]
        };

      this.$db("rolesList", this.roles);
    });
  },

  methods: {
    // Get event data from local DB
    getEventObject() {
      let events = this.$db("eventsList");

      // DB might not be available yet - try later
      if (!events)
        return setTimeout(() => {
          this.getEventObject();
        }, 500);

      this.event = events[this.$route.params.id];
    },

    sendForm(data) {
      // TODO - pokud je posílaný autofillnutý, neodesílat znovu person
      this.$api({
        url: "person",
        sendToken: false,
        data: data,
        alerts: false
      }).then(data => {
        console.log(data);
        // TODO - odeslat Registration
      });
    },

    typePicked(key, value) {
      this[key] = value;
      this.autofillData = null;
    },

    debaterSelected(data) {
      this.autofillData = data;
    }
  }
};
</script>

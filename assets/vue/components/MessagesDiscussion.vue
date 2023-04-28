<template>
  <div class="row discussion">
    <div
      v-if="messages.length>0"
      class="col-12"
    >
      <div
        v-for="(message) in messages"
        :key="message.id"
        :class="'col-md-12'"
      >
        <span
          :class="(message.createdBy.roles.indexOf('ROLE_ADMIN') >= 0) ?'message mb-2 bg-success float-right' : ' message mb-2 bg-primary'"
        >
          <i class="lar la-user" />
          {{ message.createdBy.firstName }}
          <span
            class="date-message"
          >{{ message.created | moment("DD/MM/YYYY") }}</span>
          <!-- eslint-disable vue/no-v-html -->
          <p
            style="white-space: pre;"
            v-html="message.message"
          />
          <!--eslint-enable-->
        </span>
      </div>
    </div>
    <div
      v-else
      class="col-12"
    >
      {{ $t('Entities.Discussion.labels.noMessage') }}
    </div>
    <div class="col-12 mt-2">
      <form>
        <div class="form-row">
          <div class="col-12 mb-3">
            <textarea
              v-model="newMessage"
              class="form-control"
              :placeholder="$t('Entities.Discussion.labels.writeNewMessageHere')"
            />
          </div>
          <div class="col-12 text-center ">
            <button
              :disabled="newMessage.length === 0"
              type="button"
              class="btn btn-primary"
              @click="createMessage()"
            >
              {{ $t('Entities.Discussion.actions.post_message') }}
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  name: "Discussion",
  props: {
    messages: {
      type: Array,
      required: true
    },
    discussion: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      newMessage: "",
      roleUser: '["ROLE_USER"]'
    };
  },
  // computed: {
  //   messages() {
  //     return this.$store.getters["discussion/messages"];
  //   }
  // },
  mounted() {
    this.$store.dispatch("discussion/setMessages", this.messages);
  },
  methods: {
    async createMessage() {
      const result = await this.$store.dispatch("discussion/newMessage", {
        message: this.$data.newMessage,
        idDiscussion: this.discussion
      });
      if (result !== null) {
        this.$data.newMessage = "";
      }
    }
  }
};
</script>

<template>
  <div>
    <div v-if="isLoading">
      <div v-if="isLoading">
        <vue-loader />
      </div>
    </div>

    <div
      v-else-if="hasError"
      class="col"
    >
      <error-message :error="error" />
    </div>

    <div
      v-else-if="!discussion"
      class="text-center"
    >
      Non trouvé
    </div>

    <div
      v-else
      id="discussion"
    >
      <vue-title :title="$t('Entities.Discussion.labels.discussionAbris') + ' : ' + discussion.abris.name" />
      <h2>{{ $t('Entities.Discussion.labels.discussionAbris') + ' : ' + discussion.abris.name }}</h2>
      <section class="fields-container">
        <div class="field-value">
          {{ discussion.name }} ({{ discussion.created | moment("DD/MM/YYYY") }})
        </div>
        <div class="field-value">
          {{ discussion.description }}
        </div>
        <hr>
      </section>
      
      <MessagesDiscussion
        v-if="discussion"
        :messages="discussion.messages"
        :discussion="discussion.id"
      />
      <div id="bottom-buttons">
        <router-link          
          :to="{ name: 'detail-abris', params: { id: discussion.abris.id }}"   
          :title="$t('Generics.actions.backToAbris')"        
        >
          <i class="fas fa-angle-left la-2x" />
        </router-link>
      </div>        
    </div>
  </div>
</template>

<script>
import ErrorMessage from "../../components/ErrorMessage";
import MessagesDiscussion from "../../components/MessagesDiscussion";

export default {
  name: "Discussion",
  components: {
    ErrorMessage,    
    MessagesDiscussion
  },
  data: function() {
    return {
      formData: null
    };
  },
  computed: {
    isLoading() {
      return this.$store.getters["discussion/isLoading"];
    },
    hasError() {
      return this.$store.getters["discussion/hasError"];
    },
    error() {
      return this.$store.getters["discussion/error"];
    },
    discussion() {      
      return this.$store.getters["discussion/discussion"];
    },
  },
  created() {
    this.$store.dispatch("discussion/load", this.$route.params.id);
  }
};
</script>

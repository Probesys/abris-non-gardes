<template>
  <div>
    <div v-if="isLoading">
      <vue-loader />
    </div>
    <div
      v-else-if="hasError"
      class="row col"
    />      
    <div
      v-else-if="content"
      id="custom-page"
    >
      <h2>{{ content.name }}</h2>
      <!-- eslint-disable vue/no-v-html -->
      <div v-html="content.body" />
      <!--eslint-enable-->
    </div>
  </div>
</template>

<script>
export default {
  name: "Page",  
  computed: { 
    isLoading() {
      return this.$store.getters["page/isLoading"];
    },
    hasError() {
      return this.$store.getters["page/hasError"];
    },
    error() {
      return this.$store.getters["page/error"];
    },
    content() {
      return this.$store.getters["page/content"];
    }    
  },
  watch: {
    $route(to) {
      this.$store.dispatch("page/loadPage", to.params.id);      
    }
  },
  created() {
    this.$store.dispatch("page/loadPage", this.$route.params.id);
  }
};
</script>

<template>
  <div>
    <!-- <transition name="fade"> -->
    <div
        
      ref="searchPanel"
      class="search-panel-parent"
    >
      <button
        type="button"
        name="close-search-panel-btn"
        class="close-search-panel-btn"
      >
        <i class="las la-times" />
      </button>
      <div class="search-panel">
        <vue-title :title="'Rechercher un abris'" />

        <div class="d-flex search-panel-content mx-auto">
          <input
            v-model="keySearch"
            type="text"
            class="form-control search-panel-input flex-grow-1"
            placeholder="RECHERCHER UN ABRI"
            @keyup.13="searchAbris()"
          >          
          <button
            :disabled="isLoading"
            type="button"
            class="search-panel-btn"
            @click="searchAbris()"
          >
            <i class="las la-search" />
          </button>
        </div>
      </div>
    </div>
    <!-- </transition>     -->




    <div v-if="isLoading">
      <vue-loader />
    </div>

    <div
      v-else-if="hasError"
      class="row col"
    >
      <error-message :error="error" />
    </div>


    <!-- <div
      v-else-if="!hasAbris"
      class="row col"
    >
      Aucun abris
    </div> -->

    <div
      v-else-if="markers.length > 0"
    >
      <LeafLeft
        :markers="markers"
      />
    </div>
  </div>
</template>

<script>
//import Abris from "../../components/Abris/ItemListeAbris";
import ErrorMessage from "../../components/ErrorMessage";
import LeafLeft from "../../components/Abris/HomeLeafLeft";


export default {
  name: "ListeAbris",
  components: {
    ErrorMessage,
    LeafLeft
  },
  data() {
    return {
      keySearch: "",
      mapMarkers:[],
      showSearch: false,
    };
  },
  computed: {
    isLoading() { 

      return this.$store.getters["abris/isLoading"];
    },
    hasError() {
      return this.$store.getters["abris/hasError"];
    },
    error() {
      return this.$store.getters["abris/error"];
    },
    hasAbris() {
      return this.$store.getters["abris/hasAbris"];
    },
    markers() {
      return this.$store.getters["abris/markers"];
    },
  },
  created() {
    
    // if (this.$route.name == 'searchAbris'){
    //   console.log(this.$route.name);
    //   this.showSearch = true;
    // }
    //jQuery(".search-panel-parent").fadeIn( 300 );
    this.$store.dispatch("abris/search", this.keySearch);
  },
  
  methods: {
    async searchAbris() {
      this.$refs["searchPanel"].setAttribute("style",'display:none');
      //localStorage.searchKey = this.$data.keySearch;
      const result =  await this.$store.dispatch("abris/search", this.$data.keySearch);
      this.keySearch = "";
      if (result !== null) {        
        if (result.length == 0){
          this.$toasted.show("Aucun abri trouvé", {
            theme: "bubble",
            position: "top-right",
            duration: 2000
          });
          this.searchAbris();
        }
      }

    }
  }
};
</script>
<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity .5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
  opacity: 0;
}

</style>
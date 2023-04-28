import Vue from "vue";
import Vuex from "vuex";
import SecurityModule from "./security";
import AbrisModule from "./listeAbris";
import DetailAbrisModule from "./detailAbris";
import Dysfonctionnement from "./dysfonctionnement";
import Discussion from "./discussion";
import Page from "./page";

Vue.use(Vuex);

export default new Vuex.Store({
  modules: {
    security: SecurityModule,
    abris: AbrisModule,
    detailAbris: DetailAbrisModule,
    dysfonctionnement: Dysfonctionnement,
    discussion:Discussion,
    page:Page
  }
});

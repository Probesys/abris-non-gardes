import AbrisAPI from "../api/abris";

const FETCHING_DYSFUNCTION_ABRIS = "FETCHING_DYSFUNCTION_ABRIS",
  FETCHING_DYSFUNCTION_ABRIS_SUCCESS = "FETCHING_DYSFUNCTION_ABRIS_SUCCESS",
  FETCHING_DYSFUNCTION_ABRIS_ERROR = "FETCHING_DYSFUNCTION_ABRIS_ERROR",
  SET_ACTIVE_TAB = "SET_ACTIVE_TAB"

export default {
  namespaced: true,
  state: {
    isLoading: false,
    error: null,
    dysfunction: null,
  },
  getters: {
    isLoading(state) {
      return state.isLoading;
    },
    hasError(state) {
      return state.error !== null;
    },
    error(state) { 
      return state.error;
    },
    detail(state) {
      return state.dysfunction;
    },  
    photos(state) {
      var photos = []; 

      state.dysfunction.photos.forEach(element => {
        photos.push('/files/' + state.dysfunction.abris.id + '/' + element.fileName);
      });

      return photos;
    },
  },
  mutations: {
    [FETCHING_DYSFUNCTION_ABRIS](state) {
      state.isLoading = true;
      state.error = null;
      state.dysfunction = null;
    },
    [FETCHING_DYSFUNCTION_ABRIS_SUCCESS](state, dysfunction) {
      state.isLoading = false;
      state.error = null;
      state.dysfunction = dysfunction
    },
    [FETCHING_DYSFUNCTION_ABRIS_ERROR](state, error) {
      state.isLoading = false;
      state.error = error;
      state.detail = [];
    },
    [SET_ACTIVE_TAB](state, tab) {
      state.activeTab = tab;
    }

  },
  actions: { 
    async load({ commit }, id) {
      commit(FETCHING_DYSFUNCTION_ABRIS);
      try {
        let response = await AbrisAPI.dysfonctionnement(id);
        commit(FETCHING_DYSFUNCTION_ABRIS_SUCCESS, response.data[0]);                
        return response.data[0];
      } catch (error) {
        console.error("1321313113");
        // commit(FETCHING_DYSFUNCTION_ABRIS_ERROR, error);
        return null;
      }
    },
  }
};

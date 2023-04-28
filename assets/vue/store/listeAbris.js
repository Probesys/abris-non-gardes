import AbrisAPI from "../api/abris";

const
  FETCHING_ABRIS = "FETCHING_ABRIS",
  FETCHING_ABRIS_SUCCESS = "FETCHING_ABRIS_SUCCESS",
  FETCHING_ABRIS_ERROR = "FETCHING_ABRIS_ERROR";

export default {
  namespaced: true,
  state: {
    isLoading: false,
    error: null,
    searchResult: []
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
    hasAbris(state) {
      return state.searchResult.length > 0;
    },
    searchResult(state) {
      return state.searchResult;
    },

    markers(state) {
      var markers = [];
      state.searchResult.forEach(element => {
        markers.push({
          'coords': element.coordinate,
          'altitude': element.altitude,
          'city': element.city.name,
          'name': element.name,
          'type': element.type.name,
          'photos': element.photos,
          'abrisId': element.id,
          'description': element.description
        });
      });
      return markers;
    },
  },
  mutations: {
    [FETCHING_ABRIS](state) {
      state.isLoading = true;
      state.error = null;
      state.searchResult = [];
    },
    [FETCHING_ABRIS_SUCCESS](state, searchResult) {
      state.isLoading = false;
      state.error = null;
      state.searchResult = searchResult;
    },
    [FETCHING_ABRIS_ERROR](state, error) {
      state.isLoading = false;
      state.error = error;
      state.abris = [];
    }
  },
  actions: {
    async search( { commit }, keySearch) {
      commit(FETCHING_ABRIS);
      try {
        let response = await AbrisAPI.search(keySearch);
        commit(FETCHING_ABRIS_SUCCESS, response.data);
        return response.data;
      } catch (error) {
        commit(FETCHING_ABRIS_ERROR, error);
        return null;
      }
    },
  }
};

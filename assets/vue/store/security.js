import UserAPI from "../api/user";

const
  PROVIDING_DATA_ON_REFRESH_SUCCESS = "PROVIDING_DATA_ON_REFRESH_SUCCESS",
  UPDATING_PROFILE = "UPDATING_PROFILE",
  UPDATING_PROFILE_SUCCESS = "UPDATING_PROFILE_SUCCESS";

export default {
  namespaced: true,
  state: {
    isLoading: false,
    error: null,
    isAuthenticated: false,
    user: null
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
    isAuthenticated(state) {
      return state.isAuthenticated;
    },
    userName(state) {
      return state.user.username;
    },
    user(state) {
      return state.user;
    },
    bookmarks(state) {
      return state.user.abrisFavoris;
    },
    dysfonctionnements(state) {
      return state.user.dysfonctionnements;
    },
    hasRole(state) {
      return role => {
        return state.user.roles.indexOf(role) !== -1;
      }
    },
    getRole(state) {
      return state.user.roles
    }
  },
  mutations: {
    [PROVIDING_DATA_ON_REFRESH_SUCCESS](state, payload) {
      state.isLoading = false;
      state.error = null;
      state.isAuthenticated = payload.isAuthenticated;
      state.user = payload.user;
    },
    [UPDATING_PROFILE](state) {
      state.isLoading = true;
      state.error = null;
    },
    [UPDATING_PROFILE_SUCCESS](state, user) {
      state.isLoading = false;
      state.error = null;
      state.user = user
    }
  },
  actions: {
    onRefresh({ commit }, payload) {
      commit(PROVIDING_DATA_ON_REFRESH_SUCCESS, payload);
    },
    async updateProfile({ commit }, data) {
      commit(UPDATING_PROFILE);
      try {
        let response = await UserAPI.updateProfile(data.user.id, data.formData);
        commit(UPDATING_PROFILE_SUCCESS, response.data);
        return response.data.id
      } catch (error) {
        console.log(error);
        return false
      }
    },
  }
}
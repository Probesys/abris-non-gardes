import AbrisAPI from "../api/abris";
import UserAPI from "../api/user";

const FETCHING_DETAIL_ABRIS = "FETCHING_DETAIL_ABRIS",
  FETCHING_DETAIL_ABRIS_SUCCESS = "FETCHING_DETAIL_ABRIS_SUCCESS",
  FETCHING_DETAIL_ABRIS_ERROR = "FETCHING_DETAIL_ABRIS_ERROR",
  SET_ACTIVE_TAB = "SET_ACTIVE_TAB",
  IS_BOOKMARKED = "IS_BOOKMARKED",
  CREATE_DYSFUNCTION = "CREATE_DYSFUNCTION",
  CREATE_DYSFUNCTION_SUCCESS = "CREATE_DYSFUNCTION_SUCCESS"

export default {
  namespaced: true,
  state: {
    isLoading: false,
    isBookmarked: false,
    error: null,
    detail: null,
    activeTab: 1,
  },
  getters: {
    activeTab(state) {
      return state.activeTab;
    },
    isLoading(state) {
      return state.isLoading;
    },
    hasError(state) {
      return state.error !== null;
    },
    error(state) {
      return state.error;
    },
    isLoaded(state) {
      return state.detail !== null;
    },
    isBookmarked(state) {
      return state.isBookmarked;
    },
    detail(state) {
      return state.detail;
    },
    photos(state) {
      var photos = [];
      if (state.detail.photos) {
        state.detail.photos.forEach(element => {
          photos.push('/files/' + state.detail.id + '/' + element.fileName);
        });
      }


      return photos;
    },
  },
  mutations: {
    [FETCHING_DETAIL_ABRIS](state) {
      state.isLoading = true;
      state.isBookmarked = false;
      state.error = null;
      state.detail = [];
    },
    [CREATE_DYSFUNCTION](state) {
      state.isLoading = true;
    },    
    [CREATE_DYSFUNCTION_SUCCESS](state) {
      state.isLoading = false;
    },       
    [FETCHING_DETAIL_ABRIS_SUCCESS](state, detail) {
      state.isLoading = false;
      state.error = null;
      state.detail = detail;
      state.dysfunctions = detail.dysfonctionnements
    },
    [FETCHING_DETAIL_ABRIS_ERROR](state, error) {
      state.isLoading = false;
      state.error = error;
      state.detail = [];
    },
    [SET_ACTIVE_TAB](state, tab) {
      state.activeTab = tab;
    },
    [IS_BOOKMARKED](state, isBookmarked) {

      state.isBookmarked = isBookmarked;
    }
  },
  actions: {
    async load({ commit }, id) {
      commit(FETCHING_DETAIL_ABRIS);
      // let returnData = null;
      try {
        let response = await AbrisAPI.detail(id);


        let responseDiscussions = await AbrisAPI.listDiscussions(id);
        response.data[0].discussions = responseDiscussions.data;
        commit(FETCHING_DETAIL_ABRIS_SUCCESS, response.data[0]);
        let userBookmarks = this.state.security.user.abrisFavoris

        userBookmarks.forEach(function (item) {
          if (item.id === response.data[0].id) {
            commit(IS_BOOKMARKED, true);
          }
        }); 

        return response.data;
      } catch (error) {
        commit(FETCHING_DETAIL_ABRIS_ERROR, error);
        return null;
      }
    },
    async addToBookMark({ commit }, abris) {
      try {

        await UserAPI.addAbrisToBookMark(this.state.security.user.id, abris.id);
        this.state.security.user.abrisFavoris.push(abris) 
        commit(IS_BOOKMARKED, true);
        return true
      } catch (error) {
        commit(IS_BOOKMARKED, false);
        return false
      }
    },
    async removeFromBookMark({ commit }, abris) {
      try {
        await UserAPI.removeAbrisFromBookMark(this.state.security.user.id, abris.id);
        commit(IS_BOOKMARKED, false);
        let userBookmarks = this.state.security.user.abrisFavoris

        let cpt = 0;
        userBookmarks.forEach(function (item) {
          if (item.id === abris.id) {
            userBookmarks.splice(cpt,1);
          }
          cpt++;
        });        
        return true
      } catch (error) {
        console.log(error);
        commit(IS_BOOKMARKED, true);
        return false
      }
    },
    setActiveTab({ commit }, tabNum) {
      commit(SET_ACTIVE_TAB, tabNum)
    },
    async createDysfuction({ commit }, data) {
      commit(CREATE_DYSFUNCTION);
      try {
        let response = await AbrisAPI.createDysfuction(data);     
        commit(CREATE_DYSFUNCTION_SUCCESS, data);
        return response.data.id
      } catch (error) {
        commit(IS_BOOKMARKED, false);
        return false
      }
    },    
  }
};

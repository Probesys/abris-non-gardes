import PageAPI from "../api/page";

const LOAD_PAGE = "LOAD_PAGE",
  LOAD_PAGE_SUCCESS = "LOAD_PAGE_SUCCESS",
  LOAD_PAGE_ERROR = "LOAD_PAGE_ERROR"


export default {
  namespaced: true,
  state: {
    error: null,
    isLoading: false,    
    content : null,
  },
  getters: {
    isLoading(state) {
      return state.isLoading;
    },
    content(state) {     
      return state.content;
    },    
    hasError(state) {
      return state.error !== null;
    },    
  },
  mutations: {  
    [LOAD_PAGE](state) {
      state.isLoading = true;
      state.error = null;
      state.page = null;
    },   
    [LOAD_PAGE_SUCCESS](state,content) {
      state.isLoading = false;
      state.error = null;
      
      state.content = content;
    },     
    [LOAD_PAGE_ERROR](state,error) {
      state.isLoading = false;
      state.error = error;
      state.content = null;
    },            
  },
  actions: { 
    async loadPage({ commit }, id) {
      commit(LOAD_PAGE);
      
      try {
        let response = await PageAPI.loadPages(id);
        commit(LOAD_PAGE_SUCCESS, response.data);   
        return response.data;
      } catch (error) {
        
        commit(LOAD_PAGE_ERROR, error);
        return null;
      }
    },     
  }
};

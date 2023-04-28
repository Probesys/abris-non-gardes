import DiscussionAPI from "../api/discussion";

const CREATING_MESSAGE_SUCCESS = "CREATING_MESSAGE_SUCCESS",
  SET_MESSAGES = "SET_MESSAGES",
  LOAD_DISCUSSION = "LOAD_DISCUSSION",
  LOAD_DISCUSSION_SUCCESS = "LOAD_DISCUSSION_SUCCESS",
  LOAD_DISCUSSION_ERROR = "LOAD_DISCUSSION_ERROR"

export default {
  namespaced: true,
  state: {
    error: null,
    isLoading: false,
    messages: [],    
    discussion : null,
  },
  getters: {
    isLoading(state) {
      return state.isLoading;
    },
    messages(state) {     
      return state.messages;
    },
    discussion(state) {     
      return state.discussion;
    },    
    hasError(state) {
      return state.error !== null;
    },    
  },
  mutations: {  
    [LOAD_DISCUSSION](state) {
      state.isLoading = true;
      state.error = null;
      state.discussion = null;
    },   
    [LOAD_DISCUSSION_SUCCESS](state,discussion) {
      state.isLoading = false;
      state.error = null;
      
      state.discussion = discussion;
    },     
    [LOAD_DISCUSSION_ERROR](state,error) {
      state.isLoading = false;
      state.error = error;
      state.discussion = null;
    },        
    [CREATING_MESSAGE_SUCCESS](state, message) {
      state.isLoading = false;
      state.error = null;
      state.messages.push(message);
    },   
    [SET_MESSAGES](state,messages) {
      
      state.isLoading = false;
      state.error = null;
      state.messages  = messages;
      
    },    
  },
  actions: { 
    async load({ commit }, id) {
      commit(LOAD_DISCUSSION);
      
      try {
        let response = await DiscussionAPI.load(id);
        commit(LOAD_DISCUSSION_SUCCESS, response.data);                
        return response.data;
      } catch (error) {
        
        commit(LOAD_DISCUSSION_ERROR, error);
        return null;
      }
    },     
    setMessages({ commit }, messages) {
      commit(SET_MESSAGES, messages);          
    },
    async newMessage({ commit }, params) {
      try {
        let response = await DiscussionAPI.postNewMessage(params.message, params.idDiscussion);
        commit(CREATING_MESSAGE_SUCCESS, response.data);
        return response.data;
      } catch (error) {
        return null;
      }
    },
  }
};

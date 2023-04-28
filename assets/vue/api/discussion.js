import axios from "axios";

export default {
  load($id) {
    return axios.get("/api/discussion/detail/" + $id);
  }  ,  
  postNewMessage(message, idDiscussion) {
    return axios.post("/api/discussion/" + idDiscussion + "/new-message/", {
      message: message
    });
  },
};
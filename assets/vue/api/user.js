import axios from "axios";

export default {
  addAbrisToBookMark(user, abris) {
   
    return axios.post("/api/user/" + user + "/addAbris" , {
      id_abris: abris,
    });
  },
  removeAbrisFromBookMark(user, abris) {
   
    return axios.post("/api/user/" + user + "/removeAbris" , {
      id_abris: abris,
    });
  },  
  listeTypeUser() {
    return axios.get("/api/user/userTypes");
  },  
  updateProfile(userId,formData) {
    return axios.post("/api/user/" + userId + "/updateProfile", formData);
  },       
}
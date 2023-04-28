import axios from "axios";

export default {
  listePages() {   
    return axios.get("/api/pages/");
  },  
  loadPages(id) {   
    return axios.get("/api/pages/" + id);
  },   
}
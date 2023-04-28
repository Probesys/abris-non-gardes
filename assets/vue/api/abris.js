import axios from "axios";

export default {
  findAll() {
    return axios.get("/api/abris/liste");
  },
  search($keySearch) {
    
    return axios.get("/api/abris/search/" , { params: { keySearch: $keySearch } });
  },   
  detail($id) {
    return axios.get("/api/abris/detail/" + $id);
  }  ,
  dysfonctionnement($id) {
    return axios.get("/api/dysfonctionnement/detail/" + $id);
  }  ,  
  findAllNatureDys() {
    return axios.get("/api/dysfonctionnement/natureDys/");
  },
  findElementsDys(idNatureDys) {
    return axios.get("/api/dysfonctionnement/elementsDys/" + idNatureDys);
  }  ,
  findDetailsDys(idElementDys) {
    return axios.get("/api/dysfonctionnement/detailsDys/" + idElementDys);
  },    
  createDysfuction(formData) {
    return axios.post("/api/dysfonctionnement/create/", formData);
  },  
  createDiscussion(formData) {
    return axios.post("/api/discussion/create/", formData);
  }, 
  listDiscussions($id) {
    return axios.get("/api/abris/" + $id + "/discussions/");
  },      
};
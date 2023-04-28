import Vue from "vue";
import VueRouter from "vue-router";
import store from "../store";
import ListeAbris from "../views/Abris/ListeAbris";
import DetailAbris from "../views/Abris/DetailAbris";
import Dysfonctionnement from "../views/Abris/Dysfonctionnement";
import Discussion from "../views/Abris/DiscussionAbris";
import Page from "../views/Page";
import Compte from "../views/Compte";


Vue.use(VueRouter);

let router = new VueRouter({
  mode: "history",
  routes: [
    {path: "/abris", component: ListeAbris},
    {path: "/abris/search", component: ListeAbris, name: "searchAbris"},
    {path: "/abris/details/:id", name:"detail-abris", component: DetailAbris}, 
    {path: "/abris/details/dysfonctionnement/:id", name:"dysfunction-abris", component: Dysfonctionnement},
    {path: "/abris/details/discussion/:id", name:"discussion-abris", component: Discussion},
    {path: "/page/:id", name:"page", component: Page},
    {path: "/mon-compte/", name:"mon-compte", component: Compte},
    {path: "*", redirect: "/abris"}
  ] 
});



router.beforeEach((to, from, next) => {
  if (to.matched.some(record => record.meta.requiresAuth)) {
    // this route requires auth, check if logged in
    // if not, redirect to login page.
    if (store.getters["security/isAuthenticated"]) {
      next();
    } else { 
      next({
        path: "/security/login",
        query: {redirect: to.fullPath}
      });
    }
  } else {
    next(); // make sure to always call next()!
  }
});

export default router;
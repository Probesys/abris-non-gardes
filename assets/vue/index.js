import Vue from "vue";
import App from "./App";
import router from "./router";
import store from "./store";
import VueTitle from "./components/app.title.vue";
import VueLoader from "./components/Loader";
import { languages } from './lang.js'

const messages = Object.assign(languages)

import VueI18n from 'vue-i18n'
import VueSpinners from 'vue-spinners'
import vSelect from 'vue-select'
import moment from 'vue-moment'
import Toasted from 'vue-toasted';

Vue.component('vue-title', VueTitle);
Vue.component('vue-loader', VueLoader);
Vue.use(VueI18n)
Vue.use(Toasted)
Vue.use(moment)
Vue.use(VueSpinners)
Vue.component('v-select', vSelect)

var i18n = new VueI18n({
  locale: 'fr',
  fallbackLocale: 'fr',
  messages
})


Vue.filter('ucfirst', function (value) {
  if (!value) return ''
  value = value.toString()
  return value.charAt(0).toLowerCase() + value.slice(1)
})

new Vue({
  //delimiters: ['${', '}'],
  components: {App},
  template: "<App/>",
  router,
  store,
  i18n,
}).$mount("#app");

<template>
  <div class="container-fluid p-0">
    <button
      class="menu-btn mobile border-0"
      type="button"
    >
      <div class="open-panel burger">
        <span class="first-bar" />
        <span class="second-bar" />
        <span class="third-bar" />
      </div>
    </button>

    <nav class="main-nav">
      <ul class="main-menu">
        <li
          v-if="isAuthenticated"
          class="main-nav-item"
        >
          <a
            class="main-nav-link"
            href="/api/security/logout"
            title="Se déconnecter"
          >
            Se déconnecter
            <i class="las la-power-off" />
          </a>
        </li>

        <router-link
          class="main-nav-item"
          tag="li"
          to="/mon-compte"
          active-class="active"
        >
          <a
            class="main-nav-link"
            title="Mon compte"
          >
            Mon compte
            <i class="lar la-user" />
          </a>
        </router-link>

        <router-link
          class="main-nav-item"
          tag="li"
          to="/abris"
          active-class="active"
        >
          <a
            class="main-nav-link"
            title="Rechercher un abri"
          >
            Rechercher un abri
            <i class="las la-search" />
          </a>
        </router-link>

        <router-link
          v-for="(page) in listePages"
          :key="page.id"
          class="main-nav-item"
          tag="li"
          :to="{ name: 'page', params: { id: page.id }}"
          active-class="active"
        >
          <a class="main-nav-link">
            {{ page.linkText }}
          </a>
        </router-link>
      </ul>
    </nav>

    <router-view />
  </div>
</template>

<script>
import axios from "axios";

import commonApi from "./api/page";

export default {
  name: "App",

  data: function() {
    return {
      listePages: []
    };
  },

  computed: {
    isAuthenticated() {
      return this.$store.getters["security/isAuthenticated"];
    },

    userName() {
      return this.$store.getters["security/userName"];
    }
  },

  created() {
    this.getAllPages();

    let isAuthenticated = JSON.parse(
        this.$parent.$el.attributes["data-is-authenticated"].value
      ),
      user = JSON.parse(this.$parent.$el.attributes["data-user"].value);

    let payload = { isAuthenticated: isAuthenticated, user: user };

    this.$store.dispatch("security/onRefresh", payload);

    axios.interceptors.response.use(undefined, err => {
      return new Promise(() => {
        if (err.response.status === 401) {
          document.location.href = "/security/login/";

          // this.$router.push({ path: "/security/login/" });
        } else if (err.response.status === 500) {
          document.open();

          document.write(err.response.data);

          document.close();
        }

        throw err;
      });
    });
  },

  methods: {
    async getAllPages() {
      try {
        let response = await commonApi.listePages();

        this.listePages = response.data;
      } catch (error) {
        return null;
      }
    }
  }
};
</script>

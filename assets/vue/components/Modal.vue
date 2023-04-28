<template>
  <div class="modal-backdrop">
    <div class="modal">
      <div v-if="photoPath">
        <header class="modal-header">
          <img :src="photoPath">
        </header>
      </div>

      <section class="modal-body">
        <h2>{{ titre }} - {{ city }} - {{ altitude }} m</h2>
        <p v-if="description.length < 100 ">
          {{ description }}
        </p>
        <p v-else>
          {{ description.substring(0,100) + "..." }}
        </p>
        <router-link
          class="btn btn-primary"
          :to="{ name: 'detail-abris', params: { id: id }}"
        >
          <a class="nav-link">Voir la fiche abri</a>
        </router-link>
      </section>
      <footer class="modal-footer">
        <button
          type="button"
          class="btn btn-primary"
          @click="close"
        >
          Fermer
        </button>
      </footer>
    </div>
  </div>
</template>

<script>
export default {
  name: "Modal",
  props: {
    id: {
      type: String,
      required: false,
      default: ""
    },
    titre: {
      type: String,
      required: false,
      default: ""
    },
    description: {
      type: String,
      required: false,
      default: ""
    },
    city: {
      type: String,
      required: false,
      default: ""
    },
    altitude: {
      type: String,
      required: false,
      default: ""
    },
    photoPath: {
      type: String,
      required: false,
      default: ""
    }
  },
  methods: {
    close() {
      this.$emit("close");
    }
  }
};
</script>
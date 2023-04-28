<template>
  <div>
    <div v-if="isLoading">
      <vue-loader />
    </div>

    <div
      v-else-if="hasError"
      class="col"
    >
      <error-message :error="error" />
    </div>

    <div
      v-else-if="!detail"
      class="text-center"
    >
      Non trouvé
    </div>

    <div
      v-else
      id="dysfonctionnement"
    >
      <h2>{{ $t('Entities.Abris.tabs.dysfonctionnements') }}</h2>
      <section class="fields-container">
        <div class="field-label">
          {{ $t('Entities.Dysfonctionnement.fields.abris') }}
        </div>
        <div class="field-value">
          {{ detail.abris.name }}
        </div>
        <hr>

        <div class="field-label">
          {{ $t('Generics.fields.date') }}
        </div>
        <div class="field-value">
          {{ detail.created | moment("DD/MM/YYYY") }}
        </div>
        <hr>

        <div class="field-label">
          {{ $t('Entities.Dysfonctionnement.fields.natureDys') }}
        </div>
        <div class="field-value">
          {{ detail.natureDys.name }}
        </div>
        <hr>

        <div class="field-label">
          {{ $t('Entities.Dysfonctionnement.fields.elementDys') }}
        </div>
        <div class="field-value">
          {{ detail.elementDys.name }}
        </div>
        <hr>

        <div class="field-label">
          {{ $t('Entities.Dysfonctionnement.fields.description') }}
        </div>
        <!-- eslint-disable vue/no-v-html -->
        <div
          class="field-value"
          v-html="detail.description"
        />
        <!--eslint-enable-->
        <div v-if="photos.length > 0">
          <ImageSlider :images="photos" />
        </div>
      </section>

      <u>{{ $t('Entities.Dysfonctionnement.fields.discussion') }}</u>
      <MessagesDiscussion
        v-if="detail.discussion"
        :messages="detail.discussion.messages"
        :discussion="detail.discussion.id"
      />
      <div id="bottom-buttons">
        <router-link
          :to="{ name: 'detail-abris', params: { id: detail.abris.id }}"
          :title="$t('Generics.actions.backToAbris')"
        >
          <i class="fas fa-angle-left la-2x" />
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import ErrorMessage from "../../components/ErrorMessage";
import ImageSlider from "../../components/Abris/ImageSlider";
import MessagesDiscussion from "../../components/MessagesDiscussion";

export default {
  name: "Dysfonctionnement",
  components: {
    ErrorMessage,
    ImageSlider,
    MessagesDiscussion
  },
  data: function() {
    return {
      formData: null
    };
  },
  computed: {
    isLoading() {
      return this.$store.getters["dysfonctionnement/isLoading"];
    },
    activeTab() {
      return this.$store.getters["dysfonctionnement/activeTab"];
    },
    hasError() {
      return this.$store.getters["dysfonctionnement/hasError"];
    },
    error() {
      return this.$store.getters["dysfonctionnement/error"];
    },
    detail() {
      return this.$store.getters["dysfonctionnement/detail"];
    },
    photos() {
      return this.$store.getters["dysfonctionnement/photos"];
    },
    dysfunctions() {
      return this.$store.getters["dysfonctionnement/dysfunctions"];
    }
  },
  created() {
    this.$store.dispatch("dysfonctionnement/load", this.$route.params.id);
  }
};
</script>
<style >
section.fields-container{
  margin-bottom: 30px;
}
</style>
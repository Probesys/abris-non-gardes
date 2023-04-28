<template>
  <div>
    <div v-if="isLoading">
      <vue-title :title="$t('Generics.messages.loading')" />
      <div
        v-if="isLoading"
        class="loader-container"
      >
        <vue-loader />
      </div>
    </div>

    <div
      v-else-if="hasError"
      class="row col"
    >
      <error-message :error="error" />
    </div>

    <div
      v-else-if="!isLoaded"
      class="row col"
    >
      Non trouvé
    </div>

    <div v-else-if="detailAbris">
      <vue-title :title="detailAbris.name" />
      <div class="cd-tabs cd-tabs--vertical max-width-md margin-top-xl margin-bottom-lg js-cd-tabs">
        <nav class="cd-tabs__navigation local-nav">
          <ul class="cd-tabs__list">
            <li class="local-nav-item">
              <a
                href=""
                :class="'cd-tabs__item cd-tabs__item--selected local-nav-link ' + (activeTab == 1 ? 'active' : '')"
                @click.prevent="setActiveTab(1)"
              >
                <span>{{ $t('Entities.Abris.tabs.infosGenerales') }}</span>
              </a>
            </li>

            <li class="local-nav-item">
              <a
                href=""
                :class="'cd-tabs__item local-nav-link ' + (activeTab == 2 ? 'active' : '')"
                @click.prevent="setActiveTab(2)"
              >
                <span>{{ $t('Entities.Abris.tabs.structureBatiment') }}</span>
              </a>
            </li>

            <li class="local-nav-item">
              <a
                href=""
                :class="'cd-tabs__item local-nav-link ' + (activeTab == 3 ? 'active' : '')"
                @click.prevent="setActiveTab(3)"
              >
                <span>{{ $t('Entities.Abris.tabs.equipementServices') }}</span>
              </a>
            </li>

            <li class="local-nav-item">
              <a
                href=""
                :class="'cd-tabs__item local-nav-link ' + (activeTab == 4 ? 'active' : '')"
                @click.prevent="setActiveTab(4)"
              >
                <span>{{ $t('Entities.Abris.tabs.otherInfos') }}</span>
              </a>
            </li>

            <li class="local-nav-item">
              <a
                href=""
                :class="'cd-tabs__item local-nav-link ' + (activeTab == 5 ? 'active' : '')"
                @click.prevent="setActiveTab(5)"
              >
                <span>{{ $t('Entities.Abris.tabs.dysfonctionnements') }}</span>
              </a>
            </li>

            <li class="local-nav-item">
              <a
                href=""
                :class="'cd-tabs__item local-nav-link ' + (activeTab == 6 ? 'active' : '')"
                @click.prevent="setActiveTab(6)"
              >
                <span>{{ $t('Entities.Abris.tabs.discussions') }}</span>
              </a>
            </li>
          </ul>
          <div class="scroll-info">
            <img
              src="/img/scroll.png"
              height="30px"
            >
          </div>
          <!-- cd-tabs__list -->
        </nav>

        <ul class="cd-tabs__panels">
          <li
            v-if="activeTab === 1"
            id="tab-inbox"
            class="cd-tabs__panel cd-tabs__panel--selected text-component"
          >
            <vue-title :title="detailAbris.name" />
            <div class="row">
              <div class="col-md-4">
                <div class="float-right">
                  <a
                    :class="(isBookmarked) ? 'float-right text-warning' : 'float-right text-primary'"
                    title="Ajouter aux favoris"
                    href="#"
                    @click="(isBookmarked) ? removeBookMark(detailAbris) : addToBookMark(detailAbris)"
                  >
                    <i class="las la-star la-2x" />
                  </a>
                </div>
                <h1>{{ detailAbris.name }}</h1>

                <div v-if="photos.length > 0">
                  <ImageSlider :images="photos" />
                </div>
                <div v-else>
                  <LeafLeft
                    :name="detailAbris.name"
                    :coordinate="detailAbris.coordinate"
                    :type="detailAbris.type.name"
                  />
                </div>
              </div>
              <div class="col-md-8">
                <div class="field-value">
                  {{ detailAbris.description }}
                </div>

                <section class="fields-container">
                  <div class="field-label">
                    {{ $t('Entities.Abris.fields.type') }}
                  </div>
                  <div class="field-value">
                    {{ detailAbris.type.name }}
                  </div>
                  <hr>


                  <hr>
                  <div class="field-label">
                    {{ $t('Entities.Abris.fields.coordinate') }}
                  </div>
                  <div class="field-value">
                    {{ detailAbris.coordinate }}
                  </div>
                  <hr>
                  <div class="field-label">
                    {{ $t('Entities.Abris.fields.altitude') }}
                  </div>
                  <div class="field-value">
                    {{ detailAbris.altitude }} m
                  </div>
                  <hr>
                  <div class="field-label">
                    {{ $t('Entities.Abris.fields.capaciteAccueil') }}
                  </div>
                  <div class="field-value">
                    {{ detailAbris.capaciteAccueil }}
                  </div>
                  <hr>

                  <div class="field-label">
                    {{ $t('Entities.Abris.fields.capaciteCouchage') }}
                  </div>
                  <div class="field-value">
                    {{ detailAbris.capaciteCouchage }}
                  </div>
                  <hr>

                  <div class="field-label">
                    {{ $t('Entities.Abris.fields.city') }}
                  </div>
                  <div class="field-value">
                    {{ detailAbris.city.name }}
                  </div>
                  <hr>

                  <div class="field-label">
                    {{ $t('Entities.Abris.fields.proprietaires') }}
                  </div>
                  <div class="field-value">
                    <span
                      v-for="(proprio,index) in detailAbris.proprietaires"
                      :key="proprio.id"
                    >
                      {{ proprio.structureName ? proprio.structureName : proprio.firstName + " " + proprio.lastName }}
                      <span
                        v-if="index != Object.keys(detailAbris.proprietaires).length - 1"
                      >,</span>
                    </span>
                  </div>
                </section>
              </div>
            </div>
          </li>

          <li
            v-if="activeTab === 2"
            id="tab-new"
            class="cd-tabs__panel text-component"
          >
            <vue-title
              :title="$t('Entities.Abris.tabs.structureBatiment') + ' - ' + detailAbris.name"
            />
            <h1>{{ detailAbris.name }}</h1>
            <h3>{{ $t('Entities.Abris.tabs.structureBatiment') }}</h3>
            <section class="fields-container">
              <div class="field-label">
                {{ $t('Entities.Abris.fields.toit') }}
              </div>
              <div class="field-value">
                <span
                  v-for="(typeToit,index) in detailAbris.toit"
                  :key="typeToit.id"
                >
                  {{ typeToit.name }}
                  <span
                    v-if="index != Object.keys(detailAbris.toit).length - 1"
                  >,</span>
                </span>
              </div>
              <hr>

              <div v-if="detailAbris.sortieFumees && detailAbris.sortieFumees.id !== 5">
                <div class="field-label">
                  {{ $t('Entities.Abris.fields.sortieFumees') }}
                </div>
                <div class="field-value">
                  {{ detailAbris.sortieFumees.name }}
                </div>
                <hr>

                <div class="field-label">
                  {{ $t('Entities.Abris.fields.materiauSortieFumees') }}
                </div>
                <div class="field-value">
                  {{ detailAbris.materiauSortieFumees.name }}
                </div>
                <hr>
              </div>

              <div class="field-label">
                {{ $t('Entities.Abris.fields.nbPortes') }}
              </div>
              <div class="field-value">
                {{ detailAbris.nbPortes }}
              </div>
              <hr>
              <div class="field-label">
                {{ $t('Entities.Abris.fields.nbFenetres') }}
              </div>
              <div class="field-value">
                {{ detailAbris.nbFenetres }}
              </div>
              <hr>

              <div class="field-label">
                {{ $t('Entities.Abris.fields.typeMur') }}
              </div>
              <div class="field-value">
                {{ detailAbris.typeMur.name }}
              </div>
              <hr>

              <div v-if="detailAbris.etage == 1">
                <div class="field-label">
                  {{ $t('Entities.Abris.fields.etage') }}
                </div>
                <div class="field-value">
                  {{ $t('Entities.Abris.fields.accesEtage') }} :
                  <span
                    v-for="(accesEtage,index) in detailAbris.accesEtage"
                    :key="accesEtage.id"
                  >
                    {{ accesEtage.name | ucfirst }}
                    <span
                      v-if="index != Object.keys(detailAbris.accesEtage).length - 1"
                    >,</span>
                  </span>
                  .
                  {{ $t('Entities.Abris.fields.typeAccesEtage') }} :
                  <span
                    v-for="(typeAccesEtage,index) in detailAbris.typeAccesEtage"
                    :key="typeAccesEtage.id"
                  >
                    {{ typeAccesEtage.name | ucfirst }}
                    <span
                      v-if="index != Object.keys(detailAbris.typeAccesEtage).length - 1"
                    >,</span>
                  </span>
                </div>
                <hr>
              </div>

              <div class="field-label">
                {{ $t('Entities.Abris.fields.typeSol') }}
              </div>
              <div class="field-value">
                <span
                  v-for="(typeSol,index) in detailAbris.typeSol"
                  :key="typeSol.id"
                >
                  {{ typeSol.name }}
                  <span
                    v-if="index != Object.keys(detailAbris.typeSol).length - 1"
                  >,</span>
                </span>
              </div>
              <hr>

              <div v-if="detailAbris.nbAncrageSol > 0">
                <div
                  class="field-label"
                >
                  {{ $t('Entities.Abris.fields.nbAncrageSol') }} : {{ detailAbris.nbAncrageSol }}
                </div>
                <div
                  class="field-value"
                >
                  {{ $t('Entities.Abris.fields.typeAncrageSol') }} : {{ detailAbris.typeAncrageSol.name }}
                </div>
                <hr>
              </div>

              <div v-if="detailAbris.citerneExterieure || detailAbris.appentisExterieur">
                <div class="field-label">
                  {{ $t('Generics.fields.divers') }}
                </div>
                <div class="field-value">
                  <span v-if="detailAbris.citerneExterieure">
                    {{ $t('Entities.Abris.fields.citerneExterieure') }}
                    <br>
                  </span>
                  <span
                    v-if="detailAbris.appentisExterieur"
                  >{{ $t('Entities.Abris.fields.appentisExterieur') }}</span>
                </div>
                <hr>
              </div>

              <div
                v-if="detailAbris.remarqueStructureBat && detailAbris.remarqueStructureBat.trim().length > 0"
              >
                <div class="field-label">
                  {{ $t('Entities.Abris.fields.remarqueStructureBat') }}
                </div>
                <div class="field-value">
                  {{ detailAbris.remarqueStructureBat }}
                </div>
                <hr>
              </div>
            </section>
          </li>
          <!-- Onglet Equipements et Service -->
          <li
            v-if="activeTab === 3"
            id="tab-gallery"
            class="cd-tabs__panel text-component"
          >
            <vue-title
              :title="$t('Entities.Abris.tabs.equipementServices') + ' - ' + detailAbris.name"
            />
            <h1>{{ detailAbris.name }}</h1>
            <h3>{{ $t('Entities.Abris.tabs.equipementServices') }}</h3>
            <section class="fields-container">
              <div v-if="detailAbris.mobiliers.length > 0">
                <div class="field-label">
                  {{ $t('Entities.Abris.fields.mobiliers') }}
                </div>
                <div class="field-value">
                  <span
                    v-for="(mobilier,index) in detailAbris.mobiliers"
                    :key="mobilier.id"
                  >
                    {{ mobilier.qty }} {{ mobilier.listingValue.name }}(s)
                    <span
                      v-if="index != Object.keys(detailAbris.mobiliers).length - 1"
                    >,</span>
                  </span>
                </div>
                <hr>
              </div>

              <div v-if="detailAbris.couchages.length > 0">
                <div class="field-label">
                  {{ $t('Entities.Abris.fields.couchages') }}
                </div>
                <div class="field-value">
                  <span
                    v-for="(couchage,index) in detailAbris.couchages"
                    :key="couchage.id"
                  >
                    {{ couchage.qty }} {{ couchage.listingValue.name }}(s)
                    <span
                      v-if="index != Object.keys(detailAbris.couchages).length - 1"
                    >,</span>
                  </span>
                </div>
                <hr>
              </div>

              <div v-if="detailAbris.placeDeFeuInterieur.length > 0">
                <div class="field-label">
                  {{ $t('Entities.Abris.fields.placeDeFeuInterieur') }}
                </div>
                <div class="field-value">
                  <span
                    v-for="(placeDeFeu,index) in detailAbris.placeDeFeuInterieur"
                    :key="placeDeFeu.id"
                  >
                    {{ placeDeFeu.qty }} {{ placeDeFeu.listingValue.name }}(s)
                    <span
                      v-if="index != Object.keys(detailAbris.placeDeFeuInterieur).length - 1"
                    >,</span>
                  </span>
                </div>
                <hr>
              </div>

              <div v-if="detailAbris.mobilierPiqueniqueExterieur.length > 0">
                <div
                  class="field-label"
                >
                  {{ $t('Entities.Abris.fields.mobilierPiqueniqueExterieur') }}
                </div>
                <div class="field-value">
                  <span
                    v-for="(mobilierPiquenique,index) in detailAbris.mobilierPiqueniqueExterieur"
                    :key="mobilierPiquenique.id"
                  >
                    {{ mobilierPiquenique.qty }} {{ mobilierPiquenique.listingValue.name }}(s)
                    <span
                      v-if="index != Object.keys(detailAbris.mobilierPiqueniqueExterieur).length - 1"
                    >,</span>
                  </span>
                </div>
                <hr>
              </div>

              <div v-if="detailAbris.materielDivers.length > 0">
                <div class="field-label">
                  {{ $t('Entities.Abris.fields.materielDivers') }}
                </div>
                <div class="field-value">
                  <span
                    v-for="(materielDivers,index) in detailAbris.materielDivers"
                    :key="materielDivers.id"
                  >
                    {{ materielDivers.qty }} {{ materielDivers.listingValue.name }}(s)
                    <span
                      v-if="index != Object.keys(detailAbris.materielDivers).length - 1"
                    >,</span>
                  </span>
                </div>
                <hr>
              </div>

              <div v-if="detailAbris.source">
                <div class="field-label">
                  {{ $t('Entities.Abris.fields.source') }}
                </div>
                <div
                  class="field-value"
                >
                  {{ detailAbris.nomSource }} ({{ detailAbris.coordinateSource }})
                </div>
              </div>
              <hr>

              <div class="field-label">
                {{ $t('Generics.fields.divers') }}
              </div>
              <div class="field-value">
                <span v-if="detailAbris.placeDeFeuExterieure">
                  {{ $t('Entities.Abris.fields.placeDeFeuInterieur') }}
                  <br>
                </span>
                <span v-if="detailAbris.emplacementInterieurReserveBois">
                  {{ $t('Entities.Abris.fields.emplacementInterieurReserveBois') }}
                  <br>
                </span>
                <span v-if="detailAbris.toilettesSeches">
                  {{ $t('Entities.Abris.fields.toilettesSeches') }}
                  <br>
                </span>
                <span v-if="detailAbris.eauCourante">
                  {{ $t('Entities.Abris.fields.eauCourante') }}
                  <br>
                </span>
              </div>
              <hr>
            </section>
          </li>

          <!-- Onglet Autres informations -->
          <li
            v-if="activeTab === 4"
            id="tab-gallery"
            class="cd-tabs__panel text-component"
          >
            <vue-title :title="$t('Entities.Abris.tabs.otherInfos') + ' - ' + detailAbris.name" />
            <h1>{{ detailAbris.name }}</h1>
            <h3>{{ $t('Entities.Abris.tabs.otherInfos') }}</h3>
            <section>
              <div v-if="detailAbris.chemineeEnPierreSurLeToit">
                {{ $t('Entities.Abris.fields.chemineeEnPierreSurLeToit') }}
              </div>
              <div v-if="detailAbris.cahierSuiviEtCrayon">
                {{ $t('Entities.Abris.fields.cahierSuiviEtCrayon') }}
              </div>
              <div v-if="detailAbris.panneauInfosBonnesPratiques">
                {{ $t('Entities.Abris.fields.panneauInfosBonnesPratiques') }}
              </div>
              <div v-if="detailAbris.signaletiqueSourceProche">
                {{ $t('Entities.Abris.fields.signaletiqueSourceProche') }}
              </div>
            </section>
          </li>

          <!-- Onglet liste des dysfonctionnements -->
          <li
            v-if="activeTab === 5"
            id
            class="cd-tabs__panel text-component"
          >
            <vue-title
              :title="$t('Entities.Abris.tabs.dysfonctionnements') + ' - ' + detailAbris.name"
            />
            <h1>{{ detailAbris.name }}</h1>
            <h3>{{ $t('Entities.Abris.tabs.dysfonctionnements') }}</h3>
            <section class="fields-container">
              <div v-if="detailAbris.dysfonctionnements.length > 0">
                <div
                  v-for="(dysfonctionnement) in detailAbris.dysfonctionnements"
                  :key="dysfonctionnement.id"
                >
                  <div class="field-label">
                    <router-link
                      :to="{ name: 'dysfunction-abris', params: { id: dysfonctionnement.id }}"
                    >
                      <i class="fas fa-exclamation-triangle mr-2" />
                      <a>{{ dysfonctionnement.natureDys.name }} - {{ dysfonctionnement.elementDys.name }}</a>
                    </router-link>
                  </div>
                  <span
                    v-if="dysfonctionnement.statusDys"

                    :class="'badge badge-' + statusDys[dysfonctionnement.statusDys.id]"
                  >{{ dysfonctionnement.statusDys.name }}</span>
                  <div class="field-value">
                    {{ $t('Generics.text.on') }} {{ dysfonctionnement.created | moment("DD/MM/YYYY") }}, {{ $t('Generics.text.by') }} {{ (dysfonctionnement.createdBy === null) ? 'Anonyme' : dysfonctionnement.createdBy.firstName }}
                  </div>
                  <hr>
                </div>
              </div>
            </section>
          </li>

          <!-- Onglet liste des discussions -->
          <li
            v-if="activeTab === 6"
            id
            class="cd-tabs__panel text-component"
          >
            <vue-title :title="$t('Entities.Abris.tabs.discussions') + ' - ' + detailAbris.name" />
            <h1>{{ detailAbris.name }}</h1>
            <h3>{{ $t('Entities.Abris.tabs.discussions') }}</h3>
            <section class="fields-container">
              <div v-if="detailAbris.discussions.length > 0">
                <div
                  v-for="discussion in detailAbris.discussions"
                  :key="discussion.id"
                >
                  <div class="field-label">
                    <router-link
                      :to="{ name: 'discussion-abris', params: { id: discussion.id }}"
                    >
                      <i class="far fa-comments" />
                      {{ discussion.name }}
                    </router-link>
                  </div>
                  <div class="field-value">
                    {{ $t('Generics.text.by') }} {{ (discussion.createdBy === null) ? 'Anonyme' : discussion.createdBy.firstName }}, {{ $t('Generics.text.on') }} {{ discussion.updated | moment("DD/MM/YYYY") }} - {{ discussion.messages.length }} message(s)
                  </div>
                  <hr>
                </div>
              </div>
            </section>
          </li>

          <!-- Onglet saisie d'un dysfonctionnement, pas accessible depuis le menu -->
          <li
            v-if="activeTab === 7"
            id
            class="cd-tabs__panel text-component"
          >
            <vue-title
              :title="$t('Entities.Dysfonctionnement.actions.report_new') + ' - ' + detailAbris.name"
            />
            <DysfonctionnementAbrisForm
              :id-abris="detailAbris.id "
              :nom-abris="detailAbris.name "
            />
          </li>

          <!-- Onglet nouvelle discussion, pas accessible depuis le menu -->
          <li
            v-if="activeTab === 8"
            id
            class="cd-tabs__panel text-component"
          >
            <vue-title
              :title="$t('Entities.Dysfonctionnement.actions.report_new') + ' - ' + detailAbris.name"
            />
            <DiscussionAbrisForm
              :id-abris="detailAbris.id"
              :nom-abris="detailAbris.name "
            />
          </li>
        </ul>
        <!-- cd-tabs__panels -->
      </div>
      <!-- cd-tabs -->
    </div>
    <!-- cd-tabs -->

    <div id="bottom-buttons">
      <a
        class="backToList"
        href=""
        @click.prevent="$router.go(-1)"
      >
        <i class="fas fa-angle-left la-2x" />
      </a>

      <router-link to="/abris/search">
        <i class="far fas fa-search-location la-2x" />
      </router-link>

      <a
        class="report-dysfunction"
        href="#"
        @click="setActiveTab(7)"
      >
        <i class="fas fa-exclamation-triangle la-2x" />
      </a>

      <a
        class="add-message"
        href="#"
        @click="setActiveTab(8)"
      >
        <i class="far fa-comments la-2x" />
      </a>

      <a
        class="scroll-to-top d-none d-md-block"
        href="#page-top"
      >
        <i class="fas fa-angle-up la-2x" />
      </a>
    </div>
  </div>
</template>

<script>
import ErrorMessage from "../../components/ErrorMessage";
import LeafLeft from "../../components/Abris/LeafLeft";
import ImageSlider from "../../components/Abris/ImageSlider";
import DysfonctionnementAbrisForm from "../../components/Abris/DysfonctionnementAbrisForm";
import DiscussionAbrisForm from "../../components/Abris/DiscussionAbrisForm";

export default {
  name: "ViewDetailAbris",
  components: {
    ErrorMessage,
    LeafLeft,
    ImageSlider,
    DysfonctionnementAbrisForm,
    DiscussionAbrisForm
  },
  data: function() {
    return {
      formData: null,
      statusDys : {47:'info', 48:'warning', 49:'success'},
    };
  },
  computed: {
    isLoading() {
      return this.$store.getters["detailAbris/isLoading"];
    },
    activeTab() {
      return this.$store.getters["detailAbris/activeTab"];
    },
    hasError() {
      return this.$store.getters["detailAbris/hasError"];
    },
    error() {
      return this.$store.getters["detailAbris/error"];
    },
    isLoaded() {
      return this.$store.getters["detailAbris/isLoaded"];
    },
    isBookmarked() {
      return this.$store.getters["detailAbris/isBookmarked"];
    },
    detailAbris() {
      return this.$store.getters["detailAbris/detail"];
    },
    photos() {
      return this.$store.getters["detailAbris/photos"];
    }
  },
  created() {
    this.$store.dispatch("detailAbris/load", this.$route.params.id);
  },
  destroyed: function () {
    this.$store.dispatch("detailAbris/setActiveTab", 1);
  },  
  methods: {
    setActiveTab(tabNum) {
      this.$store.dispatch("detailAbris/setActiveTab", tabNum);
    },
    async addToBookMark(abris) {
      let result = await this.$store.dispatch("detailAbris/addToBookMark", abris);
      if (result) {
        this.$toasted.show("Abris ajouté à vos favoris", {
          theme: "bubble",
          position: "top-right",
          duration: 2000
        });
      }
    },
    removeBookMark(abris) {
      let result = this.$store.dispatch("detailAbris/removeFromBookMark", abris);
      if (result) {
        this.$toasted.show("Abris supprimé de vos favoris", {
          theme: "bubble",
          position: "top-right",
          duration: 2000
        });
      }
    }
  }
};
</script>

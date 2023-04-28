<template>
  <div id="accordion">
    <h2>Mon compte</h2>
    <div class="card">
      <div
        id="headingOne"
        class="card-header"
      >
        <h5 class="mb-0">
          <button
            class="btn btn-link"
            data-toggle="collapse"
            data-target="#collapseOne"
            aria-expanded="true"
            aria-controls="collapseOne"
          >
            {{ $t('Entities.User.tabs.infoCompte') }}
          </button>
        </h5>
      </div>

      <div
        id="collapseOne"
        class="collapse show"
        aria-labelledby="headingOne"
        data-parent="#accordion"
      >
        <div class="card-body">
          <section
            v-if="!modeEdit"
            class="fields-container"
          >
            <div class="field-label">
              {{ $t('Entities.User.fields.lastName') }}
            </div>
            <div class="field-value">
              {{ user.lastName }}
            </div>
            <hr>
            <div class="field-label">
              {{ $t('Entities.User.fields.firstName') }}
            </div>
            <div class="field-value">
              {{ user.firstName }}
            </div>
            <hr>
            <div class="field-label">
              {{ $t('Entities.User.fields.email') }}
            </div>
            <div class="field-value">
              {{ user.email }}
            </div>
            <hr>
            <div class="field-label">
              {{ $t('Entities.User.fields.userType') }}
            </div>
            <div class="field-value">
              {{ user.userType.name }}
            </div>
            <div>
              <button
                class="btn btn-primary"
                @click="modeEdit = true"
              >
                {{ $t('Generics.actions.edit') }}
              </button>
            </div>
          </section>

          <section v-if="modeEdit">
            <form

              method="post"
              @submit="updateProfile"
            >
              <div class="form-group">
                <label for>{{ $t('Entities.User.fields.lastName') }}</label>
                <input
                  type="text"
                  required="true"
                  name="registration_form[lastName]"
                  class="form-control"
                  :value="user.lastName"
                >
              </div>
              <div class="form-group">
                <label for>{{ $t('Entities.User.fields.firstName') }}</label>
                <input
                  type="text"
                  required="true"
                  name="registration_form[firstName]"
                  class="form-control"
                  :value="user.firstName"
                >
              </div>
              <div class="form-group">
                <label for>{{ $t('Entities.User.fields.userType') }}</label>
                <select
                  v-model="userType"
                  name="registration_form[userType]"
                  class="form-control"
                >
                  <option>----</option>
                  <option
                    v-for="(option) in listUserTypes"
                    :key="option.id"
                    :value="option.id"
                    :selected="option.id == user.userType.id ? 'selected' : ''"
                  >
                    {{ option.name }}
                  </option>
                </select>
              </div>
              <div>
                <span class="col-12">
                  <button
                    type="submit"
                    class="btn btn-primary"
                  >{{ $t('Generics.actions.save') }}</button>
                </span>
                <button
                  type="button"
                  class="btn btn-primary"
                  @click="modeEdit = false"
                >
                  {{ $t('Generics.actions.cancel') }}
                </button>
              </div>
            </form>
          </section>
        </div>
      </div>
    </div>
    <div class="card">
      <div
        id="headingTwo"
        class="card-header"
      >
        <h5 class="mb-0">
          <button
            class="btn btn-link collapsed"
            data-toggle="collapse"
            data-target="#collapseTwo"
            aria-expanded="false"
            aria-controls="collapseTwo"
          >
            {{ $t('Entities.User.tabs.favoris') }}
          </button>
        </h5>
      </div>
      <div
        id="collapseTwo"
        class="collapse"
        aria-labelledby="headingTwo"
        data-parent="#accordion"
      >
        <div class="card-body">
          <div
            v-for="(bookmark) in bookmarks"
            :key="bookmark.id"
            class="row"
          >
            <div class="col-6 mb-3">
              <router-link
                :to="{ name: 'detail-abris', params: { id: bookmark.id }}"
              >
                <i class="las la-home mr-2" />
                {{ bookmark.name }}
              </router-link>
            </div>
            <div class="col-6">
              {{ bookmark.city.name }}
            </div>
          </div>
        </div>
      </div>
      <div class="card">
        <div
          id="headingThree"
          class="card-header"
        >
          <h5 class="mb-0">
            <button
              class="btn btn-link collapsed"
              data-toggle="collapse"
              data-target="#collapseThree"
              aria-expanded="false"
              aria-controls="collapseThree"
            >
              {{ $t('Entities.User.tabs.dysfunctions') }}
            </button>
          </h5>
        </div>
        <div
          id="collapseThree"
          class="collapse"
          aria-labelledby="headingThree"
          data-parent="#accordion"
        >
          <div class="card-body">
            <div
              v-for="(dysfonctionnement) in dysfonctionnements"
              :key="dysfonctionnement.id"
              class="row"
            >
              <div class="col-6">
                <router-link
                  :to="{ name: 'dysfunction-abris', params: { id: dysfonctionnement.id }}"
                >
                  <i class="fas fa-exclamation-triangle mr-2" />
                  {{ $t('Entities.Dysfonctionnement.labels.dateReport') }} {{ dysfonctionnement.created | moment("DD/MM/YYYY") }}
                </router-link>
              </div>
              <div
                class="col-6"
              >
                {{ $t('Entities.Dysfonctionnement.fields.abris') }} {{ dysfonctionnement.abris.name }}
              </div>
              <div
                class="col-12  mb-3"
              >
                <small>{{ dysfonctionnement.natureDys.name }} - {{ dysfonctionnement.elementDys.name }}</small>
              </div>
              <hr>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import UserAPI from "../api/user";

export default {
  name: "Compte",
  data() {
    return {
      modeEdit: false,
      listUserTypes: [],
      userType: null
    };
  },
  computed: {
    isLoading() {
      return this.$store.getters["page/isLoading"];
    },
    hasError() {
      return this.$store.getters["page/hasError"];
    },
    error() {
      return this.$store.getters["page/error"];
    },
    bookmarks() {
      return this.$store.getters["security/bookmarks"];
    },
    dysfonctionnements() {
      return this.$store.getters["security/dysfonctionnements"];
    },
    user() {
      return this.$store.getters["security/user"];
    }
  },
  created() {
    this.getListUserTypes();
  },
  methods: {
    async getListUserTypes() {
      try {
        let response = await UserAPI.listeTypeUser();
        this.listUserTypes = response.data;
        this.userType = this.user.userType.id;
      } catch (error) {
        return null;
      }
    },
    async updateProfile(e) {
      e.preventDefault();
      let form = e.target;
      let formData = new FormData(form);

      await this.$store.dispatch("security/updateProfile", {'user':this.user,'formData':formData});
      this.modeEdit = false;

    }
  }
};
</script>

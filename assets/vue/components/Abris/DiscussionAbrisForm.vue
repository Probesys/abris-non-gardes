<template>
  <div>
    <h1>{{ nomAbris }}</h1>
    <h3>{{ $t('Entities.Discussion.actions.create_new') }}</h3>
    <form
      enctype="multipart/form-data"
      @submit="createDiscussion"
    >
      <input
        type="hidden"
        name="discussion[abris]"
        :value="idAbris"
      >
      <div class="form-group">
        <label for>{{ $t('Entities.Discussion.fields.name') }}</label>
        <input
          type="text"
          required="true"
          name="discussion[name]"
          class="form-control"
          :placeholder="$t('Entities.Discussion.fields.name')"
        >
      </div>
      <div class="form-group">
        <label for>{{ $t('Entities.Discussion.fields.description') }}</label>
        <textarea
          required="true"
          name="discussion[description]"
          class="form-control"
          :placeholder="$t('Entities.Discussion.fields.description')"
        />
      </div>      
      <div class="form-group">
        <div class="form-check">
          <input
            id="isPrivate"
            type="checkbox"
            name="discussion[isPrivate]"
            class="form-check-input mr-2"
            value="1"
          >
          <label
            class="form-check-label ml-2"
            for="abris_form_etage"
          >&nbsp;{{ $t('Entities.Discussion.fields.isPrivate') }}</label>
        </div>
      </div>

      <div class="text-center">
        <button
          type="submit"
          class="btn btn-primary"
        >
          {{ $t('Generics.actions.report') }}
        </button>
        <button
          type="button"
          class="btn btn-primary"
          @click="closeForm()"
        >
          {{ $t('Generics.actions.cancel') }}
        </button>
      </div>
    </form>
  </div>
</template>

<script>
import AbrisAPI from "../../api/abris";

export default {
  name: "DysfonctionnementAbrisForm",
  props: {
    idAbris: {
      type: String,
      required: true
    },
    nomAbris: {
      type: String,
      required: true
    }       
  },

  methods: {
    closeForm() {
      this.$store.dispatch("detailAbris/setActiveTab", 1);
    },
    async createDiscussion(e) {
      e.preventDefault();
      let form = e.target;
      let formData = new FormData(form);
      try {
        let response = await AbrisAPI.createDiscussion(formData);
        let discussionId = response.data['id'];
        this.$router.push({ path: '/abris/details/discussion/' + discussionId })
      } catch (error) {
        return null;
      }
    }
  }
};
</script>

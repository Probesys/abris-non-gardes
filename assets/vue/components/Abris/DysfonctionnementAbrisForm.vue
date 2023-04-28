<template>
  <div>
    <h1>{{ nomAbris }}</h1>
    <h3>{{ $t('Entities.Dysfonctionnement.actions.report_new') }}</h3>
    <form
      enctype="multipart/form-data"
      @submit="createDysfuction"
    >
      <input
        type="hidden"
        name="dysfonctionnement[abris]"
        :value="idAbris"
      >
      <div class="form-group">
        <label for>{{ $t('Entities.Dysfonctionnement.fields.natureDys') }}</label>
        <select
          id="inputNatureDys"
          v-model="natureDys"
          required="true"
          name="dysfonctionnement[natureDys]"
          class="form-control"
          @change="findElementsDys($event)"
        >
          <option value>
            ----
          </option>
          <option
            v-for="(option) in optionsNatureDys"
            :key="option.id"
            :value="option.id"
          >
            {{ option.name }}
          </option>
        </select>
      </div>

      <div
        v-if="natureDys != ''"
        class="form-group"
      >
        <label for>{{ $t('Entities.Dysfonctionnement.fields.elementDys') }}</label>
        <select
          v-model="elementDys"
          name="dysfonctionnement[elementDys]"
          class="form-control"
          required="true"          
        >
          <option />
          <option
            v-for="(option) in optionsElementDys"
            :key="option.id"
            :value="option.id"
          >
            {{ option.name }}
          </option>
        </select>
      </div>


      <div
        v-if="elementDys != ''"
        class="form-group"
      >
        <label for>{{ $t('Entities.Dysfonctionnement.fields.description') }}</label>
        <textarea
          name="dysfonctionnement[description]"
          class="form-control"
          required="true"
        />
        <input
          type="file"
          name="dysfonctionnement[files][]"
          multiple
        >
      </div>

      <div>
        <span
          v-if="elementDys != ''"
          class="col-12"
        >
          <button
            type="submit"
            class="btn btn-primary"
          >{{ $t('Generics.actions.report') }}</button>
        </span>
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
  data: function() {
    return {
      optionsNatureDys: [],
      optionsElementDys: [],
      natureDys: "",
      elementDys: "",
    };
  },
  created() {
    this.findAllNatureDys();
  },
  methods: {
    closeForm() {
      this.$store.dispatch("detailAbris/setActiveTab", 1);
    },
    async findAllNatureDys() {
      try {
        let response = await AbrisAPI.findAllNatureDys();
        this.optionsNatureDys = response.data;
      } catch (error) {
        return null;
      }
    },
    async findElementsDys(event) {
      this.optionsElementDys = [];
      this.elementDys = "";
      try {
        let response = await AbrisAPI.findElementsDys(event.target.value);
        this.optionsElementDys = response.data;
      } catch (error) {
        return null;
      }
    },
    async createDysfuction(e) {
      e.preventDefault();
      let form = e.target;
      let formData = new FormData(form);

      let result = await this.$store.dispatch("detailAbris/createDysfuction", formData);  
      if (result > 0){
        this.$toasted.show("Merci, le dysfonctionnement a bien été pris en compte", {
          theme: "bubble",
          position: "top-right",
          duration: 2000
        });        
      }
      this.$router.push({ path: '/abris/details/dysfonctionnement/' + result})

    }
  }
};
</script>

<template>
  <div class="loader-container">
    <div>
      <bounce />
      <div />
    </div>
  </div>
</template>

<script>
export default {
  name: "Loader",
  props: {
    text: {
      type: String,
      required: false,
      default: 'Chargement en cours...'
    }
  }
};
</script>
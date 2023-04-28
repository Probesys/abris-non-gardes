<script>
export default {
  name: 'VueTitle',
  props:{
    title: {
      type: String,
      required: true,
    },        
  },
  watch: {
    title: {
      immediate: true,
      handler() {
        document.title = this.title;
      }
    }
  },
  render: function (createElement) {
    return createElement('', '')
  }
}
</script>
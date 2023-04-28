<template>
  <div>
    <carousel-3d
      :controls-visible="true"
      :autoplay="images.lenght >= 2 ? true : false"
      :animation-speed="1000"
    >
      <slide
        v-for="(slide, i) in images"
        :key="i"
        :index="i"
      >
        <img :src="slide">
      </slide>
    </carousel-3d>
  </div> 
</template>

<script>
import { Carousel3d, Slide } from 'vue-carousel-3d';

export default {
  name: "ImageSlider",
  components: {
    Carousel3d,
    Slide
  },
  props: {
    images: {
      type: Array, 
      required: true
    },
  },
  
};
</script>
<style scoped>

#slide {
  display: flex;
  overflow: hidden;
  align-items: flex-start;
}

.container {
  padding-left: 50px;
  width: 500px;
  padding-bottom: 100px;
}

.prev {
  display: inline-block;
  margin-right: 50px;
  cursor: pointer;
}

.next {
  display: inline-block;
  cursor: pointer;
}

.control {
  position: absolute;
  bottom: 25;
  right: 30px;
}


#slide > div {
  min-width: 500px;
  padding-right: 20px;
  box-sizing: border-box;
  overflow: hidden;
}

#slide > div > img {
  width:100%;
  display: block;
}


</style> 
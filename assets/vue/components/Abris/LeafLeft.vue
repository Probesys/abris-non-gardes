<template>
  <div class="map-container m-0 border-0">
    <div
      id="map"
      ref="myMap"
    />
  </div>
</template>

<script>
import "leaflet/dist/leaflet.css";
import L from "leaflet";
export default {
  name: "LeafletMap",
  props: {
    coordinate: {
      type: String,
      required: true,
    },
    name: {
      type: String,
      required: true,
    },
    type: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      map: null
    };
  },
  mounted() {
    var markerIcon = L.icon({
      iconUrl: '/img/marker-icon.2273e3d8.png',
    });
    //todo vérifier la validdité du champ coordinate
    var x = this.coordinate.split(',')[0];
    var y = this.coordinate.split(',')[1];

    this.map = L.map("map").setView([x, y], 9);
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
      attribution:
        '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(this.map);
    L.marker([x, y], {icon: markerIcon}).addTo(this.map)
      .bindPopup(this.name +  ' ' + this.type)
      .openPopup();
  },
  beforeDestroy() {
    if (this.map) {
      this.map.remove();
    }
  }
};


</script>
<style scoped>
#map {
  width: 100%;
  height: 100%;
  min-height: 300px;

}
.map-container {
border:1px solid #00000030;;
margin: 3px;
}
</style>

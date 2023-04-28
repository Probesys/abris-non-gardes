<template>
  <div class="map-container m-0 border-0">
    <button
      type="button"
      name="open-search-panel-btn"
      class="open-search-panel-btn circle-btn"
    >
      <i class="las la-search" />
    </button>
    <div
      id="map"
      ref="myMap"
    />
    <modal
      v-show="isModalVisible"
      :id="idAbrisMarker"
      :titre="titleAbrisMarker"
      :description="descriptionAbrisMarker"
      :photo-path="photoAbrisMarkerPath"
      :city="cityAbrisMarker"
      :altitude="altitudeAbrisMarker"
      @close="closeModal"
    />
  </div>
</template>

<script>
import "leaflet/dist/leaflet.css";
import L from "leaflet";
import modal from "../Modal.vue";

export default {
  name: "LeafletMap",
  components: {
    modal
  },
  props: {
    markers: {
      type: Array,
      required: true
    }
  },
  data() {
    return {
      map: null,
      isModalVisible: false,
      titleAbrisMarker: "",
      descriptionAbrisMarker: "",
      photoAbrisMarkerPath: "",
      idAbrisMarker: "",
      cityAbrisMarker: "",
      altitudeAbrisMarker: ""
    };
  },
  mounted() {
    this.$store.dispatch("detailAbris/setActiveTab", 1);
    var myIcon = L.divIcon({
      html: '<i class="marker las la-home la-3x" />',
      iconSize: [20, 20],
      className: "" // We don't want to use the default class
    });
    //todo vérifier la validdité du champ coordinate
    var x = this.markers[0].coords.split(",")[0];
    var y = this.markers[0].coords.split(",")[1];

    this.map = L.map("map", {
      zoomControl: false
    });
    L.control
      .zoom({
        position: "topright"
      })
      .addTo(this.map);

    // L.tileLayer("https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png", {
    //   attribution:
    //     'Map data: &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, <a href="http://viewfinderpanoramas.org">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)'
    // }).addTo(this.map);

    L.tileLayer(
      "https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}",
      {
        attribution:
          "Tiles &copy; Esri &mdash; Esri, DeLorme, NAVTEQ, TomTom, Intermap, iPC, USGS, FAO, NPS, NRCAN, GeoBase, Kadaster NL, Ordnance Survey, Esri Japan, METI, Esri China (Hong Kong), and the GIS User Community"
      }
    ).addTo(this.map);

    var arrayOfLatLngs = [];
    var markersCluster = new L.MarkerClusterGroup();
    this.markers.forEach(element => {
      x = element.coords.split(",")[0];
      y = element.coords.split(",")[1];

      arrayOfLatLngs.push(L.latLng(x, y));
      var marker = L.marker([x, y], { icon: myIcon })
        //.addTo(this.map)
        .on("click", this.showModal);
      marker.abrisId = element.abrisId;
      marker.abrisNom = element.name;
      marker.abrisDescription = element.description;
      marker.abrisPhotos = element.photos[0];
      marker.abrisCity = element.city;
      marker.abrisAltitude = element.altitude;
      marker.bindTooltip(element.name + "<br>" + element.altitude + "m",{permanent:true}).openTooltip();
      markersCluster.addLayer(marker);
    });
    var bounds = new L.LatLngBounds(arrayOfLatLngs);
    this.map.fitBounds(bounds);
    this.map.addLayer(markersCluster);
  },
  beforeDestroy() {
    if (this.map) {
      this.map.remove();
    }
  },
  methods: {
    showModal(e) {
      this.titleAbrisMarker = e.target.abrisNom;
      this.idAbrisMarker = e.target.abrisId;
      this.descriptionAbrisMarker = e.target.abrisDescription;
      this.cityAbrisMarker = e.target.abrisCity;
      this.altitudeAbrisMarker = e.target.abrisAltitude;
      if (typeof e.target.abrisPhotos !== "undefined") {
        this.photoAbrisMarkerPath =
          "/files/" + e.target.abrisId + "/" + e.target.abrisPhotos.fileName;
      } else {
        this.photoAbrisMarkerPath = "";
      }
      this.isModalVisible = true;
    },
    closeModal() {
      this.isModalVisible = false;
    }
  }
};
</script>
<style scoped>
#map {
  width: 100%;
  height: 100vh;
  min-height: 600px;
}
.map-container {
  border: 1px solid #00000030;
  margin: 3px;
  height: 100vh;
}
</style>

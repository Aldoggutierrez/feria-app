<template>
  <div class="drawer max-h-screen">
    <input id="my-drawer" type="checkbox" class="drawer-toggle" />
    <div class="drawer-content">
      <div class="navbar bg-base-100">
        <div class="flex-none">
          <label for="my-drawer" class="btn btn-square btn-ghost drawer-button z-10">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
              class="inline-block w-5 h-5 stroke-current">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
          </label>
        </div>
        <div class="flex-1">
          <a class="btn btn-ghost normal-case text-xl">daisyUI</a>
        </div>
        <div class="flex-none">
          <button class="btn btn-square btn-ghost">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
              class="inline-block w-5 h-5 stroke-current">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z">
              </path>
            </svg>
          </button>
        </div>
      </div>
        <v-map class="w-full h-full" :options="state.map" @loaded="onMapLoaded">
          <template v-if="loaded">
            <!-- <VControlNavigation position="top-right" :options="{showCompass: true,showZoom: true, visualizePitch: true}"/> -->
            <v-marker v-for="(marker, idx) in markers" :key="idx" :coordinates="marker.coordinates" :options="marker.options"
              :popup-options="marker.popup.options">
              <template>
                <div class="p-3">
                  <p><span class="font-bold">Economico:</span> {{ marker.popup.numero }}</p>
                  <p><span class="font-bold">Hora:</span> {{ marker.popup.hora }}</p>
                  <p><span class="font-bold">Sentido:</span> {{ marker.popup.sentido }}</p>
                </div>
              </template>
            </v-marker>
            <VLayerDeckGeojson v-if="deck.geojson.source.features[0].geometry.coordinates" :data="deck.geojson.source" :layer-id="'deck.gl-geojson-layer'" :options="deck.geojson.options"/>
          </template>
        </v-map>
    </div>
    <div class="drawer-side">
      <label for="my-drawer" class="drawer-overlay"></label>
      <ul class="menu p-4 overflow-y-auto w-80 bg-base-100 text-base-content">
        <!-- Sidebar content here -->
        <li><a @click="getData('80-111')">R-1 Central</a></li>
      </ul>
    </div>
  </div>
</template>


<script setup>
import axios from "axios";
import { VMap, VMarker, VLayerDeckGeojson, VControlNavigation } from "v-mapbox";
import { reactive, computed, ref } from "vue";

const state = reactive({
  ui: {
    loaded: false,
    styleChanged: true,
  },
  map: {
    accessToken:
      "pk.eyJ1Ijoic29jaWFsZXhwbG9yZXIiLCJhIjoiREFQbXBISSJ9.dwFTwfSaWsHvktHrRtpydQ",
    style: "mapbox://styles/mapbox/streets-v11?optimize=true",
    // style: "https://basemaps.cartocdn.com/gl/dark-matter-gl-style/style.json",
    center: [-100.28281688690186, 25.677269657380343],
    zoom: 12,
    maxZoom: 22,
    crossSourceCollisions: false,
    failIfMajorPerformanceCaveat: false,
    attributionControl: false,
    preserveDrawingBuffer: true,
    hash: false,
    minPitch: 0,
    maxPitch: 60,
  },
});

const geoJsonSource = {
  'id': 'route',
  'type': 'geojson',
  'data': {
    'type': 'Feature',
    'properties': {},
    'geometry': {
      'type': 'LineString',
      'coordinates': [
        [-100.286455068109, 25.6506166592275],
        [-100.289997430754, 25.6479278305147],
        [-100.292101623294, 25.6511887244147],
        [-100.292137637195, 25.6515898509034],
        [-100.292424851361, 25.6517810641469],
        [-100.296576579111, 25.6583615154629],
        [-100.297088859472, 25.6599875152764],
        [-100.297193526405, 25.6663642653212],
        [-100.298706025688, 25.6692154658673],
        [-100.298249021225, 25.6720864005858],
        [-100.298543191981, 25.6721031861682],
        [-100.299092181543, 25.6689029685078],
        [-100.299867878994, 25.6682549442251],
        [-100.30389192241, 25.6659674345714],
        [-100.305832632835, 25.6644094945316],
        [-100.307517097689, 25.6637173326199],
        [-100.309845142796, 25.6634467799376],
        [-100.315832822021, 25.6647663287149],
        [-100.315980426615, 25.6653211370305],
        [-100.315744686648, 25.6663877216769],
        [-100.313075750692, 25.6858990017778]
      ]
    }
  }
}
const geoJsonLayer = {
  'type': 'line',
  'source': 'route',
  'layout': {
    'line-join': 'round',
    'line-cap': 'round'
  },
  'paint': {
    'line-color': '#888',
    'line-width': 8
  }
}

const deck = ref({
  geojson: {
    source: {
      type: "FeatureCollection",
      features: [
        {
          type: "Feature",
          properties: {},
          geometry: {
            type: "LineString",
            coordinates: [
              [-100.286455068109, 25.6506166592275],
            ],
          },
        },
      ],
    },
    options: {
      pickable: true,
      stroked: false,
      filled: true,
      extruded: true,
      pointType: "circle",
      lineWidthScale: 20,
      lineWidthMinPixels: 2,
      getFillColor: [33, 160, 180, 200],
      getPointRadius: 100,
      getLineWidth: 1,
      getElevation: 200,
    },
  },
});

const loaded = computed(() => state.ui.loaded);

const markers = ref([]);

function onMapLoaded(map) {
  [
    "idle",
    "moveend",
    "touchend",
    "style.load",
    "sourcedata",
    "sourcedataloading",
  ].forEach((event) => {
    map.on(event, () => {
      if (event === "style.load") {
        const waiting = () => {
          if (!map.isStyleLoaded()) {
            state.ui.styleChanged = false;
            setTimeout(waiting, 200);
          } else {
            state.ui.styleChanged = true;
          }
        };
        waiting();
      }
    });
  });
  state.ui.styleChanged = true;
  state.ui.loaded = true;
}

const getData = async (id) => {
  const {data} = await axios.get(`/api/economic?id=${id}`)
  let results = []
  data.forEach((value,index) =>{
    let data = {
      options: { color: value.sentido == "ida"? "green": "orange", draggable: false },
      coordinates: [value.longitud, value.latitud],
      popup: {
        options: {
          closeButton: false,
          closeOnClick: true,
          closeOnMove: true,
        },
        numero: value.numero,
        hora: value.hora,
        sentido: value.sentido
      },
    }

    results.push(data)
  })
  markers.value = results

  const response = await axios.get(`/api/route?id=${id}`)
  let coordinates = []
  response.data.forEach((value,index) => {
    coordinates.push([value.longitud,value.latitud])
    deck.value.geojson.source.features.at(0).geometry.coordinates = coordinates
  })
}
</script>

<style lang="scss">
@import "mapbox-gl/dist/mapbox-gl.css";
@import "v-mapbox/dist/v-mapbox.css";
html,
body {
  margin: 0;
}
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
}
</style>
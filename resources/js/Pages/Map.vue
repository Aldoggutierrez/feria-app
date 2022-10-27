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
            <v-marker v-for="(marker, idx) in markers" :key="idx" :coordinates="marker.coordinates" :options="marker.options"
              :popup-options="marker.popup">
              <template>
                <div class="p-2 text-black">
                  Popup Content: {{ marker.popup.content }}
                  <img class="rounded shadow-sm" src="https://picsum.photos/200" />
                </div>
              </template>
            </v-marker>
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
import { VMap, VMarker } from "v-mapbox";
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
        content: "ABC",
      },
    }

    results.push(data)
  })
  markers.value = results
  console.log(results);
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
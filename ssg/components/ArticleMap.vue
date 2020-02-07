<template>
  <div style="height: 100%; width: 100%; overflow: hidden;">
    <div class="info" style="height: 15%;">
      <span>Center: {{ center }}</span>
      <span>Zoom: {{ zoom }}</span>
      <span>Bounds: {{ bounds }}</span>
    </div>
    <client-only>
      <l-map
        :center="center"
        :zoom="zoom"
        @update:bounds="boundsUpdated"
        @update:center="centerUpdated"
        @update:zoom="zoomUpdated"
        style="height: 85%; width: 100%;"
      >
        <l-tile-layer :url="url"></l-tile-layer>
        <l-marker
          v-for="(marker, index) in markers"
          :key="index"
          :lat-lng="marker.position"
        >
          <l-tooltip>{{ marker.text }}</l-tooltip>
        </l-marker>
      </l-map>
    </client-only>
  </div>
</template>

<script>
export default {
  props: {
    articles: {
      type: Array,
      required: true
    }
  },
  data() {
    return {
      url: 'http://{s}.tile.osm.org/{z}/{x}/{y}.png',
      zoom: 11,
      center: [52.507445, 13.391647],
      bounds: null
    }
  },
  computed: {
    // eslint-disable-next-line object-shorthand
    markers: function() {
      const markers = []
      if (!this.articles) {
        return markers
      }
      this.articles.forEach((article) => {
        if (!article.addresses) {
          return
        }
        article.addresses.forEach((address) => {
          if (!address.coordinate) {
            return
          }
          markers.push({
            position: [address.coordinate.lat, address.coordinate.lon],
            text: article.title
          })
        })
      })
      return markers
    }
  },
  methods: {
    zoomUpdated(zoom) {
      this.zoom = zoom
    },
    centerUpdated(center) {
      this.center = center
    },
    boundsUpdated(bounds) {
      this.bounds = bounds
    }
  }
}
</script>

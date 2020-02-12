<template>
  <div style="height: 100%; width: 100%;">
    <client-only>
      <div v-if="debug" class="info">
        <span>Center: {{ center }}</span>
        <span>Zoom: {{ zoom }}</span>
        <span>Bounds: {{ bounds }}</span>
      </div>
      <l-map
        :center="center"
        :zoom="zoom"
        @update:bounds="boundsUpdated"
        @update:center="centerUpdated"
        @update:zoom="zoomUpdated"
        style="height: 100%; width: 100%;"
      >
        <l-tile-layer :url="url"></l-tile-layer>
        <span v-for="(marker, index) in markers" :key="index">
          <l-circle-marker
            v-if="marker.type === 'point'"
            :lat-lng="marker.latlng"
            :color="highlight === marker.article ? 'red' : 'blue'"
            :radius="10"
            @mouseover="$emit('maphover', marker.article)"
            @mouseleave="$emit('maphover', {})"
          >
            <l-tooltip>{{ marker.article.title }}</l-tooltip>
          </l-circle-marker>
          <l-polygon
            v-else
            :lat-lngs="marker.latlngs"
            :color="highlight === marker.article ? 'red' : 'blue'"
            @mouseover="$emit('maphover', marker.article)"
            @mouseleave="$emit('maphover', {})"
          >
            <l-tooltip>{{ marker.article.title }}</l-tooltip>
          </l-polygon>
        </span>
      </l-map>
    </client-only>
  </div>
</template>

<script>
export default {
  props: {
    debug: {
      type: Boolean,
      default: false
    },
    articles: {
      type: Array,
      required: true
    },
    highlight: {
      type: Object,
      default: () => ({})
    }
  },
  data() {
    return {
      url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
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
        const latlngs = []
        article.addresses.forEach((address) => {
          if (!address.coordinate) {
            return
          }
          latlngs.push([address.coordinate.lat, address.coordinate.lon])
        })
        if (latlngs.length === 1) {
          markers.push({
            type: 'point',
            latlng: latlngs[0],
            article
          })
        } else if (latlngs.length > 1) {
          markers.push({
            type: 'polygon',
            latlngs,
            article
          })
        }
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

<style scoped="scoped">
.info {
  height: 3em;
  position: absolute;
  z-index: 100;
  top: 0;
  left: 0;
  opacity: 0.8;
}
</style>

<template>
  <div
    :class="{ 'article-teaser--highlighted': highlight === article }"
    class="article-teaser"
  >
    <v-icon
      :title="hasAnyCoordinate ? 'Koordinate vorhanden' : 'Kein Koordinate'"
      class="float-right"
    >
      {{ hasAnyCoordinate ? 'mdi-map-check-outline' : 'mdi-map-outline' }}
    </v-icon>
    <a :href="article.link">{{ article.title }}</a>
    <p>
      {{ formattedDate }}, Ereignisort: <strong>{{ article.districts }}</strong>
    </p>
    <p class="text">
      {{ article.text }}
    </p>
    <!-- <nuxt-link :to="'/users/' + user.id">{{ user.name }}</nuxt-link> -->
  </div>
</template>

<script>
import dateFormat from 'dateformat'

export default {
  props: {
    article: {
      type: Object,
      required: true
    },
    highlight: {
      type: Object,
      default: () => ({})
    }
  },
  computed: {
    // eslint-disable-next-line object-shorthand
    formattedDate: function() {
      return dateFormat(new Date(this.article.date), 'dd.mm.yyyy HH:MM')
    },
    // eslint-disable-next-line object-shorthand
    hasAnyCoordinate: function() {
      return this.article.addresses.some((address) => address.coordinate)
    }
  }
}
</script>

<style scoped="scoped">
.article-teaser {
  padding: 0.5em;
  transition: background-color 0.3s;
}

.article-teaser--highlighted {
  background-color: #47494e;
}

.text {
  max-height: 8ex;
  overflow: hidden;
}

.article-teaser--highlighted .text {
  max-height: none;
}
</style>

<template>
  <div>
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
    <p class="text">{{ article.text }}</p>
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
.text {
  max-height: 8ex;
  overflow: hidden;
}

.text:hover {
  max-height: none;
}
</style>

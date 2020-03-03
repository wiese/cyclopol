<template>
  <section class="container">
    <div>
      <h2 class="subtitle">Die {{ article_list.length }} neusten Meldungen</h2>
      <div class="row">
        <v-text-field v-model="search" label="Suchbegriff" class="col" />
        <v-select
          :items="maxItemsDefault"
          v-model="limit"
          class="col"
          label="Anzahl (max.)"
        />
      </div>
      <div class="row" style="height: 224px;">
        <ArticleCountTimeline
          :search="search"
          width="100%"
          height="200px"
          class="col"
        />
      </div>
      <div class="row">
        <ul class="col">
          <li
            v-for="(article, index) in article_list"
            :key="index"
            @mouseenter.passive="highlight = article"
            @mouseleave.passive="highlight = {}"
          >
            <ArticleTeaser :article="article" :highlight="highlight" />
          </li>
        </ul>
        <div class="col mapcol">
          <Affix :wiggleRoom="75">
            <div class="map">
              <ArticleMap
                :articles="article_list"
                :highlight="highlight"
                @maphover="highlight = $event"
              />
            </div>
          </Affix>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import gql from 'graphql-tag'
import Affix from '@/components/Affix.vue'
import ArticleTeaser from '@/components/ArticleTeaser.vue'
import ArticleMap from '@/components/ArticleMap.vue'
import ArticleCountTimeline from '@/components/ArticleCountTimeline'

export default {
  components: {
    Affix,
    ArticleTeaser,
    ArticleMap,
    ArticleCountTimeline
  },
  data: () => ({
    maxItemsDefault: [5, 10, 50, 100],
    limit: 5,
    search: '',
    highlight: {}
  }),
  apollo: {
    article_list: {
      query: gql`
        query article_list($limit: Int!, $search: String!) {
          article_list(limit: $limit, search: $search) {
            link
            title
            date
            text
            districts
            addresses {
              street
              number
              coordinate {
                name
                lat
                lon
              }
            }
          }
        }
      `,
      variables() {
        return {
          limit: this.limit,
          search: this.search
        }
      },
      throttle: 300
    }
  }
}
</script>

<style>
.map {
  width: 800px;
  height: 800px;
}

.mapcol .affix-scrolledPast {
  position: fixed;
  top: 75px;
}
</style>

<template>
  <section class="container">
    <div>
      <h1 class="title">
        Meldungen
      </h1>
      <h2 class="subtitle">
        Die {{ article_list.articles.length }} neusten Meldungen
      </h2>
      <div class="row">
        <v-text-field v-model="search" label="Suchbegriff" class="col" />
        <v-select
          :items="maxItemsDefault"
          v-model="limit"
          class="col"
          label="Anzahl (max.)"
        />
      </div>
      <div class="row">
        <ul class="col">
          <li
            v-for="(article, index) in article_list.articles"
            :key="index"
            @mouseover="highlight = article"
            @mouseleave="highlight = {}"
          >
            <ArticleTeaser :article="article" />
          </li>
        </ul>
        <div class="col" style="width: 800px; height: 800px;">
          <ArticleMap
            :articles="article_list.articles"
            :highlight="highlight"
          />
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import gql from 'graphql-tag'
import ArticleTeaser from '@/components/ArticleTeaser.vue'
import ArticleMap from '@/components/ArticleMap.vue'

export default {
  components: {
    ArticleTeaser,
    ArticleMap
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
            articles {
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

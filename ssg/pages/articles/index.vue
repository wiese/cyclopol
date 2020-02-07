<template>
  <section class="container">
    <div>
      LOGO
      <h1 class="title">
        Meldungen
      </h1>
      <h2 class="subtitle">
        Die {{ article_list.articles.length }} neusten Meldungen
      </h2>
      <ul>
        <li v-for="article in article_list.articles">
          <ArticleTeaser :article="article" />
        </li>
      </ul>
      <div style="width: 800px; height: 800px;">
        <ArticleMap :articles="article_list.articles" />
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
  apollo: {
    article_list: gql`
      query {
        article_list(limit: 10) {
          articles {
            link
            title
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
    `
  }
}
</script>

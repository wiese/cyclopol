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
    </div>
  </section>
</template>

<script>
import gql from 'graphql-tag'
import ArticleTeaser from '@/components/ArticleTeaser.vue'

export default {
  components: {
    ArticleTeaser
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
          }
        }
      }
    `
  }
}
</script>

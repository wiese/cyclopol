<template>
  <div>
    <a :href="article.link">{{ article.title }}</a>
    <p>
      Ereignisort: <strong>{{ article.districts }}</strong>
    </p>
    <p>{{ article.text }}</p>
    <!-- <nuxt-link :to="'/users/' + user.id">{{ user.name }}</nuxt-link> -->
  </div>
</template>

<script>
export default {
  props: {
    article: {
      type: Object,
      required: true
    }
  }
}
</script>

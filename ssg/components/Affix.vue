<template>
  <div :class="{ 'affix-scrolledPast': scrolledPast }" class="affix-container">
    <slot />
  </div>
</template>

<script>
export default {
  props: {
    wiggleRoom: {
      type: Number,
      default: 0
    }
  },
  data: () => ({
    scrollposition: { x: 0, y: 0 },
    fixedFrom: null
  }),
  computed: {
    // eslint-disable-next-line object-shorthand
    scrolledPast: function() {
      if (this.fixedFrom === null) {
        return false
      }
      return this.scrollposition.y + this.wiggleRoom > this.fixedFrom
    }
  },
  mounted() {
    // When scrolling, update the position
    // eslint-disable-next-line nuxt/no-globals-in-created
    window.addEventListener('scroll', this.scrollListener, { passive: true })

    // Call listener once to detect initial position
    this.scrollListener()

    // eslint-disable-next-line nuxt/no-globals-in-created
    const el = document.getElementsByClassName('affix-container')[0]
    // eslint-disable-next-line nuxt/no-globals-in-created
    const documentScrollY = window.scrollY

    this.fixedFrom = el.getBoundingClientRect().top + documentScrollY
  },
  destroyed() {
    window.removeEventListener('scroll', this.scrollListener)
  },
  methods: {
    // eslint-disable-next-line object-shorthand
    scrollListener: function() {
      this.scrollposition.x = Math.round(window.pageXOffset) // eslint-disable-line nuxt/no-globals-in-created
      this.scrollposition.y = Math.round(window.pageYOffset) // eslint-disable-line nuxt/no-globals-in-created
    }
  }
}
</script>

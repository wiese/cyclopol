<template>
  <div>
    <div ref="visualization"></div>
  </div>
</template>

<script>
import gql from 'graphql-tag'
import { DataSet, Graph2d } from 'vis-timeline/dist/vis-timeline-graph2d.min.js'
import 'vis-timeline/dist/vis-timeline-graph2d.css'

export default {
  props: {
    width: {
      type: String,
      required: true
    },
    height: {
      type: String,
      required: true
    },
    search: {
      type: String,
      required: true
    }
  },
  data: () => ({
    graph: null,
    items: null
  }),
  watch: {
    // eslint-disable-next-line object-shorthand
    article_count: function(articleCount) {
      const items = []
      articleCount.forEach((set) => {
        items.push({
          x: set.date,
          y: set.count
        })
      })
      if (this.items === null) {
        this.items = new DataSet(items)
        this.graph = new Graph2d(this.$refs.visualization, this.items, {
          style: 'bar',
          drawPoints: false,
          // orientation: 'top',
          width: this.width,
          height: this.height
          // start: '2020-01-01',
          // end: '2020-03-01'
        })
      } else {
        this.items.clear()
        this.items.add(items)
        this.graph.redraw()
      }
    }
  },
  apollo: {
    article_count: {
      query: gql`
        query article_count($search: String!) {
          article_count(search: $search) {
            count
            date
          }
        }
      `,
      variables() {
        return {
          search: this.search
        }
      }
    }
  }
}
</script>

<style>
#visualization {
  width: 100%;
  height: 100%;
  border: 1px solid lightgray;
}
</style>

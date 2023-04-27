<template>
  <div>
    <v-data-table
      :mobile-breakpoint="0"
      :sort-by="sortBy"
      :sort-desc="sortDesc"
      :items="items"
      :headers="headers"
      item-key="id"
      :loading="loading"
      hide-default-footer
      class="elevation-4 ma-2 text-uppercase"
    >
      <template #top="{ top }">
        <slot name="top" :top="top" />
      </template>
      <template #item="{ item }">
        <slot name="item" :item="item" />
      </template>

      <template #no-results>
        <v-alert color="warning" icon="warning" outlined>
          Sin Resultados.
        </v-alert>
      </template>
    </v-data-table>
    <!-- <Pagination :links="links" /> -->
  </div>
</template>

<script>
// import Pagination from '@/Shared/Pagination'

export default {
  name: "DataTableWrapper",
  components: {
    // Pagination,
  },

  props: {
    items: {
      type: Array,
      default: () => [],
    },
    links: {
      type: Array,
      default: () => [],
    },
    headers: {
      type: Array,
      default: () => [],
    },
    sortBy: {
      type: [String, Array],
      default: () => null,
    },
    sortDesc: {
      type: Boolean,
      default: false,
    },
    loading: {
      type: Boolean,
      default: false,
    },
  },

  data: () => ({}),
};
</script>

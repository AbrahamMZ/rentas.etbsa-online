<template>
  <v-breadcrumbs :items="items">
    <template v-slot:divider>
      <v-icon>mdi-chevron-right</v-icon>
    </template>
    <template v-slot:item="{ item }">
      <v-breadcrumbs-item>
        <v-btn :disabled="item.disabled" color="blue" text @click="To(item.href)">
          {{ item.text }}
        </v-btn>
      </v-breadcrumbs-item>
    </template>
  </v-breadcrumbs>
</template>

<script>
export default {
  props: {
    items: {
      type: Array,
      required: true,
    },
  },
  methods: {
    To(_route) {
      this.$inertia.visit(_route);
    },
  },
};
</script>

<style></style>

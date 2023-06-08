<template>
  <v-list v-model="active" mandatory>
    <v-subheader v-if="!isChild">Menu</v-subheader>
    <template v-for="menu in menus">
      <v-list-item
        v-if="menu.enabled && !menu.children"
        :key="`list-item-${menu.route}`"
        active-color="primary"
        link
        @click="Click(menu.route)"
      >
        <v-list-item-icon v-if="!isChild">
          <v-icon>{{ menu.icon }}</v-icon>
        </v-list-item-icon>
        <v-list-item-icon v-else />

        <v-list-item-content>
          <v-list-item-title>{{ menu.name }}</v-list-item-title>
        </v-list-item-content>

        <v-list-item-icon v-if="isChild">
          <v-icon>{{ menu.icon }}</v-icon>
        </v-list-item-icon>
      </v-list-item>
      <v-list-group
        v-else-if="menu.children"
        :key="`list-group-${menu.route || menu.name}`"
        :prepend-icon="menu.icon"
      >
        <template #activator>
          <v-list-item-content>
            <v-list-item-title>{{ menu.name }}</v-list-item-title>
          </v-list-item-content>
        </template>
        <NestedMenu :menus="menu.children" is-child />
      </v-list-group>
    </template>
  </v-list>
</template>
  

<script>
export default {
  name: "NestedMenu",
  props: { menus: Array, isChild: { type: Boolean, default: false } },
  data() {
    return {
      selectedItem: 1,
      active: 1,
    };
  },
  mounted() {
    for (const key in this.menus) {
      if (!this.menus[key].route) {
        this.active = parseInt(key);
        return;
      }
    }
  },
  methods: {
    Click(_route) {
      this.$inertia.visit(this.route(_route));
    },
  },
};
</script>
  
<!-- eslint-disable vue/html-self-closing -->
<template>
  <v-app id="inspire">
    <!-- <drawer
      :drawer="drawer"
      :sidebar-color="sidebarColor"
      :sidebar-theme="sidebarTheme"
    /> -->
    <v-navigation-drawer v-model="drawer" app class="d-print-none">
      <v-sheet color="grey lighten-4" class="pa-4">
        <v-avatar
          class="hidden-sm-and-down mb-4"
          color="grey darken-1 shrink"
          size="64"
        >
          <img
            v-if="$page.props.auth.user.photo"
            :src="$page.props.auth.user.photo"
            :alt="$page.props.auth.user.first_name"
          />
        </v-avatar>

        <div>{{ $page.props.auth.user.name }}</div>
      </v-sheet>

      <v-divider />

      <nested-menu :menus="$page.props.navigation.menu" />

      <template v-slot:append>
        <div class="pa-2">
          <inertia-link
            class="overline"
            :href="route('logout')"
            method="post"
            block
            as="v-btn"
          >
            Cerrar Sesion
          </inertia-link>
        </div>
      </template>
    </v-navigation-drawer>

    <v-app-bar app dense flat class="d-print-none">
      <v-app-bar-nav-icon @click="drawer = !drawer" />
      <slot name="breadcrumbs" />
    </v-app-bar>

    <v-main>
      <v-container fluid class="overflow-y-auto grey lighten-5 ">
        <v-row>
          <v-col cols="12">
            <flash-messages />
            <v-card class="d-print-table">
              <slot />
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-main>
    <portal-target name="modal" multiple />
  </v-app>
</template>

<script>
import FlashMessages from "./FlashMessages.vue";
import NestedMenu from "./NestedMenu.vue";
export default {
  components: { FlashMessages, NestedMenu },
  data: () => ({
    drawer: null,
    showSettingsDrawer: false,
    sidebarColor: "success",
    sidebarTheme: "transparent",
    navbarFixed: false,
  }),
  methods: {},
};
</script>
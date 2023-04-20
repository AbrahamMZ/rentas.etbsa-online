<template>
  <v-app id="inspire">
    <v-navigation-drawer v-model="drawer" app width="224">
      <v-sheet color="grey lighten-4" class="pa-4">
        <v-avatar
          class="hidden-sm-and-down mb-4"
          color="grey darken-1 shrink"
          size="64"
        >
          <!-- eslint-disable-next-line vue/html-self-closing -->
          <img
            v-if="$page.props.auth.user.photo"
            :src="$page.props.auth.user.photo"
            :alt="$page.props.auth.user.first_name"
          />
        </v-avatar>

        <div>{{ $page.props.auth.user.name }}</div>
      </v-sheet>

      <v-divider />

      <v-list>
        <v-list-item
          v-for="tab in Menus"
          :key="tab.route"
          link
          @click="click(tab.route)"
        >
          <v-list-item-icon>
            <v-icon>{{ tab.icon }}</v-icon>
          </v-list-item-icon>

          <v-list-item-content>
            <v-list-item-title>{{ tab.label }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-divider />
        <v-list-item>
          <v-list-item-content>
            <v-list-item-title>
              <inertia-link
                class="overline"
                :href="route('logout')"
                method="post"
                as="button"
              >
                Cerrar Sesion
              </inertia-link>
            </v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>
    <v-app-bar app dense flat>
      <v-app-bar-nav-icon @click="drawer = !drawer" />
      <slot name="breadcrumbs" />
    </v-app-bar>

    <v-main>
      <v-container fluid>
        <v-row>
          <v-col cols="12">
            <flash-messages />
            <v-card>
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
export default {
  components: { FlashMessages },
  data: () => ({
    drawer: null,
    // showUserMenu: false,
    // accounts: null,
    // active: null,
    // items: [{ title: "Logout", route: "logout" }],
  }),
  // mounted() {
  //   for (const key in this.$page.props.tabs) {
  //     if (!this.$page.props.tabs[key].route) {
  //       this.active = parseInt(key);
  //       return;
  //     }
  //   }
  // },
  computed: {
    Menus() {
      return this.$page.props.tabs.filter((menu) => {
        return !!menu.show;
      });
    },
  },
  methods: {
    click(_route) {
      this.$inertia.visit(this.route(_route));
    },
    // url() {
    //   return location.pathname.substr(1);
    // },
    // hideDropdownMenus() {
    //   this.showUserMenu = false;
    // },
  },
};
</script>
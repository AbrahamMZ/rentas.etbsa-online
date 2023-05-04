<!-- eslint-disable vue/html-self-closing -->
<template>
  <v-navigation-drawer
    fixed
    app
    floating
    :expand-on-hover="mini"
    :value="drawer"
    :right="$vuetify.rtl"
    :class="!$vuetify.breakpoint.mobile ? '' : 'bg-white'"
    :data-color="sidebarColor"
    :data-theme="sidebarTheme"
  >
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

    <v-divider class="mb-4" />
    <v-list nav>
      <v-list-item-group v-model="selectedItem" mandatory>
        <v-list-item v-for="item in ListItems" :key="item.title">
          <v-list-item-icon>
            <v-icon>{{ item.icon }}</v-icon>
          </v-list-item-icon>
          <v-list-item-content>
            <v-list-item-title @click="click(item.route)">
              {{ item.title }}
            </v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list-item-group>

      <v-list-group
        v-for="item in ListGroups"
        :key="item.title"
        :value="true"
        :prepend-icon="item.icon"
      >
        <template v-slot:activator>
          <v-list-item-title>{{ item.title }}</v-list-item-title>
        </template>

        <!-- <v-list-group :value="true" no-action sub-group>
          <template v-slot:activator>
            <v-list-item-content>
              <v-list-item-title>Admin</v-list-item-title>
            </v-list-item-content>
          </template>

          <v-list-item v-for="([title, icon], i) in admins" :key="i" link>
            <v-list-item-title v-text="title"></v-list-item-title>

            <v-list-item-icon>
              <v-icon v-text="icon"></v-icon>
            </v-list-item-icon>
          </v-list-item>
        </v-list-group> -->
        <v-list-item-group>
          <v-list-item v-for="([title, icon], i) in cruds" :key="i" link>
            <v-list-item-title v-text="title"></v-list-item-title>

            <v-list-item-icon>
              <v-icon v-text="icon"></v-icon>
            </v-list-item-icon>
          </v-list-item>
        </v-list-item-group>
      </v-list-group>
    </v-list>
  </v-navigation-drawer>
</template>
<script>
export default {
  name: "Drawer",
  props: {
    drawer: {
      type: Boolean,
      default: true,
    },
    sidebarColor: {
      type: String,
      default: "success",
    },
    sidebarTheme: {
      type: String,
      default: "transparent",
    },
  },
  data: () => ({
    selectedMenu: 0,
    mini: false,
    togglerActive: false,
    thirdLevelSimple: [
      "Third level menu",
      "Just another link",
      "One last link",
    ],
    admins: [
      ["Management", "mdi-account-multiple-outline"],
      ["Settings", "mdi-account-box"],
    ],
    cruds: [
      ["Create", "mdi-plus-outline"],
      ["Read", "mdi-file-outline"],
      ["Update", "mdi-update"],
      ["Delete", "mdi-delete"],
    ],
    menus: [
      {
        title: "Dashboard",
        icon: "mdi-menu",
        route: "users",
      },
      {
        title: "Users",
        icon: "mdi-account",
        childs: [
          {
            title: "Admin",
            childs: [
              {
                title: "Management",
                icon: "mdi-account-multiple-outline",
                route: "users",
              },
              {
                title: "Settings",
                icon: "mdi-account-multiple-outline",
                route: "users",
              },
            ],
          },
          {
            title: "Actions",
            childs: [
              {
                title: "Create",
                icon: "mdi-plus-outline",
                route: "users.create",
              },
              { title: "Read", icon: "mdi-file-outline", route: "users" },
              { title: "Update", icon: "mdi-update", route: "users.edit" },
              { title: "Delete", icon: "mdi-delete", route: "users.delete" },
            ],
          },
        ],
      },
    ],
  }),
  computed: {
    ListItems() {
      return this.menus.filter((item) => !item.childs);
    },
    ListGroups() {
      return this.menus.filter((item) => !!item.childs);
    },
  },
  methods: {
    click(_route) {
      this.$inertia.visit(this.route(_route));
    },
  },
};
</script>

<template>
  <div>
    <v-snackbar
      v-model="showSnack"
      :color="successFlash ? 'success' : 'error'"
      content-class="title"
      :timeout="5000"
      multi-line
      right
      top
      @input="close()"
    >
      <template v-if="successFlash">
        {{ $page.props.flash.success }}
      </template>
      <template v-else-if="errorFlash">
        {{ $page.props.flash.error }}
      </template>
      <template v-else-if="errorsFormFlash">
        <span v-if="Object.keys($page.props.errors).length === 1">
          Existe error(es) en el Fomulario
        </span>
        <span v-else>
          Existen {{ Object.keys($page.props.errors).length }} errores de
          formulario
        </span>
      </template>

      <template v-slot:action="{ attrs }">
        <v-btn icon v-bind="attrs" @click="close">
          <v-icon> mdi-window-close </v-icon>
        </v-btn>
      </template>
    </v-snackbar>
  </div>
</template>

<script>
export default {
  data() {
    return {
      show: false,
    };
  },
  computed: {
    successFlash() {
      return this.$page.props.flash.success && this.show;
    },
    errorFlash() {
      return this.$page.props.flash.error && this.show;
    },
    errorsFormFlash() {
      return Object.keys(this.$page.props.errors).length > 0 && this.show;
    },
    showSnack: {
      get() {
        return (
          (this.successFlash || this.errorFlash || this.errorsFormFlash) &&
          this.show
        );
      },
      set(v) {
        this.show = v;
      },
    },
  },
  watch: {
    "$page.props.flash": {
      handler() {
        this.show = true;
      },
      deep: true,
    },
  },
  methods: {
    close() {
      this.show = false;
    },
  },
};
</script>

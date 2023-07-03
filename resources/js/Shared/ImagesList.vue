<template>
  <v-row>
    <v-col v-for="(image, i) in Images" :key="i" cols="12" md="3">
      <v-slide-x-transition>
        <v-hover v-slot="{ hover }">
          <v-card class="mx-auto" color="grey lighten-4" max-width="600">
            <v-img
              :src="image.path"
              :lazy-src="`https://picsum.photos/10/6?image=${i * 5 + 10}`"
              :alt="image.path"
              aspect-ratio="1"
            >
              <v-expand-transition>
                <div
                  v-if="hover"
                  class="d-flex justify-space-around transition-fast-in-fast-out white darken-1 v-card--reveal text-h2 white--text"
                  style="height: 100%"
                >
                  <v-btn icon x-large @click="show(i)">
                    <v-icon x-large color="blue"> mdi-arrow-expand-all </v-icon>
                  </v-btn>

                  <v-btn icon x-large @click="remove(i, image)">
                    <v-icon x-large color="red"> mdi-trash-can </v-icon>
                  </v-btn>
                </div>
              </v-expand-transition>
            </v-img>
          </v-card>
        </v-hover>
      </v-slide-x-transition>
    </v-col>
  </v-row>
</template>
<script>
export default {
  name: "ImagesList",
  props: {
    images: {
      type: Array,
      default: () => {
        return [];
      },
    },
  },
  computed: {
    Images() {
      return this.images.map((img) => {
        return {
          id: img.id ?? null,
          path: img.path ? img.path : URL.createObjectURL(img),
        };
      });
    },
  },

  methods: {
    show(index) {
      window.open(this.Images[index].path, "_blank");
    },
    remove(index, image) {
      this.$eventBus.$emit("remove-image", { index, image });
    },
  },
};
</script>
<style scoped>
.v-card--reveal {
  align-items: center;
  bottom: 0;
  justify-content: center;
  opacity: 0.5;
  position: absolute;
  width: 100%;
}
</style>
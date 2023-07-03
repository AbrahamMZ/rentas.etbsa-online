<template >
  <v-img
    :src="`https://picsum.photos/500/300?image=${5 + 10}`"
    :lazy-src="`https://picsum.photos/10/6?image=${5 + 10}`"
    aspect-ratio="1"
  />
</template>
<script>
export default {
  props: {
    image: {
      type: Object,
      default: () => {
        return { id: 1 };
      },
    },
  },
};
</script>
<style >
</style>
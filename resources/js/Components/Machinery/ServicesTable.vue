<template>
  <v-data-table :headers="headers" :items="desserts" class="elevation-0" dense>
    <template #[`item.calories`]="{ item }">
      <v-chip :color="getColor(item.calories)" dark small>
        {{ item.calories }}
      </v-chip>
    </template>
    <template #[`item.fat`]="{ value }">
      {{ value | truncate }}
    </template>
    <template #[`item.carbs`]="{ value }">
      {{ value | currency }}
    </template>
    <template #[`item.actions`]>
      <v-btn icon small>
        <v-icon small>mdi-eye</v-icon>
      </v-btn>
      <v-btn icon small>
        <v-icon small color="blue">mdi-pencil</v-icon>
      </v-btn>
      <v-btn icon small>
        <v-icon small color="red">mdi-delete-alert-outline</v-icon>
      </v-btn>
    </template>
  </v-data-table>
</template>

<script>
export default {
  data() {
    return {
      headers: [
        {
          text: "Trabajo",
          align: "start",
          sortable: false,
          value: "name",
        },
        { text: "Estatus", value: "calories" },
        { text: "Descripcion", value: "fat" },
        { text: "Valor", value: "carbs" },
        { text: "Acciones", value: "actions" },
      ],
      desserts: [
        {
          name: "Jelly bean",
          calories: 375,
          fat: "Lorem ipsum dolor sit amet consectetur adipisicing elit. Tenetur ut asperiores odio facere dolore sapiente numquam nihil ullam, nulla ipsam.",
          carbs: 94,
          protein: 0.0,
          iron: 0,
        },
        {
          name: "Lollipop",
          calories: 392,
          fat: "Lorem ipsum dolor sit amet consectetur adipisicing elit. Tenetur ut asperiores odio facere dolore sapiente numquam nihil ullam, nulla ipsam.",
          carbs: 98,
          protein: 0,
          iron: 2,
        },
        {
          name: "Honeycomb",
          calories: 408,
          fat: "Lorem ipsum dolor sit amet consectetur adipisicing elit. Tenetur ut asperiores odio facere dolore sapiente numquam nihil ullam, nulla ipsam.",
          carbs: 87,
          protein: 6.5,
          iron: 45,
        },
        {
          name: "Donut",
          calories: 452,
          fat: "Lorem ipsum dolor sit amet consectetur adipisicing elit. Tenetur ut asperiores odio facere dolore sapiente numquam nihil ullam, nulla ipsam.",
          carbs: 51,
          protein: 4.9,
          iron: 22,
        },
        {
          name: "KitKat",
          calories: 518,
          fat: "Lorem ipsum dolor sit amet consectetur adipisicing elit. Tenetur ut asperiores odio facere dolore sapiente numquam nihil ullam, nulla ipsam.",
          carbs: 65,
          protein: 7,
          iron: 6,
        },
      ],
    };
  },
  methods: {
    getColor(calories) {
      if (calories > 400) return "red";
      else if (calories > 200) return "orange";
      else return "green";
    },
  },
};
</script>
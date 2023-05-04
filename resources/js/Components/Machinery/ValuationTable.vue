<template>
  <v-card flat>
    <v-card-title class="d-flex justify-space-between flex-nowrap">
      <v-currency-field
        label="Gasto Mesual"
        :value="monthlyExpense"
        readonly
        class="pa-2"
        prefix="$"
        outlined
        hide-details
        dense
      />
      <v-currency-field
        :value="LeaseAmount"
        label="Monto de Renta"
        class="pa-2"
        prefix="$"
        readonly
        outlined
        hide-details
        dense
      />
      <!-- <v-text-field
        v-model="residual_percent"
        label="Margen"
        class="pa-2"
        type="number"
        suffix="%"
        outlined
        hide-details
        dense
      /> -->
      <v-text-field
        v-model.number="term"
        label="Plazo (Meses)"
        type="number"
        class="pa-2"
        outlined
        hide-details
        dense
      />
    </v-card-title>
    <v-toolbar flat dark>
      <v-toolbar-title>Tabla de Avaluo</v-toolbar-title>
    </v-toolbar>
    <v-simple-table>
      <template v-slot:default>
        <thead>
          <tr>
            <th class="text-left">#</th>
            <th class="text-left">Gasto Mes</th>
            <th class="text-left">Renta Mes</th>
            <th class="text-left">Utilidad</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="index in term" :key="index">
            <td>{{ index }}</td>
            <td>{{ (index * monthlyExpense) | currency }}</td>
            <td>{{ (index * LeaseAmount) | currency }}</td>
            <td>
              {{ (index * (LeaseAmount - monthlyExpense)) | currency }}
            </td>
          </tr>
        </tbody>
      </template>
    </v-simple-table>
  </v-card>
</template>

<script>
export default {
  name: "ValuationMachinaryTable",
  props: {
    machineryId: {
      type: [Number, String],
      require: true,
    },
    monthlyExpense: {
      type: [Number],
      default: () => {
        return 0;
      },
    },
  },
  data() {
    return {
      // monthy_expense: 60548.88,
      residual_percent: 25,
      term: 12,
    };
  },
  computed: {
    LeaseAmount() {
      return this.monthlyExpense / 0.8;
    },
  },
  mounted() {
    this.getData();
  },
  methods: {
    getData() {
      // eslint-disable-next-line no-console
      console.log("mounted");
    },
  },
};
</script>

<style>
</style>
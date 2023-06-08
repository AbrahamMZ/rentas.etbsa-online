<script>
export default {
  name: "CalculatorValueMAchinery",
  props: {
    params: {
      type: Object,
      require: true,
    },
  },
  data() {
    return {
      percent_margin_cost: 0.85,
      percent_margin_value: 0.85,
      percent_margin_renta: 0.8,
      MonthsUsed: this.params.months_used,
    };
  },
  computed: {
    SalePrice() {
      //   const percent_margin_cost = 0.85;
      const percent_margin_value = 0.85;
      //   const percent_margin_renta = 0.8;
      const limitMonthPercent = 24;
      const MonthsUsed = this.MonthsUsed;

      const TotalExpensesAmount = this.params.TotalAmountExpenses;
      const TotalMonthlyExpensesAmount =
        this.params.TotalAmountMounthlyExpenses;

      //   const Costo_Real = this.cost_price + TotalExpensesAmount;
      const Valor_Actual = this.params.value_price + TotalExpensesAmount;

      //   const Monto_Renta = TotalMonthlyExpensesAmount / percent_margin_renta;
      //   const Valor_Real = Costo_Real / percent_margin_cost;
      const Valor_Comercial = Valor_Actual / percent_margin_value;

      const Gasto_Mensual_Estimado = TotalMonthlyExpensesAmount * MonthsUsed;
      //   const Valor_Real_Estimado = Valor_Real - Gasto_Mensual_Estimado;
      const Valor_Comercial_Estimado = Valor_Comercial - Gasto_Mensual_Estimado;

      let Percent_Valor_Comercial = Valor_Comercial_Estimado / Valor_Actual;
      if (limitMonthPercent < MonthsUsed) {
        Percent_Valor_Comercial += 0.01;
      }

      const Price_Sales = Valor_Actual * Percent_Valor_Comercial;

      return Price_Sales;
    },
  },
};
</script>
<style >
</style>
<template>
  <VCard dark>
    <VCardTitle class="title text-uppercase">
      Calcular Precio de Venta
      <v-spacer />
    </VCardTitle>

    <VCardText>
      <v-container>
        <VRow>
          <v-subheader>Mes a Estimar:</v-subheader>
          <v-text-field
            v-model.number="MonthsUsed"
            outlined
            type="number"
            filled
            suffix="Mes"
          />
        </VRow>
        <VRow justify="center" class="text-h2 font-weight-black">
          {{ SalePrice | currency }}
        </VRow>
      </v-container>
    </VCardText>
  </VCard>
</template>
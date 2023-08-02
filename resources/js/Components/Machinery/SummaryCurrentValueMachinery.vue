<script>
import CalculatorValueMachinery from "./CalculatorValueMachinery.vue";
export default {
  name: "SummaryCurrentValueMachinery",
  components: { CalculatorValueMachinery },
  props: {
    valuePrice: {
      type: Number,
    },
    salePrice: {
      type: Number,
    },
    monthMachinery: {
      type: Number,
      require: true,
    },
    calculatorParams: {
      type: Object,
      require: true,
      default: () => {
        return {
          months_used: 0,
        };
      },
    },
  },
  data() {
    return {
      dialog: false,
      statistics: [
        {
          title: "Valor Comercial:",
          stats: this.salePrice,
          icon: "mdi-credit-card-outline",
          color: "success",
        },
        {
          title: "Adquirido hace:",
          stats: `${this.monthMachinery || 0} Meses`,
          icon: "mdi-calendar-clock",
          color: "primary",
        },
        {
          title: "Renta Mensual Sugerida:",
          stats: this.calculatorParams.TotalAmountMounthlyExpenses / 0.8,
          icon: "mdi-cash-multiple",
          color: "pink",
        },
        {
          title: "Utilidad Renta Optima:",
          stats:
            (this.calculatorParams.TotalAmountMounthlyExpenses / 0.8 -
              this.calculatorParams.TotalAmountMounthlyExpenses) *
            this.monthMachinery,
          icon: "mdi-currency-usd",
          color: "indigo",
        },
        {
          title: "Acumuldado de Renta Optima:",
          stats:
            (this.calculatorParams.TotalAmountMounthlyExpenses / 0.8) *
            this.monthMachinery,
          icon: "mdi-currency-usd",
          color: "yellow",
        },
      ],
    };
  },
  computed: {
    SuggestedMonthlyRent() {
      return this.calculatorParams.TotalAmountMounthlyExpenses / 0.8;
    },
    OptimalIncomeUtility() {
      return this.SuggestedMonthlyRent * this.monthMachinery;
    },
  },
};
</script>

<style>
</style>

<template>
  <VCard dark>
    <VCardTitle class="title text-uppercase">
      Valor Comercial Actual
      <v-spacer />
      <div class="me-n3">
        <v-dialog v-model="dialog" width="500">
          <template v-slot:activator="{ on, attrs }">
            <VBtn icon v-bind="attrs" class="mr-2" v-on="on">
              <VIcon size="24">mdi-calculator</VIcon>
            </VBtn>
          </template>
          <calculator-value-machinery :params="calculatorParams" />
        </v-dialog>
      </div>
    </VCardTitle>
    <v-card-subtitle>
      Precio Actual: $ {{ valuePrice | number("0,0.00") }}
    </v-card-subtitle>

    <VCardText>
      <VRow>
        <VCol v-for="item in statistics" :key="item.title" cols="12" md="6">
          <div class="d-flex align-center">
            <div class="me-3">
              <VAvatar
                :color="item.color"
                rounded
                size="42"
                class="elevation-1"
              >
                <VIcon size="24" dark> {{ item.icon }} </VIcon>
              </VAvatar>
            </div>

            <div class="d-flex flex-column">
              <span class="text-caption">
                {{ item.title }}
              </span>
              <span v-if="typeof item.stats === 'number'" class="text-h6">
                {{ item.stats | currency }}
              </span>
              <span v-else class="text-h6">{{ item.stats }}</span>
            </div>
          </div>
        </VCol>
      </VRow>
    </VCardText>
  </VCard>
</template>

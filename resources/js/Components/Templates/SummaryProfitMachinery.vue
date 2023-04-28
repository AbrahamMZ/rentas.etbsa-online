<script>
export default {
  name: "SummaryStatsMachinery",
  props: {
    statistics: {
      type: Array,
      require: true,
    },
    profit: {
      type: Number,
    },
    balance: {
      type: Number,
    },
  },
  data() {
    return {
      //   statistics: [
      //     {
      //       title: "Depeciacion",
      //       stats: this.stats.monthly_depreciation,
      //       icon: "mdi-trending-down",
      //       color: "primary",
      //     },
      //     {
      //       title: "Gastos",
      //       stats: this.stats.TotalAmountExpenses,
      //       icon: "mdi-credit-card-outline",
      //       color: "success",
      //     },
      //     {
      //       title: "Cargos",
      //       stats: this.stats.TotalAmountSerivcesExpenses,
      //       icon: "mdi-hammer-screwdriver",
      //       color: "warning",
      //     },
      //     {
      //       title: "Valor Actual",
      //       stats: this.stats.TotalCostEquipment,
      //       icon: "mdi-currency-usd",
      //       color: "info",
      //     },
      //   ],
    };
  },
};
</script>

<style>
</style>

<template>
  <VCard>
    <VCardTitle class="title text-uppercase">
      Beneficio Total:
      <span
        class="ml-2"
        :class="[Math.sign(profit) == 1 ? 'green--text' : 'red--text']"
      >
        {{ profit | currency("$", 2, { spaceBetweenAmountAndSymbol: true }) }}
        MXN
      </span>
      <v-spacer />
      <div class="me-n3">
        <VBtn icon>
          <VIcon size="24">mdi-dots-vertical</VIcon>
        </VBtn>
      </div>
    </VCardTitle>
    <v-card-subtitle>
      Ultimo balance total: $ {{ balance | number("0.00 a") }}
    </v-card-subtitle>

    <VCardText>
      <VRow>
        <VCol v-for="item in statistics" :key="item.title" cols="6" sm="3">
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
              <span class="text-h6">{{ item.stats | number("0.00 a") }}</span>
            </div>
          </div>
        </VCol>
      </VRow>
    </VCardText>
  </VCard>
</template>

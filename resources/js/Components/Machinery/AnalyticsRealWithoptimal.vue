<template>
  <VCard>
    <VRow no-gutters>
      <VCol cols="12" md="6">
        <VCardTitle>Rentabilidad Real</VCardTitle>
        <VCardText class="pb-0">
          <VList class="card-list">
            <VListItem
              v-for="deposit in profitability.real"
              :key="deposit.title"
            >
              <v-list-item-avatar>
                <VIcon :height="29" :width="28">
                  {{ deposit.logo }}
                </VIcon>
              </v-list-item-avatar>

              <v-list-item-content class="pa-0">
                <VListItemTitle class="text-sm font-weight-medium mb-1">
                  {{ deposit.title }}
                </VListItemTitle>
                <VListItemSubtitle class="text-xs">
                  {{ deposit.subtitle }}
                </VListItemSubtitle>
              </v-list-item-content>

              <VListItemAction class="green--text font-weight-medium">
                {{ deposit.amount | currency }}
              </VListItemAction>
            </VListItem>
          </VList>
        </VCardText>
      </VCol>

      <VDivider
        :vertical="$vuetify.breakpoint.mdAndUp"
        :inset="$vuetify.breakpoint.mdAndUp"
      />

      <VCol cols="12" md="6">
        <VCardTitle>Rentabilidad Optima</VCardTitle>

        <VCardText class="pb-0">
          <VList class="card-list">
            <VListItem
              v-for="withdraw in profitability.withoptimal"
              :key="withdraw.title"
            >
              <v-list-item-avatar>
                <VIcon :height="29" :width="28">
                  {{ withdraw.logo }}
                </VIcon>
              </v-list-item-avatar>
              <v-list-item-content class="pa-0">
                <VListItemTitle class="text-sm font-weight-medium mb-1">
                  {{ withdraw.title }}
                </VListItemTitle>
                <VListItemSubtitle class="text-xs">
                  {{ withdraw.subtitle }}
                </VListItemSubtitle>
              </v-list-item-content>
              <VListItemAction class="red--text font-weight-medium">
                {{ withdraw.amount | currency }}
              </VListItemAction>
            </VListItem>
          </VList>
        </VCardText>
      </VCol>
    </VRow>
  </VCard>
</template>
<script>
export default {
  props: {
    profitability: {
      type: Object,
    },
  },
  data() {
    return {
      //   real: [
      //     {
      //       title: "Gasto Mensual",
      //       subtitle: "Promedio desde la Adquisicion",
      //       amount: "+$4,650",
      //       logo: "mdi-currency-usd",
      //     },
      //     {
      //       title: "Costo Adquisicion",
      //       subtitle: "Incluye los Gastos Operativos y cargos",
      //       amount: "+$92,705",
      //       logo: "mdi-currency-usd",
      //     },
      //     {
      //       title: "Utilidad",
      //       subtitle: "Por Arrendamientos",
      //       amount: "+$957",
      //       logo: "mdi-currency-usd",
      //     },
      //   ],
      //   withoptimal: [
      //     {
      //       title: "Gasto Mensual",
      //       subtitle: "Proyectado",
      //       amount: "-$145",
      //       logo: "mdi-currency-usd",
      //     },
      //     {
      //       title: "Valor Comercial",
      //       subtitle: "Precio de venta Proyectado",
      //       amount: "-$1870",
      //       logo: "mdi-currency-usd",
      //     },
      //     {
      //       title: "Utilidad",
      //       subtitle: "Proyectado",
      //       amount: "-$450",
      //       logo: "mdi-currency-usd",
      //     },
      //   ],
    };
  },
};
</script>

<style lang="scss" scoped>
.card-list {
  --v-card-list-gap: 0.5rem;
}
</style>

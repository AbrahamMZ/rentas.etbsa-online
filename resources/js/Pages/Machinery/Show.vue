<template>
  <layout>
    <template #breadcrumbs>
      <breadcrumbs :items="breadcrumbs" class="overline" />
    </template>
    <trashed-message v-if="item.deleted_at" class="mb-6" @restore="restore">
      Este Registro fue Eliminado.
    </trashed-message>
    <v-card-text>
      <v-row dense align="start">
        <v-col cols="12" md="4">
          <v-card color="grey lighten-4" min-height="100%">
            <v-card-text class="text-center pa-2">
              <h3 class="text-h4 mb-2">
                {{ item.name }}
              </h3>
              <div class="grey--text mb-2 font-weight-bold">
                N.E. {{ item.economic_serial }}
              </div>
              <div class="blue--text mb-2 font-weight-bold">
                {{ item.category }}
              </div>
            </v-card-text>
            <v-divider />
            <v-row dense class="text-left" tag="v-card-text">
              <v-col class="text-right mr-4" tag="strong" cols="5">
                No. Serie Equipo:
              </v-col>
              <v-col>{{ item.equipment_serial }}</v-col>
              <v-col class="text-right mr-4" tag="strong" cols="5">
                Serie Motor:
              </v-col>
              <v-col>{{ item.engine_serial | placeholder("N/A") }}</v-col>
              <v-col class="text-right mr-4" tag="strong" cols="5">
                Valor de Maquina:
              </v-col>
              <v-col>
                <div>
                  {{
                    item.cost_price
                      | currency("$", 2, { spaceBetweenAmountAndSymbol: true })
                  }}
                  MXN
                </div>
                <div>Factura: {{ item.invoice | placeholder("N/E") }}</div>
              </v-col>
              <v-col class="text-right mr-4" tag="strong" cols="5">
                Costo del Equipo:
              </v-col>
              <v-col>
                {{
                  TotalCostEquipment
                    | currency("$", 2, { spaceBetweenAmountAndSymbol: true })
                }}
                MXN
              </v-col>
              <v-col class="text-right mr-4" tag="strong" cols="5">
                Fecha de Adquisicion:
              </v-col>
              <v-col>
                {{ item.acquisition_date | placeholder("No Especificado") }}
              </v-col>
              <v-col class="text-right mr-4" tag="strong" cols="5">
                Meses de USO:
              </v-col>
              <v-col>
                {{
                  `${item.months_used} Meses` | placeholder("No Especificado")
                }}
              </v-col>
              <v-col class="text-right mr-4" tag="strong" cols="5">
                Depreciacion Mensual:
              </v-col>
              <v-col>
                {{
                  item.monthly_depreciation
                    | currency("$", 2, { spaceBetweenAmountAndSymbol: true })
                }}
                MXN
              </v-col>
              <v-col class="text-right mr-4" tag="strong" cols="5">
                Costo s/Inv. Actual:
              </v-col>
              <v-col>
                {{
                  (item.cost_price -
                    item.monthly_depreciation * item.months_used)
                    | currency("$", 2, { spaceBetweenAmountAndSymbol: true })
                }}
                MXN
              </v-col>
              <v-col class="text-right mr-4" tag="strong" cols="5">
                Costo c/Inv. Actual:
              </v-col>
              <v-col>
                {{
                  (TotalCostEquipment -
                    item.monthly_depreciation * item.months_used)
                    | currency("$", 2, { spaceBetweenAmountAndSymbol: true })
                }}
                MXN
              </v-col>
            </v-row>
            <v-card-actions>
              <v-btn v-if="!item.deleted_at" color="error" @click="destroy">
                Eliminar
              </v-btn>
              <v-spacer />
              <v-btn dark color="blue" @click="edit(item.id)">
                Modificar
              </v-btn>
            </v-card-actions>
          </v-card>
          <!-- <VCard class="mt-4">
            <VCardTitle class="text-h5">
              $482.85k
              <VSpacer />

              <div class="mt-n7 me-n3">
                <VBtn icon size="x-small" color="default" variant="text">
                  <VIcon size="24"> mdi-dots-vertical </VIcon>
                </VBtn>
              </div>
            </VCardTitle>
            <VCardSubtitle>Last month balance $234.40k</VCardSubtitle>

            <VCardText class="pt-2">
              <VList class="card-list" two-line>
                <VListItem
                  v-for="transaction in lastThreeTransactions"
                  :key="transaction.title"
                >
                  <VListItemAvatar tile>
                    <VAvatar :color="transaction.avatar.color" tile>
                      <VIcon dark> {{ transaction.avatar.icon }}</VIcon>
                    </VAvatar>
                  </VListItemAvatar>
                  <VListItemContent>
                    <VListItemTitle class="font-weight-medium">
                      {{ transaction.title }}
                    </VListItemTitle>
                    <VListItemSubtitle>
                      {{ transaction.subtitle }}
                    </VListItemSubtitle>
                  </VListItemContent>
                </VListItem>
              </VList>

              <VBtn block class="mt-6"> View Report </VBtn>
            </VCardText>
          </VCard> -->
        </v-col>
        <v-col cols="12" md="8">
          <v-row>
            <v-col cols="12">
              <summary-stats-machinery
                :statistics="Statistics"
                :percent="item.percent_depreciation"
              />
            </v-col>
            <v-col cols="12">
              <v-card>
                <v-tabs right color="secondary">
                  <v-tab>
                    <v-icon left> mdi-currency-usd </v-icon>
                    Gastos
                  </v-tab>
                  <v-tab>
                    <v-icon left> mdi-toolbox </v-icon>
                    Cargos de Servicio
                  </v-tab>
                  <v-tab>
                    <v-icon left> mdi-car-key </v-icon>
                    Rentas
                  </v-tab>

                  <v-tab-item>
                    <expenses-table
                      :items.sync="item.expenses"
                      :machinery-id="item.id"
                    />
                  </v-tab-item>
                  <v-tab-item>
                    <services-expenses-table
                      :items.sync="item.serivces_expenses"
                      :machinery-id="item.id"
                    />
                  </v-tab-item>
                  <v-tab-item>
                    <leases-machinery-income-table
                      :items.sync="item.leases_incomes"
                      :machinery-id="item.id"
                    />
                  </v-tab-item>
                </v-tabs>
              </v-card>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </v-card-text>
  </layout>
</template>

<script>
import Layout from "@/Shared/Layout";
import TrashedMessage from "@/Shared/TrashedMessage";
import Breadcrumbs from "@/Shared/Breadcrumbs.vue";
import ServicesExpensesTable from "@/Components/ServiceExpenses/ServicesExpensesTable.vue";
import ExpensesTable from "@/Components/Expenses/ExpensesTable.vue";
import SummaryStatsMachinery from "@/Components/Templates/SummaryStatsMachinery.vue";
import LeasesMachineryIncomeTable from "@/Components/Lease/LeasesMachineryIncomeTable.vue";

export default {
  name: "MachineryShow",
  metaInfo: { title: "Detalle Maquinaria" },
  components: {
    TrashedMessage,
    Breadcrumbs,
    Layout,
    ServicesExpensesTable,
    ExpensesTable,
    SummaryStatsMachinery,
    LeasesMachineryIncomeTable,
  },
  props: {
    errors: Object,
    item: Object,
  },
  remember: "form",
  data() {
    return {
      sending: false,
      breadcrumbs: [
        {
          text: "Maquinarias",
          disabled: false,
          href: this.route("machineries"),
          exact: true,
        },
        { text: `${this.item.name}`, disabled: true },
        {
          text: "Detalle",
          disabled: true,
        },
      ],
      lastThreeTransactions: [
        {
          avatar: {
            icon: "mdi-trending-up",
            color: "success",
          },
          title: "$48,568.20",
          subtitle: "Total Profit",
        },
        {
          avatar: {
            icon: "mdi-account-outline",
            color: "primary",
          },
          title: "$38,453.25",
          subtitle: "Total Income",
        },
        {
          avatar: {
            icon: "mdi-currency-usd",
            color: "secondary",
          },
          title: "$2,453.45",
          subtitle: "Total Expense",
        },
      ],
    };
  },
  computed: {
    TotalAmountExpenses() {
      return this.item.expenses.reduce((acc, curr) => acc + curr.amount, 0);
    },
    TotalAmountSerivcesExpenses() {
      return this.item.serivces_expenses.reduce(
        (acc, curr) => acc + curr.amount,
        0
      );
    },
    TotalCostEquipment() {
      return (
        this.TotalAmountExpenses +
        this.TotalAmountSerivcesExpenses +
        Number(this.item.cost_price)
      );
    },
    Statistics() {
      return [
        {
          title: "Depreciacion",
          stats: this.item.monthly_depreciation,
          icon: "mdi-trending-down",
          color: "primary",
        },
        {
          title: "Gastos",
          stats: this.TotalAmountExpenses,
          icon: "mdi-credit-card-outline",
          color: "success",
        },
        {
          title: "Cargos Internos",
          stats: this.TotalAmountSerivcesExpenses,
          icon: "mdi-hammer-screwdriver",
          color: "warning",
        },
        {
          title: "Valor Actual",
          stats:
            this.TotalCostEquipment -
            this.item.monthly_depreciation * this.item.months_used,
          icon: "mdi-currency-usd",
          color: "info",
        },
      ];
    },
  },
  methods: {
    destroy() {
      if (confirm("Desea Eliminar la Maquinaria?")) {
        this.$inertia.delete(this.route("machineries.destroy", this.item.id));
      }
    },
    restore() {
      if (confirm("Desea Restaurar la Maquinaria?")) {
        this.$inertia.put(this.route("machineries.restore", this.item.id));
      }
    },
    edit(_item_id) {
      this.$inertia.visit(this.route("machineries.edit", _item_id));
    },
    // async updateMachineryFixesCosts(items) {

    //   let payload = {
    //     fixes_costs: items,
    //   };
    //   await this.$inertia.put(
    //     this.route("machineries.updateMachineryFixesCosts", this.item.id),
    //     payload,
    //     {
    //       onStart: () => (this.sending = true),
    //       onFinish: () => this.$inertia.reload({ only: ["item.fixes_costs"] }),
    //     }
    //   );
    // },
  },
};
</script>

<style lang="scss">
.card-list {
  --v-card-list-gap: 20px;

  &.v-list {
    padding-block: 0;
  }

  .v-list-item {
    min-block-size: unset;
    min-block-size: auto !important;
    padding-block: 0 !important;
    padding-inline: 0 !important;

    > .v-ripple__container {
      opacity: 0;
    }

    &:not(:last-child) {
      padding-block-end: var(--v-card-list-gap) !important;
    }
  }

  .v-list-item:hover,
  .v-list-item:focus,
  .v-list-item:active,
  .v-list-item.active {
    > .v-list-item__overlay {
      opacity: 0 !important;
    }
  }
}
</style>

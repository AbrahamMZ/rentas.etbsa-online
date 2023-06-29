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
          <v-row>
            <v-col cols="12">
              <VCard class="mb-4">
                <v-card-title class="title text-uppercase">
                  Resumen
                  <v-spacer />
                  <v-menu transition="slide-x-transition" bottom right offset-x>
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn icon v-bind="attrs" v-on="on">
                        <v-icon size="24">mdi-settings</v-icon>
                      </v-btn>
                    </template>

                    <v-list shaped>
                      <v-list-item @click="edit(item.id)">
                        <v-list-item-title> Editar </v-list-item-title>
                      </v-list-item>
                      <v-list-item @click="openDialog('ValuationTable')">
                        <v-list-item-title>Proyeccion Renta </v-list-item-title>
                      </v-list-item>
                      <v-list-item @click="destroy(item.id)">
                        <v-list-item-title> Eliminar </v-list-item-title>
                      </v-list-item>
                      <v-list-item
                        v-if="item.deleted_at"
                        @click="restore(item.id)"
                      >
                        <v-list-item-title> Recuperar </v-list-item-title>
                      </v-list-item>
                    </v-list>
                  </v-menu>
                </v-card-title>
                <VCardText>
                  <VList
                    class="card-list text-medium-emphasis text-secondary"
                    dense
                  >
                    <VListItem>
                      <VListItemIcon>
                        <VIcon size="24">mdi-currency-usd</VIcon>
                      </VListItemIcon>
                      <VListItemContent>
                        <VListItemTitle class="align-center">
                          <span class="font-weight-medium me-1 text-uppercase">
                            Costo del Equipo:
                          </span>
                        </VListItemTitle>
                      </VListItemContent>
                      <VListItemActionText>
                        <span class="title">
                          {{ item.cost_price | currency }}
                        </span>
                      </VListItemActionText>
                    </VListItem>
                    <VListItem>
                      <VListItemIcon>
                        <VIcon size="24">mdi-currency-usd </VIcon>
                      </VListItemIcon>
                      <VListItemContent>
                        <VListItemTitle class="align-center">
                          <span class="font-weight-medium me-1 text-uppercase">
                            Costo Total:
                          </span>
                        </VListItemTitle>
                      </VListItemContent>
                      <VListItemActionText>
                        <span class="title">
                          {{ item.total_cost_amount | currency }}
                        </span>
                      </VListItemActionText>
                    </VListItem>
                    <VListItem>
                      <VListItemIcon>
                        <VIcon size="24" left>mdi-currency-usd </VIcon>
                      </VListItemIcon>
                      <VListItemContent>
                        <VListItemTitle class="align-center">
                          <span class="font-weight-medium me-1 text-uppercase">
                            Utilidad Total Rentas:
                          </span>
                        </VListItemTitle>
                      </VListItemContent>
                      <VListItemActionText>
                        <span class="title">
                          {{ TotalBalanceIncome | currency }}
                        </span>
                      </VListItemActionText>
                    </VListItem>
                    <VListItem>
                      <VListItemIcon>
                        <VIcon size="24" left>mdi-currency-usd-off </VIcon>
                      </VListItemIcon>
                      <VListItemContent>
                        <VListItemTitle class="align-center">
                          <span class="font-weight-medium me-1 text-uppercase">
                            Gasto Mensual:
                          </span>
                        </VListItemTitle>
                      </VListItemContent>
                      <VListItemActionText>
                        <span class="title">
                          {{ TotalAmountMounthlyExpenses | currency }}
                        </span>
                      </VListItemActionText>
                    </VListItem>
                    <VListItem>
                      <VListItemIcon>
                        <VIcon size="24" left>mdi-currency-usd-off </VIcon>
                      </VListItemIcon>
                      <VListItemContent>
                        <VListItemTitle class="align-center">
                          <span class="font-weight-medium me-1 text-uppercase">
                            Gasto Inical/Fijo:
                          </span>
                        </VListItemTitle>
                      </VListItemContent>
                      <VListItemActionText>
                        <span class="title">
                          {{ TotalAmountExpenses | currency }}
                        </span>
                      </VListItemActionText>
                    </VListItem>
                    <VListItem>
                      <VListItemIcon>
                        <VIcon size="24" left>mdi-currency-usd-off </VIcon>
                      </VListItemIcon>
                      <VListItemContent>
                        <VListItemTitle class="align-center">
                          <span class="font-weight-medium me-1 text-uppercase">
                            Cargos de Servicio:
                          </span>
                        </VListItemTitle>
                      </VListItemContent>
                      <VListItemActionText>
                        <span class="title">
                          {{ TotalAmountSerivcesExpenses | currency }}
                        </span>
                      </VListItemActionText>
                    </VListItem>
                  </VList>
                </VCardText>
              </VCard>
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="12">
              <summary-current-value-machinery
                :value-price="item.value_price"
                :sale-price="item.current_sale_price"
                :month-machinery="item.months_used"
                :calculator-params="ParamsToCalculateValue"
              />
            </v-col>
          </v-row>
        </v-col>
        <v-col cols="12" md="8">
          <v-row>
            <v-col cols="12">
              <v-card>
                <v-tabs right color="secondary">
                  <v-tab>
                    <v-icon left> mdi-currency-usd </v-icon>
                    Gasto Mensual
                  </v-tab>
                  <v-tab>
                    <v-icon left> mdi-currency-usd </v-icon>
                    Gasto Fijo Inicial
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
                    <monthly-expenses-table
                      :items.sync="item.monthly_expenses"
                      :machinery-id="item.id"
                      :machinery-invoice-amount.sync="item.cost_price"
                      :machinery-total-cost.sync="item.total_cost_amount"
                    />
                  </v-tab-item>
                  <v-tab-item>
                    <expenses-table
                      :items.sync="item.expenses"
                      :machinery-id="item.id"
                    />
                  </v-tab-item>
                  <v-tab-item>
                    <services-expenses-table
                      :items.sync="item.services_expenses"
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
          <!-- <v-row>
            <v-col cols="12">
              <summary-stats-machinery
                :statistics="Statistics"
                :percent="item.percent_depreciation"
              />
            </v-col>
            <v-col cols="12">
              <summary-profit-machinery
                class="mt-2"
                :statistics="Profit"
                :profit="TotalProfit"
                :balance="TotalBalanceIncome"
              />
            </v-col>
          </v-row> -->
        </v-col>
      </v-row>
    </v-card-text>

    <!-- Dialogs -->
    <dialog-modal
      :show="dialog"
      max-width="750"
      persistent
      @close="closeDialog"
    >
      <template #title> Proyeccion de Renta </template>
      <template #content>
        <component
          :is="'ValuationTable'"
          v-bind="{
            'machinery-id': item.id,
            monthlyExpense: TotalAmountMounthlyExpenses,
          }"
        />
      </template>
    </dialog-modal>
  </layout>
</template>

<script>
import Layout from "@/Shared/Layout";
import TrashedMessage from "@/Shared/TrashedMessage";
import Breadcrumbs from "@/Shared/Breadcrumbs.vue";
import ServicesExpensesTable from "@/Components/ServiceExpenses/ServicesExpensesTable.vue";
import ExpensesTable from "@/Components/Expenses/ExpensesTable.vue";
import LeasesMachineryIncomeTable from "@/Components/Lease/LeasesMachineryIncomeTable.vue";
import MonthlyExpensesTable from "@/Components/Expenses/MonthlyExpensesTable.vue";
import ValuationTable from "@/Components/Machinery/ValuationTable.vue";
import DialogModal from "@/Shared/DialogModal.vue";
import SummaryCurrentValueMachinery from "@/Components/Machinery/SummaryCurrentValueMachinery.vue";

export default {
  name: "MachineryShow",
  metaInfo: { title: "Detalle Maquinaria" },
  components: {
    TrashedMessage,
    Breadcrumbs,
    Layout,
    ServicesExpensesTable,
    ExpensesTable,
    LeasesMachineryIncomeTable,
    MonthlyExpensesTable,
    ValuationTable,
    DialogModal,
    SummaryCurrentValueMachinery,
  },
  props: {
    errors: Object,
    item: Object,
  },
  remember: "form",
  data() {
    return {
      dialog: false,
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
    TotalAmountMounthlyExpenses() {
      return this.item.monthly_expenses.reduce(
        (acc, curr) => acc + curr.amount,
        0
      );
    },
    TotalAmountSerivcesExpenses() {
      return this.item.services_expenses.reduce(
        (acc, curr) => acc + curr.amount,
        0
      );
    },
    TotalIncome() {
      return this.item.leases_incomes.reduce(
        (acc, curr) => acc + curr.total_income,
        0
      );
    },
    TotalBalanceIncome() {
      return this.item.leases_incomes.reduce(
        (acc, curr) => acc + curr.balance,
        0
      );
    },
    TotalProfit() {
      return this.TotalIncome - this.TotalCostEquipment;
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
    Profit() {
      return [
        {
          title: "total Beneficio",
          stats: this.TotalIncome,
          icon: "mdi-trending-up",
          color: "primary",
        },
        {
          title: "Total Gastos",
          stats: this.TotalCostEquipment,
          icon: "mdi-currency-usd",
          color: "error",
        },
        {
          title: "Total Ingresos",
          stats: this.TotalBalanceIncome,
          icon: "mdi-account-outline",
          color: "success",
        },
      ];
    },
    ParamsToCalculateValue() {
      const { cost_price, value_price, months_used } = this.item;
      return {
        months_used,
        cost_price,
        value_price,
        TotalAmountMounthlyExpenses: this.TotalAmountMounthlyExpenses,
        TotalAmountExpenses: this.TotalAmountExpenses,
      };
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
    openDialog() {
      this.dialog = true;
    },
    closeDialog() {
      this.dialog = false;
    },
  },
};
</script>

<style lang="scss" scoped>
.card-list {
  --v-card-list-gap: 16px;
}
</style>


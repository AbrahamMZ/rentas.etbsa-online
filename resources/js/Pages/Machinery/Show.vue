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
                <v-carousel
                  height="275px"
                  hide-delimiter-background
                  hide-delimiters
                  show-arrows-on-hover
                >
                  <v-carousel-item v-for="(photo, i) in Images" :key="i">
                    <v-img
                      v-if="photo.path"
                      height="275px"
                      :src="photo.path"
                      :lazy-src="`https://picsum.photos/10/6?image=${
                        i * 5 + 10
                      }`"
                      :alt="photo.path"
                      aspect-ratio="2"
                    />
                  </v-carousel-item>
                </v-carousel>

                <v-card-title>
                  {{ item.name }}
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
                <v-card-subtitle>
                  No. Serie: {{ item.equipment_serial }}
                </v-card-subtitle>
                <VCardText>
                  <VList class="card-list pa-0">
                    <VListItem class="pa-0">
                      <v-list-item-avatar>
                        <VIcon :height="29" :width="28">
                          mdi-currency-usd
                        </VIcon>
                      </v-list-item-avatar>

                      <v-list-item-content class="pa-0">
                        <VListItemTitle class="text-sm font-weight-medium mb-1">
                          Costo Equipo
                        </VListItemTitle>
                        <VListItemSubtitle class="text-xs">
                          Valor de Adquisicon
                        </VListItemSubtitle>
                      </v-list-item-content>

                      <VListItemAction class="green--text font-weight-medium">
                        {{ item.cost_price | currency }}
                      </VListItemAction>
                    </VListItem>
                    <VListItem class="pa-0">
                      <v-list-item-avatar>
                        <VIcon :height="29" :width="28">
                          mdi-currency-usd
                        </VIcon>
                      </v-list-item-avatar>

                      <v-list-item-content class="pa-0">
                        <VListItemTitle class="text-sm font-weight-medium mb-1">
                          Costo Actual del Equipo
                        </VListItemTitle>
                        <VListItemSubtitle class="text-xs">
                          Valor Actual
                        </VListItemSubtitle>
                      </v-list-item-content>

                      <VListItemAction class="green--text font-weight-medium">
                        {{ item.value_price | currency }}
                      </VListItemAction>
                    </VListItem>
                    <VListItem class="pa-0">
                      <v-list-item-avatar>
                        <VIcon :height="29" :width="28">
                          mdi-calendar-clock
                        </VIcon>
                      </v-list-item-avatar>

                      <v-list-item-content class="pa-0">
                        <VListItemTitle class="text-sm font-weight-medium mb-1">
                          Fecha de Adquisicion
                        </VListItemTitle>
                        <VListItemSubtitle class="text-xs">
                          {{ item.acquisition_date }}
                        </VListItemSubtitle>
                      </v-list-item-content>

                      <VListItemAction class="green--text font-weight-medium">
                        {{ item.months_used }} Meses
                      </VListItemAction>
                    </VListItem>
                  </VList>
                </VCardText>
                <!-- <VCardText>
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
                </VCardText> -->
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
              <analytics-real-withoptimal :profitability="Profitability" />
            </v-col>
            <v-col cols="12">
              <v-card>
                <v-tabs fixed-tabs icons-and-text centered color="indigo">
                  <v-tab>
                    Gasto Mensual Proyectado
                    <v-icon left> mdi-currency-usd </v-icon>
                  </v-tab>
                  <v-tab>
                    Gastos Operativos
                    <v-icon left> mdi-currency-usd </v-icon>
                  </v-tab>
                  <v-tab>
                    Cargos de Servicio
                    <v-icon left> mdi-toolbox </v-icon>
                  </v-tab>
                  <v-tab>
                    Rentas
                    <v-icon left> mdi-car-key </v-icon>
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
          <v-row>
            <!-- <v-col cols="12">
              <summary-stats-machinery
                :statistics="Statistics"
                :percent="item.percent_depreciation"
              />
            </v-col> -->
            <v-col cols="12">
              <summary-profit-machinery
                class="mt-2"
                :statistics="Profit"
                :profit="TotalProfit"
                :balance="TotalBalanceIncome"
              />
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </v-card-text>

    <!-- Dialogs -->
    <dialog-modal
      :show="dialog"
      max-width="700"
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
import AnalyticsRealWithoptimal from "@/Components/Machinery/AnalyticsRealWithoptimal.vue";
import SummaryProfitMachinery from "@/Components/Machinery/SummaryProfitMachinery.vue";
// import SummaryStatsMachinery from "@/Components/Machinery/SummaryStatsMachinery.vue";

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
    AnalyticsRealWithoptimal,
    SummaryProfitMachinery,
    // SummaryStatsMachinery,
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
    };
  },
  computed: {
    Images() {
      return this.item.images.length > 0
        ? this.item.images
        : [{ path: `https://picsum.photos/10/6?image=${5 + 10}` }];
    },
    NameMachinery() {
      return `${this.item.category} ${this.item.name}`;
    },
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
      return this.item.leases_incomes.reduce((acumulado, objeto) => {
          if (objeto.lease_fees && Array.isArray(objeto.lease_fees)) {
            const amountIncomeAcumulado = objeto.lease_fees.reduce(
              (subtotal, leaseFee) => subtotal + leaseFee.amount_income,
              0
            );
            return acumulado + amountIncomeAcumulado;
          }
          return acumulado;
        }, 0);
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
    Profitability() {
      let month_used = this.item.months_used <= 1 ? 1 : this.item.months_used;
      let expenses_total =
        this.TotalAmountExpenses + this.TotalAmountSerivcesExpenses;
      return {
        real: [
          {
            title: "Gasto Mensual",
            subtitle: "Promedio desde la Adquisicion",
            amount: expenses_total / month_used,
            logo: "mdi-currency-usd",
          },
          {
            title: "Costo Adquisicion",
            subtitle: "+ Gastos",
            amount: this.TotalCostEquipment,
            logo: "mdi-currency-usd",
          },
          {
            title: "Utilidad",
            subtitle: `Ingreso: ${this.$options.filters.currency(
              this.TotalBalanceIncome
            )}`,
            // amount: this.TotalIncome - expenses_total,
            amount: this.TotalBalanceIncome - expenses_total,
            logo: "mdi-currency-usd",
          },
        ],
        withoptimal: [
          {
            title: "Gasto Mensual",
            subtitle: "Proyectado",
            amount: this.TotalAmountMounthlyExpenses,
            logo: "mdi-currency-usd",
          },
          {
            title: "Valor Comercial",
            subtitle: "Precio de venta Proyectado",
            amount: this.item.current_sale_price,
            logo: "mdi-currency-usd",
          },
          {
            title: "Utilidad",
            subtitle: "Proyectado",
            amount:
              (this.TotalAmountMounthlyExpenses / 0.8 -
                this.TotalAmountMounthlyExpenses) *
              month_used,
            logo: "mdi-currency-usd",
          },
        ],
      };
    },
    Profit() {
      return [
        {
          title: "Ingreso Estimado",
          stats: this.$options.filters.number(this.TotalIncome, "0.00 a"),
          icon: "mdi-trending-up",
          color: "primary",
        },
        {
          title: "Gastos Total",
          stats: this.$options.filters.number(
            this.TotalCostEquipment,
            "0.00 a"
          ),
          icon: "mdi-trending-down",
          color: "error",
        },
        {
          title: "Tasa de Ocupacion",
          stats: `${this.$options.filters.percent(this.item.occupancy_rate)}`,
          icon: "mdi-percent",
          color: "grey",
        },
        // {
        //   title: "Total Ingresos",
        //   stats: this.TotalBalanceIncome,
        //   icon: "mdi-account-outline",
        //   color: "success",
        // },
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

.text-overlay {
  position: absolute;
  top: 200px; /* Ajusta la posición vertical según tus necesidades */
  left: 100px; /* Ajusta la posición horizontal según tus necesidades */
  transform: translateX(-50%);
  z-index: 1;
}

.text {
  color: white;
  font-size: 24px;
  font-weight: bold;
}
</style>


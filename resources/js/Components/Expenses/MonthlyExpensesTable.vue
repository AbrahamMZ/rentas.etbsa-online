<template>
  <v-card>
    <v-data-table
      :headers="headers"
      :items="monthly_expenses"
      class="elevation-0 text-uppercase"
      disable-pagination
      hide-default-footer
      mobile-breakpoint="0"
      item-key="id"
    >
      <template v-slot:top>
        <v-toolbar flat>
          <v-toolbar-title>Gastos Mensuales</v-toolbar-title>
          <v-divider class="mx-4" inset vertical />
          <v-spacer />
          <v-dialog v-model="dialog" max-width="500px">
            <template v-slot:activator="{ on, attrs }">
              <v-btn color="primary" dark class="mb-2" v-bind="attrs" v-on="on">
                Agregar Nuevo Gasto Mensual
              </v-btn>
            </template>
            <v-card>
              <v-card-title>
                <span class="text-h5">{{ formTitle }}</span>
              </v-card-title>

              <v-card-text>
                <v-container>
                  <v-form
                    ref="formMonthlyExpense"
                    v-model="valid"
                    lazy-validation
                  >
                    <v-row>
                      <v-col cols="12">
                        <v-select
                          v-model="editedItem.expense"
                          label="Tipo de  Gasto"
                          :items="options.expense_options"
                          item-text="name"
                          item-value="id"
                          :rules="[(v) => !!v.id || 'Requerido']"
                          return-object
                          outlined
                          dense
                        />
                      </v-col>
                      <v-col cols="12">
                        <v-text-field
                          v-model="editedItem.description"
                          label="Descripcion Gasto Mensual"
                          placeholder="Emisor de Factura"
                          :rules="[(v) => !!v || 'Requerido']"
                          counter="50"
                          outlined
                          dense
                        />
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-select
                          v-model="editedItem.base_cost_type"
                          label="Valor/Costo Base"
                          :items="options.base_cost_types"
                          outlined
                          dense
                        >
                          <template #item="{ item }">
                            <v-list-item-content>
                              <v-list-item-title>
                                {{ item.text }}:
                              </v-list-item-title>
                              <v-list-item-action-text>
                                {{
                                  BaseCostConfig[item.value].amount | currency
                                }}
                              </v-list-item-action-text>
                            </v-list-item-content>
                          </template>
                        </v-select>
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-currency-field
                          v-model.number="BaseCost"
                          label="Monto Base Config"
                          type="number"
                          readonly
                          prefix="$"
                          suffix="MXN"
                          outlined
                          dense
                        />
                      </v-col>
                      <v-col cols="12" md="4">
                        <v-text-field
                          v-model.number="editedItem.percent"
                          label="Porcentaje"
                          type="number"
                          suffix="%"
                          hint="0% al 100%"
                          persistent-hint
                          :disabled="!HasBaseCostType"
                          outlined
                          dense
                        />
                      </v-col>
                      <v-col cols="12" md="8">
                        <v-currency-field
                          v-model.number="editedItem.amount"
                          label="Importe del Gasto"
                          :rules="[(v) => !!v || 'Requerido']"
                          type="number"
                          :readonly="HasBaseCostType"
                          prefix="$"
                          suffix="MXN"
                          outlined
                          dense
                        />
                      </v-col>
                    </v-row>
                  </v-form>
                </v-container>
              </v-card-text>

              <v-card-actions>
                <v-spacer />
                <v-btn color="blue darken-1" text @click="close">
                  Cancel
                </v-btn>
                <v-btn
                  color="blue darken-1"
                  text
                  :loading="sending"
                  :disabled="sending"
                  @click="save"
                >
                  Save
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>
          <v-dialog v-model="dialogDelete" max-width="500px">
            <v-card>
              <v-card-title class="text-h5">
                Are you sure you want to delete this item?
              </v-card-title>
              <v-card-actions>
                <v-spacer />
                <v-btn color="blue darken-1" text @click="closeDelete">
                  Cancel
                </v-btn>
                <v-btn color="blue darken-1" text @click="deleteItemConfirm">
                  OK
                </v-btn>
                <v-spacer />
              </v-card-actions>
            </v-card>
          </v-dialog>
        </v-toolbar>
      </template>
      <template #[`item.name`]="{ item }">
        <div class="body-1">{{ item.description }}</div>
        <div class="body-2 grey--text">{{ item.expense.name }}</div>
      </template>
      <template #[`item.base_cost_amount`]="{ value }">
        {{ showBaseCost(value) }}
      </template>
      <template #[`item.percent`]="{ value }">
        {{ showPercent(value) }}
      </template>
      <template #[`item.amount`]="{ value }">
        {{ value | currency("$", 2, { spaceBetweenAmountAndSymbol: true }) }}
        MXN
      </template>
      <template #[`item.actions`]="{ item }">
        <v-icon small class="mr-2" @click="editItem(item)"> mdi-pencil </v-icon>
        <v-icon small @click="deleteItem(item)"> mdi-delete </v-icon>
      </template>
      <template #foot>
        <tfoot>
          <tr>
            <td class="text-right overline" :colspan="headers.length - 2">
              Total:
            </td>
            <td colspan="2" class="red--text title">
              {{
                TotalAmountexpenses
                  | currency("$", 2, { spaceBetweenAmountAndSymbol: true })
              }}
              MXN
            </td>
          </tr>
        </tfoot>
      </template>
      <template v-slot:no-data>
        <div class="text-h4 ma-3">Sin Registros</div>
      </template>
    </v-data-table>
  </v-card>
</template>
<script>
// import { router } from "@inertiajs/vue2";
import axios from "axios";
export default {
  name: "MonthlyExpensesTable",
  props: {
    items: {
      type: Array,
      require: false,
    },
    machineryId: {
      type: [Number, String],
      require: false,
    },
    machineryInvoiceAmount: {
      type: [Number, String],
      require: false,
      default: 0,
    },
    machineryTotalCost: {
      type: [Number, String],
      require: false,
      default: 0,
    },
  },
  data: () => ({
    valid: true,
    dialog: false,
    sending: false,
    dialogDelete: false,
    headers: [
      {
        text: "Gasto Mensual",
        align: "start",
        value: "name",
      },
      { text: "Costo Base:", value: "base_cost_amount" },
      { text: "Porcentaje:", value: "percent" },
      { text: "Importe", value: "amount" },
      { text: "Actions", value: "actions", sortable: false },
    ],
    monthly_expenses: [],
    editedIndex: -1,
    options: {
      expense_options: [],
    },
    editedItem: {
      id: null,
      expense: {
        id: null,
      },
      monthly_expense_types_id: "",
      description: "",
      base_cost_type: "none",
      base_cost_amount: 0,
      percent: 0,
      amount: 0,
    },
    defaultItem: {
      id: null,
      expense: {
        id: null,
      },
      monthly_expense_types_id: "",
      description: "",
      base_cost_type: "none",
      base_cost_amount: 0,
      percent: 0,
      amount: 0,
    },
    // base_cost_config: {
    //   none: { amount: 0 },
    // },
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1
        ? "Agregar Gasto Mensual"
        : "Editar Gasto Mensual";
    },
    TotalAmountexpenses() {
      return this.items.reduce((acc, curr) => acc + curr.amount, 0);
    },
    HasBaseCostType() {
      return ["invoice", "total_cost"].includes(this.editedItem.base_cost_type);
    },
    BaseCost() {
      return this.BaseCostConfig[this.editedItem.base_cost_type].amount;
    },
    BaseCostConfig() {
      return {
        none: { amount: 0 },
        invoice: { amount: this.machineryInvoiceAmount },
        total_cost: { amount: this.machineryTotalCost },
      };
    },
  },

  watch: {
    dialog(val) {
      val || this.close();
    },
    dialogDelete(val) {
      val || this.closeDelete();
    },
    ["editedItem.percent"](val) {
      if (val > 0) {
        this.editedItem.amount = this.BaseCost * (val / 100);
      }
    },
  },

  mounted() {
    this.initialize();
    this.getFormOptions();
  },

  methods: {
    initialize() {
      this.monthly_expenses = this.items;
      // this.BaseCostConfig = {
      //   none: { amount: 0 },
      //   invoice: { amount: this.machineryInvoiceAmount },
      //   total_cost: { amount: this.machineryTotalCost },
      // };
    },
    async getFormOptions() {
      const { data } = await axios.get(this.route("expenses.options"));
      this.options = data;
      this.options.base_cost_types = [
        { text: "Fijo", value: "none" },
        { text: "Valor Factura", value: "invoice" },
        { text: "Costo Total", value: "total_cost" },
      ];
    },
    editItem(item) {
      this.editedIndex = this.monthly_expenses.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
    },

    deleteItem(item) {
      this.editedIndex = this.monthly_expenses.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialogDelete = true;
    },

    deleteItemConfirm() {
      this.machineryId
        ? this.destroy()
        : this.monthly_expenses.splice(this.editedIndex, 1);
      this.closeDelete();
    },

    close() {
      this.dialog = false;
      // this.$refs.formMonthlyExpense.reset();
      this.$nextTick(() => {
        this.$refs.formMonthlyExpense.resetValidation();
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },

    closeDelete() {
      this.dialogDelete = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },

    save() {
      // eslint-disable-next-line no-console
      console.log("Click SAVE");
      if (!this.$refs.formMonthlyExpense.validate()) return;
      // eslint-disable-next-line no-console
      console.log("Pass Validate form");
      if (this.editedIndex > -1) {
        // eslint-disable-next-line no-console
        console.log("Edited");
        this.editedItem.id
          ? this.update()
          : Object.assign(this.monthly_expenses[this.editedIndex], {
              ...this.editedItem,
              monthly_expense_types_id: this.editedItem.expense.id,
            });
      } else {
        // eslint-disable-next-line no-console
        console.log("Stored");
        this.machineryId
          ? this.store()
          : this.monthly_expenses.push({
              ...this.editedItem,
              monthly_expense_types_id: this.editedItem.expense.id,
            });
      }
      this.close();
    },

    async store() {
      const _this = this;
      let payload = {
        ..._this.editedItem,
        monthly_expense_types_id: _this.editedItem.expense.id,
        machinery_id: _this.machineryId,
        base_cost_amount: _this.BaseCost,
      };
      // eslint-disable-next-line no-console
      console.log("store Payload", payload);
      await _this.$inertia.post(
        _this.route("machinery-monthly-expenses.store"),
        payload,
        {
          onStart: () => (_this.sending = true),
          onFinish: () => ((_this.sending = false), _this.initialize()),
          only: ["item", "flash", "errors"],
          preserveState: true,
        }
      );
    },
    async update() {
      const _this = this;
      let payload = {
        ..._this.editedItem,
        monthly_expense_types_id: _this.editedItem.expense.id,
        machinery_id: _this.machineryId,
        base_cost_amount: _this.BaseCost,
      };
      await _this.$inertia.put(
        _this.route("machinery-monthly-expenses.update", _this.editedItem.id),
        payload,
        {
          onStart: () => (_this.sending = true),
          onFinish: () => ((_this.sending = false), _this.initialize()),
          only: ["item", "flash", "errors"],
          preserveState: true,
        }
      );
    },
    destroy() {
      const _this = this;
      _this.$inertia.delete(
        _this.route(
          "machinery-monthly-expenses.destroy",
          _this.editedItem.id
        ),
        {
          onFinish: () => ((_this.sending = false), _this.initialize()),
          only: ["item", "flash", "errors"],
          preserveState: true,
        }
      );
    },
    showBaseCost(value) {
      return value > 0
        ? `${this.$options.filters.currency(value, "$", 2, {
            spaceBetweenAmountAndSymbol: true,
          })} MXN`
        : "Valor Fijo";
    },
    showPercent(value) {
      return value > 0 ? this.$options.filters.percent(value / 100, 5) : "N/A";
    },
  },
};
</script>
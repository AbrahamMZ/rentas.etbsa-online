<template>
  <v-card>
    <v-data-table
      :headers="headers"
      :items="expenses"
      class="elevation-0 text-uppercase"
      disable-pagination
      hide-default-footer
      mobile-breakpoint="0"
      item-key="id"
    >
      <template v-slot:top>
        <v-toolbar flat>
          <v-toolbar-title>Gastos</v-toolbar-title>
          <v-divider class="mx-4" inset vertical />
          <v-spacer />
          <v-dialog v-model="dialog" max-width="500px">
            <template v-slot:activator="{ on, attrs }">
              <v-btn color="primary" dark class="mb-2" v-bind="attrs" v-on="on">
                Agregar Nuevo Gasto
              </v-btn>
            </template>
            <v-card>
              <v-card-title>
                <span class="text-h5">{{ formTitle }}</span>
              </v-card-title>

              <v-card-text>
                <v-container>
                  <v-form ref="formExpense" v-model="valid" lazy-validation>
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
                          v-model="editedItem.reference"
                          label="Nombre o Referencia del Gasto"
                          placeholder="Emisor de Factura"
                          :rules="[(v) => !!v || 'Requerido']"
                          counter="50"
                          outlined
                          dense
                        />
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-text-field
                          v-model="editedItem.folio"
                          label="Folio Factura"
                          :rules="[(v) => !!v || 'Requerido']"
                          outlined
                          dense
                        />
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-text-field
                          v-model.number="editedItem.amount"
                          label="Importe"
                          :rules="[(v) => !!v || 'Requerido']"
                          type="number"
                          prefix="$"
                          suffix="MXN"
                          outlined
                          dense
                        />
                      </v-col>
                      <v-col cols="12">
                        <v-text-field
                          v-model="editedItem.applied_date"
                          label="Fecha de Aplicacion"
                          :rules="[(v) => !!v || 'Requerido']"
                          type="date"
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
      <template #[`item.reference`]="{ item }">
        <div class="subtitle-2">{{ item.reference }}</div>
        <div class="caption">{{ item.folio }}</div>
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
  name: "MachineryExpenseTable",
  props: {
    items: {
      type: Array,
      require: false,
    },
    machineryId: {
      type: [Number, String],
      require: false,
    },
  },
  data: () => ({
    valid: true,
    dialog: false,
    sending: false,
    dialogDelete: false,
    headers: [
      {
        text: "Gasto",
        align: "start",
        value: "expense.name",
      },
      { text: "Referencia", value: "reference", divider: true },
      { text: "Importe", value: "amount" },
      { text: "Actions", value: "actions", sortable: false },
    ],
    expenses: [],
    editedIndex: -1,
    options: {
      expense_options: [],
    },
    editedItem: {
      id: null,
      expense: {
        id: null,
      },
      expense_id: "",
      reference: "",
      folio: "",
      amount: 0,
      applied_date: null,
      // machinery_id: null,
    },
    defaultItem: {
      id: null,
      expense: {
        id: null,
      },
      expense_id: "",
      reference: "",
      folio: "",
      amount: 0,
      applied_date: null,
      // machinery_id: null,
    },
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "Agregar Gasto" : "Editar Gasto";
    },
    TotalAmountexpenses() {
      return this.items.reduce((acc, curr) => acc + curr.amount, 0);
    },
  },

  watch: {
    dialog(val) {
      val || this.close();
    },
    dialogDelete(val) {
      val || this.closeDelete();
    },
  },

  mounted() {
    this.initialize();
    this.getFormOptions();
  },

  methods: {
    initialize() {
      this.expenses = this.items;
    },
    async getFormOptions() {
      const { data } = await axios.get(this.route("expenses.options"));
      this.options = data;
    },
    editItem(item) {
      this.editedIndex = this.expenses.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
    },

    deleteItem(item) {
      this.editedIndex = this.expenses.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialogDelete = true;
    },

    deleteItemConfirm() {
      this.machineryId
        ? this.destroy()
        : this.expenses.splice(this.editedIndex, 1);
      this.closeDelete();
    },

    close() {
      this.dialog = false;
      // this.$refs.formExpense.reset();
      this.$nextTick(() => {
        this.$refs.formExpense.resetValidation();
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
      if (!this.$refs.formExpense.validate()) return;
      if (this.editedIndex > -1) {
        this.editedItem.id
          ? this.update()
          : Object.assign(this.expenses[this.editedIndex], {
              ...this.editedItem,
              expense_id: this.editedItem.expense.id,
            });
      } else {
        this.machineryId
          ? this.store()
          : this.expenses.push({
              ...this.editedItem,
              expense_id: this.editedItem.expense.id,
            });
      }
      this.close();
    },

    async store() {
      const _this = this;
      let payload = {
        ..._this.editedItem,
        expense_id: _this.editedItem.expense.id,
        machinery_id: _this.machineryId,
      };
      await _this.$inertia.post(
        _this.route("machinery-expenses.store"),
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
        expense_id: _this.editedItem.expense.id,
        machinery_id: _this.machineryId,
      };
      await _this.$inertia.put(
        _this.route("machinery-expenses.update", _this.editedItem.id),
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
        _this.route("machinery-expenses.destroy", _this.editedItem.id),
        {
          onFinish: () => ((_this.sending = false), _this.initialize()),
          only: ["item", "flash", "errors"],
          preserveState: true,
        }
      );
    },
  },
};
</script>
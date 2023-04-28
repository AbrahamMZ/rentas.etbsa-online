<template>
  <VCard id="lease-machinery-list">
    <!-- SECTION Table -->

    <v-data-table
      :headers="headers"
      :items="leases"
      class="elevation-0 text-uppercase"
      disable-pagination
      hide-default-footer
      mobile-breakpoint="0"
      item-key="id"
    >
      <template #top>
        <v-toolbar flat>
          <v-toolbar-title>Ingresos de RENTAS</v-toolbar-title>
          <v-divider class="mx-4" inset vertical />
          <v-spacer />
          <v-dialog v-model="dialog" max-width="500px">
            <template v-slot:activator="{ on, attrs }">
              <v-btn color="primary" dark class="mb-2" v-bind="attrs" v-on="on">
                Registrar Ingreso de Renta
              </v-btn>
            </template>
            <v-card>
              <v-card-title>
                <span class="text-h5">{{ formTitle }}</span>
              </v-card-title>

              <v-card-text>
                <v-container>
                  <v-form ref="formLease" v-model="valid" lazy-validation>
                    <v-row>
                      <v-col cols="12">
                        <v-text-field
                          v-model="editedItem.reference"
                          label="Nombre del Cliente"
                          :rules="[(v) => !!v || 'Requerido']"
                          counter="50"
                          outlined
                          dense
                        />
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-text-field
                          v-model="editedItem.contract_lease"
                          label="Contrato"
                          placeholder="Folio de Contrato"
                          :rules="[(v) => !!v || 'Requerido']"
                          outlined
                          dense
                        />
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-text-field
                          v-model.number="editedItem.amount"
                          label="Monto de Renta Mensual"
                          :rules="[(v) => !!v || 'Requerido']"
                          type="number"
                          prefix="$"
                          suffix="MXN"
                          outlined
                          dense
                        />
                      </v-col>
                      <v-col cols="6">
                        <v-text-field
                          v-model="editedItem.start_date"
                          label="Fecha de Inicio"
                          :rules="[(v) => !!v || 'Requerido']"
                          type="date"
                          outlined
                          dense
                        />
                      </v-col>
                      <v-col cols="6">
                        <v-text-field
                          v-model="editedItem.end_date"
                          label="Fecha de Fin"
                          :rules="[(v) => !!v || 'Requerido']"
                          type="date"
                          outlined
                          dense
                        />
                      </v-col>
                      <v-col> Periodo (Meses): {{ TermInMonths }} </v-col>
                      <v-col> Dias Totales: {{ TermInDays }} </v-col>
                      <v-col> Renta/Dia: {{ DailyFee | currency }} </v-col>
                      <v-col>
                        Ingreso Total: {{ (DailyFee * TermInDays) | currency }}
                      </v-col>
                      <v-col cols="12">
                        <v-text-field
                          v-model.number="editedItem.balance"
                          label="Balance Actual"
                          hint="El Ingreso total de Cuotas Pagadas Actual."
                          persistent-hint
                          :rules="[(v) => !!v || 'Requerido']"
                          type="number"
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

      <template #[`item.contract_lease`]="{ value }">
        <span class="blue--text"> #{{ value }} </span>
      </template>
      <template #[`item.customer`]="{ item }">
        <div class="d-flex align-center">
          <v-avatar size="28" color="green" class="me-3">
            <img v-if="item.customer.avatar" :src="item.customer.avatar" />
            <span v-else class="white--text text-sm">{{
              avatarText(item.customer.name)
            }}</span>
          </v-avatar>

          <div class="d-flex flex-column">
            <h6 class="text-sm font-weight-medium mb-0">
              {{ item.customer.name }}
            </h6>
            <span class="text-caption">
              {{ item.customer.email }}
            </span>
          </div>
        </div>
      </template>
      <template #[`item.term_lease`]="{ item }">
        <div class="d-flex flex-column pt-1">
          <h6 class="text-md font-weight-medium mb-1">
            {{ item.term_lease }} Meses
          </h6>
          <div class="caption">
            <v-icon left x-small>mdi-calendar</v-icon>
            {{ item.start_date }}
          </div>
          <div class="caption">
            <v-icon left x-small>mdi-calendar</v-icon>
            {{ item.end_date }}
          </div>
        </div>
      </template>
      <template #[`item.amount`]="{ value }">
        {{ value | currency("$", 2, { spaceBetweenAmountAndSymbol: true }) }}
      </template>
      <template #[`item.total_income`]="{ value }">
        {{ value | currency("$", 2, { spaceBetweenAmountAndSymbol: true }) }}
      </template>
      <template #[`item.balance`]="{ item }">
        {{
          (item.total_income - item.balance)
            | currency("$", 2, { spaceBetweenAmountAndSymbol: true })
        }}
      </template>
      <template #[`item.actions`]="{ item }">
        <v-icon small class="mr-2" @click="editItem(item)"> mdi-pencil </v-icon>
        <v-icon small @click="deleteItem(item)"> mdi-delete </v-icon>
      </template>
      <template #foot>
        <tfoot>
          <tr>
            <td class="text-right overline" :colspan="headers.length - 3">
              Total:
            </td>
            <td class="blue--text subtitle-2">
              {{
                TotalAmountLeases
                  | currency("$", 2, { spaceBetweenAmountAndSymbol: true })
              }}
              MXN
            </td>
            <td colspan="2" class="red--text subtitle-2">
              {{
                TotalBalanceLeases
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
  </VCard>
</template>

<script>
import { differenceInMonths, differenceInDays } from "date-fns";
export default {
  name: "LeasesMachineryIncomeTable",
  props: {
    items: {
      type: Array,
      require: false,
      default: () => {
        return [];
      },
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
        text: "Contrato",
        align: "start",
        value: "contract_lease",
      },
      { text: "Cliente", value: "customer" },
      { text: "Periodo", value: "term_lease" },
      { text: "Monto Renta/Mes", value: "amount" },
      { text: "Ingreso", value: "total_income" },
      { text: "Balance", value: "balance" },
      // { text: "Status", value: "status", sortable: false },
      { text: "", value: "actions", sortable: false },
    ],
    leases: [],
    editedIndex: -1,
    options: {
      expense_options: [],
    },
    editedItem: {
      id: null,
      contract_lease: "",
      reference: "",
      term_lease: 0,
      amount: 0,
      balance: 0,
      start_date: null,
      end_date: null,
      total_income: 0,
      machinery_id: null,
    },
    defaultItem: {
      id: null,
      contract_lease: "",
      reference: "",
      term_lease: 0,
      amount: 0,
      balance: 0,
      start_date: null,
      end_date: null,
      total_income: 0,
      machinery_id: null,
    },
  }),
  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "Agregar Renta" : "Editar Renta";
    },
    TermInMonths() {
      return !!this.editedItem.end_date && !!this.editedItem.start_date
        ? differenceInMonths(
            new Date(this.editedItem.end_date),
            new Date(this.editedItem.start_date)
          )
        : 0;
    },
    TermInDays() {
      return !!this.editedItem.end_date && !!this.editedItem.start_date
        ? differenceInDays(
            new Date(this.editedItem.end_date),
            new Date(this.editedItem.start_date)
          )
        : 0;
    },
    DailyFee() {
      return this.editedItem.amount > 0 ? this.editedItem.amount / 30 : 0;
    },
    TotalAmountLeases() {
      return this.leases.reduce((acc, curr) => acc + curr.total_income, 0);
    },
    TotalBalanceLeases() {
      return this.leases.reduce(
        (acc, curr) => acc + (curr.total_income - curr.balance),
        0
      );
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
    // this.getFormOptions();
  },
  methods: {
    initialize() {
      this.leases = this.items;
    },
    close() {
      this.dialog = false;
      // this.$refs.formLease.reset();
      this.$nextTick(() => {
        this.$refs.formLease.resetValidation();
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },
    editItem(item) {
      this.editedIndex = this.leases.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
    },

    deleteItem(item) {
      this.editedIndex = this.leases.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialogDelete = true;
    },

    deleteItemConfirm() {
      this.machineryId
        ? this.destroy()
        : this.leases.splice(this.editedIndex, 1);
      this.closeDelete();
    },
    closeDelete() {
      this.dialogDelete = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },
    async save() {
      if (!this.$refs.formLease.validate()) return;
      if (this.editedIndex > -1) {
        this.editedItem.id
          ? this.update()
          : Object.assign(this.leases[this.editedIndex], {
              ...this.editedItem,
              machinery_id: this.machineryId,
              term_lease:
                this.TermInMonths == 0
                  ? this.TermInMonths + 1
                  : this.TermInMonths,
              customer: {
                avatar: "https://cdn.vuetifyjs.com/images/john.jpg",
                name: this.editedItem.reference,
                email: "mail@example.com",
              },
              total_income: this.DailyFee * this.TermInDays,
            });
      } else {
        this.machineryId
          ? this.store()
          : this.leases.push({
              ...this.editedItem,
              machinery_id: this.machineryId,
              term_lease:
                this.TermInMonths == 0
                  ? this.TermInMonths + 1
                  : this.TermInMonths,
              customer: {
                avatar: "https://cdn.vuetifyjs.com/images/john.jpg",
                name: this.editedItem.reference,
                email: "mail@example.com",
              },
              total_income: this.DailyFee * this.TermInDays,
            });
        this.close;
      }
    },
    async store() {
      const _this = this;
      let payload = {
        ..._this.editedItem,
        machinery_id: this.machineryId,
        term_lease:
          this.TermInMonths == 0 ? this.TermInMonths + 1 : this.TermInMonths,
        total_income: this.DailyFee * this.TermInDays,
      };
      await _this.$inertia.post(_this.route("lease-incomes.store"), payload, {
        onStart: () => (_this.sending = true),
        onFinish: () => ((_this.sending = false), _this.initialize()),
        only: ["item", "flash", "errors"],
        preserveState: true,
      });
    },
    async update() {
      const _this = this;
      let payload = {
        ..._this.editedItem,
        machinery_id: this.machineryId,
        term_lease:
          this.TermInMonths == 0 ? this.TermInMonths + 1 : this.TermInMonths,
        total_income: this.DailyFee * this.TermInDays,
      };
      await _this.$inertia.put(
        _this.route("lease-incomes.update", _this.editedItem.id),
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
        _this.route("lease-incomes.destroy", _this.editedItem.id),
        {
          onFinish: () => ((_this.sending = false), _this.initialize()),
          only: ["item", "flash", "errors"],
          preserveState: true,
        }
      );
    },
    avatarText(value) {
      if (!value) return "";
      const nameArray = value.split(" ", 2);

      return nameArray.map((word) => word.charAt(0).toUpperCase()).join("");
    },
  },
};
</script>

<style>
</style>
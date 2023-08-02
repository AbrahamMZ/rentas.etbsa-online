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
        <v-toolbar flat dark>
          <v-toolbar-title>RENTAS</v-toolbar-title>
          <v-divider class="mx-4" inset vertical />
          <v-spacer />
          <v-dialog v-model="dialog" max-width="550px">
            <template v-slot:activator="{ on }">
              <v-btn color="primary" dark class="mb-2" v-on="on">
                Registrar Ingreso de Renta
              </v-btn>
            </template>
            <v-card>
              <v-card-title>
                <span class="text-h5">{{ formTitle }}</span>
                <v-spacer />
                <v-icon color="red" @click="close">mdi-close</v-icon>
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
                          counter="100"
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
                      <!-- <v-col cols="12" md="6">
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
                      </v-col> -->
                      <v-col cols="12" md="6">
                        <v-currency-field
                          v-model.number="editedItem.daily_fee"
                          label="Monto Renta/Dia"
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
                      <v-col>
                        <div>Meses:</div>
                        {{ TermInMonths }}
                      </v-col>
                      <v-col>
                        <div>Dias Totales:</div>
                        {{ TermInDays }}
                      </v-col>
                      <v-col>
                        <div>Renta/Dia:</div>
                        {{ editedItem.daily_fee | currency }}
                      </v-col>
                      <v-col>
                        <div>Ingreso Total:</div>
                        {{ (editedItem.daily_fee * TermInDays) | currency }}
                      </v-col>
                      <v-col v-show="false" cols="12">
                        <v-text-field
                          v-model.number="editedItem.balance"
                          label="Balance Actual"
                          hint="El Ingreso total de Cuotas Pagadas Actual."
                          persistent-hint
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
                <v-btn color="blue darken-1" dark @click="close">
                  Cancelar
                </v-btn>
                <v-btn
                  color="blue darken-1"
                  text
                  :loading="sending"
                  :disabled="sending"
                  @click="save"
                >
                  Guardar
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
          <v-dialog v-model="dialogCheckout" max-width="900px" persistent>
            <VCard>
              <VRow no-gutters>
                <VCol cols="12" md="6">
                  <VCardTitle>Agregar Ingreso</VCardTitle>
                  <VCardText class="pb-0">
                    <lease-fees-form />
                  </VCardText>
                  <v-card-actions class="d-flex justify-space-between">
                    <v-btn
                      color="blue darken-1"
                      dark
                      @click="cancelLeaseFees()"
                    >
                      Cancelar
                    </v-btn>
                    <v-btn color="blue darken-1" text @click="saveLeaseFees()">
                      Guardar
                    </v-btn>
                  </v-card-actions>
                </VCol>

                <VDivider
                  :vertical="$vuetify.breakpoint.mdAndUp"
                  :inset="$vuetify.breakpoint.mdAndUp"
                />

                <VCol cols="12" md="6">
                  <VCardTitle>
                    Historico de Ingresos
                    <v-spacer />
                    <v-icon color="red" @click="cancelLeaseFees()">
                      mdi-close
                    </v-icon>
                  </VCardTitle>

                  <VCardText class="pb-0">
                    <lease-fees-income-table
                      :items.sync="editedItem.lease_fees"
                    />
                  </VCardText>
                  <v-card-actions class="d-flex justify-space-between">
                    <span class="title">Total:</span>
                    <span class="title text=-weigh-bold">
                      {{ TotalFeesIncome | currency }}
                    </span>
                  </v-card-actions>
                </VCol>
              </VRow>
            </VCard>
          </v-dialog>
        </v-toolbar>
      </template>

      <template #[`item.customer`]="{ item }">
        <div class="d-flex flex-column caption">
          <div class="font-weight-medium mb-0 blue--text">
            {{ item.contract_lease }}
          </div>
          <span>
            {{ item.customer.name }}
          </span>
        </div>
      </template>
      <template #[`item.term`]="{ item }">
        <div class="d-flex flex-column caption text-no-wrap">
          <span>
            <v-icon left x-small>mdi-timelapse</v-icon>
            {{ item.term_lease }} Meses</span
          >
          <span>
            <v-icon left x-small>mdi-calendar</v-icon>
            {{ item.start_date }}
          </span>
          <span>
            <v-icon left x-small>mdi-calendar</v-icon>
            {{ item.end_date }}
          </span>
        </div>
      </template>
      <template #[`item.term_in_days`]="{ item }">
        {{ item.term_in_days }}
      </template>
      <template #[`item.daily_fee`]="{ value }">
        {{ value | currency }}
      </template>
      <template #[`item.total_income`]="{ value }">
        {{ value | currency }}
        <!-- {{
          item.lease_fees.reduce((acc, curr) => acc + curr.amount_income, 0)
            | currency
        }} -->
      </template>
      <template #[`item.balance`]="{ item }">
        <!-- resolveInvoiceBalanceVariant(item.balance, item.total_income).chip -->
        <VChip
          v-bind="
            resolveInvoiceBalanceVariant(item.lease_fees, item.total_income)
              .chip
          "
          small
        >
          <!-- item.lease_fees.reduce(
                (acc, curr) => acc + curr.amount_income,
                0
              ), -->
          {{
            resolveInvoiceBalanceVariant(item.lease_fees, item.total_income)
              .status
          }}
        </VChip>
      </template>
      <template #[`item.actions`]="{ item }">
        <v-tooltip bottom>
          <template #activator="{ on, attrs }">
            <v-icon
              class="mr-2"
              color="green"
              v-bind="attrs"
              @click="showLeaseFees(item)"
              v-on="on"
            >
              mdi-cash-register
            </v-icon>
          </template>
          <span>Registrar Ingreso</span>
        </v-tooltip>
        <v-icon class="mr-2" color="blue" @click="editItem(item)">
          mdi-pencil
        </v-icon>
        <v-icon color="red" @click="deleteItem(item)"> mdi-delete </v-icon>
      </template>
      <template #foot>
        <tfoot>
          <tr>
            <td class="text-right overline" :colspan="headers.length - 3">
              Total:
            </td>
            <td class="blue--text subtitle-2 text-no-wrap">
              {{ TotalAmountLeases | currency }}
              MXN
            </td>
            <td colspan="2" class="green--text subtitle-2 text-no-wrap">
              {{ TotalBalanceLeases | currency }}
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
import { differenceInMonths, differenceInDays, parseISO } from "date-fns";
import LeaseFeesIncomeTable from "./LeaseFeesIncomeTable.vue";
import LeaseFeesForm from "./LeaseFeesForm.vue";
export default {
  name: "LeasesMachineryIncomeTable",
  components: { LeaseFeesIncomeTable, LeaseFeesForm },
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
    dialogCheckout: false,
    valid: true,
    dialog: false,
    sending: false,
    dialogDelete: false,
    headers: [
      { text: "Contrato", value: "customer", sortable: false },
      { text: "Periodo", value: "term", sortable: false },
      { text: "Dias", value: "term_in_days", sortable: false },
      { text: "Monto Renta/Dia", value: "daily_fee", sortable: false },
      { text: "Ingreso Total", value: "total_income", sortable: false },
      { text: "Balance", value: "balance", sortable: false },
      { text: "", value: "actions", sortable: false },
    ],
    leases: [],
    options: {
      expense_options: [],
    },
    editedIndex: -1,
    editedItem: {
      id: null,
      contract_lease: "",
      reference: "",
      term_lease: 0,
      amount: 0,
      daily_fee: 0,
      balance: 0,
      start_date: null,
      end_date: null,
      total_income: 0,
      machinery_id: null,
      lease_fees: [],
    },
    defaultItem: {
      id: null,
      contract_lease: "",
      reference: "",
      term_lease: 0,
      amount: 0,
      daily_fee: 0,
      balance: 0,
      start_date: null,
      end_date: null,
      total_income: 0,
      machinery_id: null,
      lease_fees: [],
    },
  }),
  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "Agregar Renta" : "Editar Renta";
    },
    TermInMonths() {
      const endDate = parseISO(this.editedItem.end_date);
      const startDate = parseISO(this.editedItem.start_date);
      return !!endDate && !!startDate
        ? differenceInMonths(endDate, startDate)
        : 1;
    },
    TermInDays() {
      const endDate = parseISO(this.editedItem.end_date);
      const startDate = parseISO(this.editedItem.start_date);
      return !!endDate && !!startDate
        ? differenceInDays(endDate, startDate)
        : 1;
    },
    // DailyFee() {
    //   return this.editedItem.amount > 0 ? this.editedItem.amount / 30 : 0;
    // },
    TotalAmountLeases() {
      return this.leases.reduce((acc, curr) => acc + curr.total_income, 0);
    },
    TotalBalanceLeases() {
      // return this.leases.reduce((acc, curr) => acc + curr.balance, 0);
      return this.leases.reduce((accumulator, item) => {
        const leaseFees = item.lease_fees;
        if (leaseFees && Array.isArray(leaseFees)) {
          const amountIncomeSum = leaseFees.reduce((sum, fee) => {
            return sum + fee.amount_income;
          }, 0);
          return accumulator + amountIncomeSum;
        }
        return accumulator;
      }, 0);
    },
    TotalFeesIncome() {
      return this.editedItem.lease_fees.reduce(
        (acc, curr) => acc + curr.amount_income,
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
    const _this = this;
    this.$eventBus.$on("REFRESH_LEASES_INCOME", () => {
      _this.initialize();
    });
    this.initialize();
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

    save() {
      const _this = this;
      if (!_this.$refs.formLease.validate()) return;
      if (_this.editedIndex > -1) {
        _this.editedItem.id
          ? _this.update()
          : Object.assign(_this.leases[_this.editedIndex], {
              ..._this.editedItem,
              machinery_id: _this.machineryId,
              term_lease:
                _this.TermInMonths == 0
                  ? _this.TermInMonths + 1
                  : _this.TermInMonths,
              customer: {
                avatar: "https://cdn.vuetifyjs.com/images/john.jpg",
                name: _this.editedItem.reference,
                email: "mail@example.com",
              },
              total_income: _this.daily_fee * _this.TermInDays,
            });
      } else {
        _this.machineryId
          ? _this.store()
          : _this.leases.push({
              ..._this.editedItem,
              machinery_id: _this.machineryId,
              term_lease:
                _this.TermInMonths == 0
                  ? _this.TermInMonths + 1
                  : _this.TermInMonths,
              customer: {
                avatar: "https://cdn.vuetifyjs.com/images/john.jpg",
                name: _this.editedItem.reference,
                email: "mail@example.com",
              },
              total_income: _this.daily_fee * _this.TermInDays,
            });
      }
    },
    async store() {
      const _this = this;
      let payload = {
        ..._this.editedItem,
        machinery_id: _this.machineryId,
        term_lease:
          _this.TermInMonths == 0 ? _this.TermInMonths + 1 : _this.TermInMonths,
        term_in_days: _this.TermInDays,
        total_income: _this.editedItem.daily_fee * _this.TermInDays,
      };
      await _this.$inertia.post(_this.route("lease-incomes.store"), payload, {
        onStart: () => (_this.sending = true),
        onFinish: () => ((_this.sending = false), _this.initialize()),
        only: ["item", "flash", "errors"],
        preserveState: true,
      });
      _this.close();
    },
    async update() {
      const _this = this;
      let payload = {
        ..._this.editedItem,
        machinery_id: _this.machineryId,
        term_lease:
          _this.TermInMonths == 0 ? _this.TermInMonths + 1 : _this.TermInMonths,
        term_in_days: _this.TermInDays,
        total_income: _this.editedItem.daily_fee * _this.TermInDays,
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
      _this.close();
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
    resolveInvoiceBalanceVariant(feeds, total) {
      let balance = feeds.reduce((acc, curr) => acc + curr.amount_income, 0);
      if (
        this.$options.filters.currency(balance) ===
        this.$options.filters.currency(total)
      )
        return {
          status: "Pagada",
          chip: { color: "success" },
        };
      if (balance === 0)
        return {
          status: "Sin Pago",
          chip: { color: "error" },
        };

      return {
        status: this.$options.filters.currency(total - balance),
        chip: { text: true, dark: true, label: true },
      };
    },

    showLeaseFees(item) {
      this.editedIndex = this.leases.indexOf(item);
      this.editedItem = { ...item };
      this.dialogCheckout = true;
    },
    saveLeaseFees() {
      this.$eventBus.$emit("STORE_LEASE_FEES", {
        lease_id: this.editedItem.id,
      });
    },
    cancelLeaseFees() {
      this.dialogCheckout = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
        this.$eventBus.$emit("CLOSE_LEASE_FEES");
      });
    },
  },
};
</script>

<style>
</style>
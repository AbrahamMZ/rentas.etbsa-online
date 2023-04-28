<template>
  <v-card>
    <v-data-table
      :headers="headers"
      :items="servicesExpenses"
      class="elevation-0 text-uppercase"
      disable-pagination
      mobile-breakpoint="0"
      hide-default-footer
    >
      <template v-slot:top>
        <v-toolbar flat>
          <v-toolbar-title>Cargos de Servicio</v-toolbar-title>
          <v-divider class="mx-4" inset vertical />
          <v-spacer />
          <v-dialog v-model="dialog" max-width="500px" persistent>
            <template v-slot:activator="{ on, attrs }">
              <v-btn color="primary" dark class="mb-2" v-bind="attrs" v-on="on">
                Registrar nuevo Cargo
              </v-btn>
            </template>
            <v-card>
              <v-card-title>
                <span class="text-h5">{{ formTitle }}</span>
              </v-card-title>

              <v-card-text>
                <v-container>
                  <v-form
                    ref="formServiceExpense"
                    v-model="valid"
                    lazy-validation
                  >
                    <v-row>
                      <v-col cols="12">
                        <v-text-field
                          v-model="editedItem.name"
                          label="Nombre del Cargo Interno"
                          placeholder="Mano de Obra, Preparacion, Seguro , etc "
                          :rules="[
                            () => !!editedItem.name || 'Nombre is required',
                          ]"
                          counter="50"
                          outlined
                          dense
                        />
                      </v-col>
                      <v-col cols="12">
                        <v-text-field
                          v-model="editedItem.reference"
                          label="Referencia Cargo Interno (OT)"
                          placeholder="Numero de OT"
                          :rules="[
                            () =>
                              !!editedItem.reference ||
                              'Numero de Referencia es Requerido',
                          ]"
                          counter="20"
                          outlined
                          dense
                        />
                      </v-col>
                      <v-col cols="12">
                        <v-textarea
                          v-model="editedItem.description"
                          label="Descripcion del Cargo"
                          placeholder="Describir del Trabajo Realizado"
                          :rules="[
                            () =>
                              !!editedItem.description ||
                              'Descripcion es Requerida',
                          ]"
                          outlined
                          dense
                        />
                      </v-col>
                      <v-col cols="12">
                        <v-text-field
                          v-model.number="editedItem.amount"
                          label="Importe (MXN):"
                          type="number"
                          prefix="$"
                          suffix="MXN"
                          :rules="[
                            () => !!editedItem.amount || 'Importe es Requerido',
                          ]"
                          outlined
                          dense
                        />
                      </v-col>
                      <v-col cols="12">
                        <v-text-field
                          v-model="editedItem.applied_date"
                          label="Fecha"
                          type="date"
                          :rules="[
                            () =>
                              !!editedItem.applied_date || 'Fecha es Requerida',
                          ]"
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
        <div class="subtitle-2">{{ item.name }}</div>
        <div class="caption">OT: {{ item.reference }}</div>
      </template>
      <template #[`item.description`]="{ item }">
        <div class="caption text-truncate" style="max-width: 300px">
          {{ item.description }}
        </div>
        <div class="caption">Fecha: {{ item.applied_date }}</div>
      </template>
      <template #[`item.amount`]="{ value }">
        <span>
          {{ value | currency("$", 2, { spaceBetweenAmountAndSymbol: true }) }}
          MXN
        </span>
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
                TotalAmountServicesExpenses
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
export default {
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
        text: "Cargo Servicio",
        align: "start",
        value: "name",
      },
      { text: "Descripcion", value: "description", divider: true },
      { text: "Importe", value: "amount" },
      { text: "Actions", value: "actions", sortable: false },
    ],
    servicesExpenses: [],
    editedIndex: -1,
    editedItem: {
      id: null,
      reference: "",
      name: "",
      description: "",
      amount: 0,
      applied_date: null,
      // machinery_id: null,
      // status_id: null,
    },
    defaultItem: {
      id: null,
      reference: "",
      name: "",
      description: "",
      amount: 0,
      applied_date: null,
      // machinery_id: null,
      // status_id: null,
    },
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1
        ? "Nuevo Cargo de Servicio"
        : "Editar Cargo de Servicio";
    },
    TotalAmountServicesExpenses() {
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
    // eslint-disable-next-line no-console
    console.log("mounted ServicesExpensesTable");
    this.initialize();
  },

  methods: {
    initialize() {
      this.servicesExpenses = this.items;
    },
    editItem(item) {
      this.editedIndex = this.servicesExpenses.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
    },

    deleteItem(item) {
      this.editedIndex = this.servicesExpenses.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialogDelete = true;
    },

    deleteItemConfirm() {
      this.machineryId
        ? this.destroy()
        : this.servicesExpenses.splice(this.editedIndex, 1);
      this.closeDelete();
    },

    close() {
      this.dialog = false;
      this.$nextTick(() => {
        this.$refs.formServiceExpense.resetValidation();
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
      if (!this.$refs.formServiceExpense.validate()) return;
      if (this.editedIndex > -1) {
        this.machineryId
          ? this.update()
          : Object.assign(
              this.servicesExpenses[this.editedIndex],
              this.editedItem
            );
      } else {
        this.machineryId
          ? this.store()
          : this.servicesExpenses.push(this.editedItem);
      }
      this.close();
    },
    async store() {
      const _this = this;
      let payload = {
        ..._this.editedItem,
        machinery_id: _this.machineryId,
      };
      await _this.$inertia.post(
        _this.route("machinery-services-expenses.store"),
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
        machinery_id: _this.machineryId,
      };
      await _this.$inertia.put(
        _this.route("machinery-services-expenses.update", _this.editedItem.id),
        payload,
        {
          onStart: () => (_this.sending = true),
          onFinish: () => ((_this.sending = false), _this.initialize()),
          only: ["item", "flash", "errors"],
          preserveState: true,
        }
      );
    },
    async destroy() {
      const _this = this;
      await _this.$inertia.delete(
        _this.route("machinery-services-expenses.destroy", _this.editedItem.id),
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
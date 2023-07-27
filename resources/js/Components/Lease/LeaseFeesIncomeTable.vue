<template>
  <v-data-table
    :headers="headers"
    :items="items"
    :single-expand="singleExpand"
    :expanded.sync="expanded"
    item-key="id"
    show-expand
    class="elevation-1"
    hide-default-footer
    fixed-header
    height="365"
    dense
  >
    <template v-slot:top>
      <v-dialog v-model="dialogDelete" max-width="500px">
        <v-card>
          <v-card-title class="text-h5">
            Seguro en elimiar el Registro?
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
    </template>
    <template #[`item.amount_income`]="{ value }">
      {{ value | currency }}
    </template>
    <template #[`item.actions`]="{ item }">
      <v-icon small color="red" @click="deleteItem(item)">mdi-trash-can</v-icon>
    </template>
    <template v-slot:expanded-item="{ headers, item }">
      <td :colspan="headers.length" class="py-2">
        <blockquote>
          <div class="subtitle">Folio Comprobante: {{ item.folio }}</div>
          <p>Nota: {{ item.note }}</p>
        </blockquote>
      </td>
    </template>
  </v-data-table>
</template>
<script>
export default {
  name: "LeaseFeedsIncomeTable",
  props: {
    items: {
      type: Array,
      require: true,
      default: () => {
        return [];
      },
    },
  },
  data() {
    return {
      expanded: [],
      singleExpand: false,
      headers: [
        {
          text: "Ingreso",
          align: "start",
          sortable: false,
          value: "amount_income",
        },
        {
          text: "Fecha de Pago",
          align: "end",
          sortable: false,
          value: "payment_date",
        },
        {
          text: "",
          align: "end",
          sortable: false,
          value: "actions",
        },
      ],
      dialogDelete: false,
      editedIndex: -1,
      editedItem: {
        id: null,
      },
    };
  },
  methods: {
    deleteItem(item) {
      this.editedIndex = this.items.indexOf(item);
      this.editedItem = Object.assign({}, item);
      // eslint-disable-next-line no-console
      console.log(item);
      this.dialogDelete = true;
    },

    deleteItemConfirm() {
      this.destroy();
      this.closeDelete();
    },
    closeDelete() {
      this.dialogDelete = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },
    destroy() {
      const _this = this;
      _this.$inertia.delete(
        _this.route("lease_fees.destroy", _this.editedItem.id),
        {
          onFinish: () => {
            _this.items.splice(this.editedIndex, 1);
          },
          only: ["item", "flash", "errors"],
          preserveState: true,
          preserveScroll: true,
        }
      );
    },
  },
};
</script>
<style >
</style>
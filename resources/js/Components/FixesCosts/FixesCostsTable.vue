<template>
  <v-simple-table class="overline" dense>
    <template v-slot:default>
      <caption>
        <v-toolbar v-if="editable" flat dense>
          <v-toolbar-title> Gastos Fijo </v-toolbar-title>
          <v-divider class="pl-4" vertical />
          <v-spacer />
          <v-btn
            v-show="!isEditable"
            color="primary"
            dark
            @click="isEditable = true"
          >
            Editar
          </v-btn>
        </v-toolbar>
      </caption>
      <thead>
        <tr>
          <th class="text-left">Gasto Fijo</th>
          <th class="text-left">Importe:</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in FixesCosts" :key="item.id">
          <td>{{ item.id }}.-{{ item.name }}</td>
          <td style="max-width: 100px">
            <span v-if="readOnly"> {{ item.amount | currency }} MXN </span>
            <v-text-field
              v-else-if="IsEmbed || (editable && isEditable)"
              ref="fixesCostAmount"
              v-model.number="item.amount"
              type="number"
              :rules="[() => !!item.amount || 'Valor Requerido']"
              requerid
              outlined
              dense
              hide-details
              prefix="$"
              suffix="MXN"
            />
            <span v-else-if="editable"> {{ item.amount | currency }} MXN </span>
          </td>
        </tr>
      </tbody>

      <tfoot>
        <tr>
          <td class="text-right">Total:</td>
          <td class="title">{{ TotalAmountFixesCosts | currency }} MXN</td>
        </tr>
        <td colspan="2">
          <v-divider />
          <v-toolbar v-if="isEditable" flat dense>
            <v-spacer />

            <v-btn color="primary" class="mr-2" @click="cancel">
              Cancelar
            </v-btn>
            <v-btn color="red" text @click="submit">Guardar</v-btn>
          </v-toolbar>
        </td>
      </tfoot>
    </template>
  </v-simple-table>
</template>

<script>
export default {
  name: "FixesCostTable",
  props: {
    items: {
      type: Array,
      default: () => {
        return [];
      },
      require: true,
    },
    editable: {
      type: Boolean,
      default: false,
    },
    readOnly: {
      type: Boolean,
      default: false,
    },
  },
  data: function () {
    return {
      valid: true,
      isEditable: false,
      initFixesCost: this.items,
    };
  },
  computed: {
    TextBtnEditable() {
      return this.isEditable ? "Cancelar" : "Editar";
    },
    IsEmbed() {
      return !this.editable;
    },
    FixesCosts() {
      return [...this.items];
    },
    TotalAmountFixesCosts() {
      return this.FixesCosts.reduce((acc, curr) => acc + curr.amount, 0);
    },
  },
  methods: {
    cancel() {
      this.$inertia.reload({ only: ["item"] });
      this.isEditable = false;
      //   this.$refs.formFixesCost.resetValidation();
    },
    submit() {
      //   if (!this.$refs.formFixesCost.validate()) return;
      this.$emit("submit", this.FixesCosts);
      this.isEditable = false;
    },
  },
};
</script>

<style>
</style>
<template>
  <v-form ref="machinery_form_ref">
    <v-row>
      <v-col cols="6">
        <v-text-field
          v-model="form.name"
          :error-messages="errors.name"
          label="Nombre del Equipo"
          placeholder="Modelo"
          outlined
          filled
        />
      </v-col>
      <v-col cols="6">
        <v-select
          v-model="form.category_id"
          :error-messages="errors.category_id"
          :items="formOptions.categories"
          label="Categoria"
          outlined
          filled
        />
      </v-col>
      <v-col cols="6">
        <v-text-field
          v-model="form.equipment_serial"
          :error-messages="errors.equipment_serial"
          label="No. Serie Equipo"
          outlined
          filled
        />
      </v-col>
      <v-col cols="6">
        <v-text-field
          v-model="form.engine_serial"
          :error-messages="errors.engine_serial"
          label="No. Serie Motor"
          outlined
          filled
        />
      </v-col>
      <v-col cols="6">
        <v-text-field
          v-model="form.economic_serial"
          :error-messages="errors.economic_serial"
          label="No. Economico"
          outlined
          filled
        />
      </v-col>

      <v-col cols="6">
        <v-text-field
          v-model.number="form.cost_price"
          :error-messages="errors.cost_price"
          label="Costo del Equipo"
          type="number"
          prepend-inner-icon="mdi-currency-usd"
          outlined
          filled
        />
      </v-col>
      <v-col cols="6">
        <v-text-field
          v-model.number="form.value_price"
          :error-messages="errors.value_price"
          label="Valor Actual Equipo"
          type="number"
          prepend-inner-icon="mdi-currency-usd"
          outlined
          filled
        />
      </v-col>
      <v-col cols="6">
        <v-text-field
          v-model.number="form.percent_depreciation"
          :error-messages="errors.percent_depreciation"
          :rules="[
            () => !!form.percent_depreciation || 'Requerido',
            () => form.percent_depreciation <= 100 || 'No debe ser Mayor a 100',
            () => form.percent_depreciation > 1 || 'Debe ser Mayor a 1',
          ]"
          label="% Depreciacion"
          type="number"
          suffix="%"
          outlined
          filled
        />
      </v-col>
      <v-col cols="6">
        <v-text-field
          v-model="form.invoice"
          :error-messages="errors.invoice"
          label="Folio Factura"
          outlined
          filled
        />
      </v-col>
      <v-col cols="6">
        <v-text-field
          v-model="form.acquisition_date"
          :error-messages="errors.acquisition_date"
          label="Fecha de la Adquisicion"
          type="date"
          outlined
          filled
        />
      </v-col>
      <v-col cols="6">
        <v-text-field
          v-model="form.warranty_date"
          :error-messages="errors.warranty_date"
          label="F. Garantia Extendida"
          type="date"
          outlined
          filled
        />
      </v-col>
      <v-col cols="6">
        <v-textarea
          v-model="form.description"
          :error-messages="errors.description"
          label="Descripcion"
          outlined
          filled
        />
      </v-col>
    </v-row>
  </v-form>
</template>
<script>
export default {
  name: "MachineryForm",

  props: {
    form: { type: Object, required: true },
    errors: Object,
    formOptions: Object,
  },

  data() {
    return {};
  },
};
</script>

<style >
</style>

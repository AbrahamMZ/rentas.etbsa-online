<template>
  <v-form ref="machinery_form_ref">
    <v-row>
      <v-col cols="12">
        <v-text-field
          v-model="form.name"
          :error-messages="errors.name"
          label="Nombre del Equipo"
          placeholder="Modelo"
          class="overline"
          outlined
          dense
        />
      </v-col>
      <v-col cols="12">
        <v-select
          v-model="form.category_id"
          :error-messages="errors.category_id"
          :items="formOptions.categories"
          label="Categoria"
          class="overline"
          outlined
          dense
        />
      </v-col>
      <v-col cols="12">
        <v-text-field
          v-model="form.equipment_serial"
          :error-messages="errors.equipment_serial"
          label="No. Serie Equipo"
          class="overline"
          outlined
          dense
        />
      </v-col>
      <v-col cols="12">
        <v-text-field
          v-model="form.engine_serial"
          :error-messages="errors.engine_serial"
          label="No. Serie Motor"
          class="overline"
          outlined
          dense
        />
      </v-col>
      <v-col cols="12">
        <v-text-field
          v-model="form.economic_serial"
          :error-messages="errors.economic_serial"
          label="No. Economico"
          class="overline"
          outlined
          dense
        />
      </v-col>
      <v-col cols="12">
        <v-textarea
          v-model="form.description"
          :error-messages="errors.description"
          label="Descripcion"
          class="overline"
          outlined
          dense
        />
      </v-col>
      <v-col cols="6">
        <v-text-field
          v-model.number="form.cost_price"
          :error-messages="errors.cost_price"
          label="Costo del Equipo"
          class="overline"
          type="number"
          prefix="$"
          outlined
          dense
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
          class="overline"
          type="number"
          suffix="%"
          outlined
          dense
        />
      </v-col>
      <v-col cols="12">
        <v-text-field
          v-model="form.invoice"
          :error-messages="errors.invoice"
          label="Folio Factura"
          class="overline"
          outlined
          dense
        />
      </v-col>
      <v-col cols="12">
        <v-text-field
          v-model="form.acquisition_date"
          :error-messages="errors.acquisition_date"
          label="Fecha de la Adquisicion"
          class="overline"
          type="date"
          outlined
          dense
        />
      </v-col>
      <v-col cols="12">
        <v-text-field
          v-model="form.warranty_date"
          :error-messages="errors.warranty_date"
          label="Fecha Garantia Extendida"
          class="overline"
          type="date"
          outlined
          dense
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

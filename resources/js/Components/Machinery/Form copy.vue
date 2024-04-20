<template>
  <v-card flat>
    <v-tabs v-model="tab">
      <v-tab href="#principal-info">General</v-tab>
      <v-tab href="#prices-info">Informacion Adicional</v-tab>
      <v-tab href="#images">Imagenes</v-tab>
    </v-tabs>

    <v-card-text>
      <v-tabs-items v-model="tab">
        <v-tab-item value="principal-info">
          <v-form ref="FormPrincipalInfo">
            <v-row class="mt-2">
              <v-col cols="4">
                <!-- <v-treeview
                  v-model="form.categories_ids"
                  selectable
                  item-disabled="locked"
                  :items="formOptions.treeCategories"
                  item-key="id"
                  item-text="name"
                  item-children="children"
                  selection-type="independent"
                  dense
                /> -->
                <!-- <v-select
                  v-model="form.category_ids"
                  label="Categorias"
                  placeholder="Seleccione Categorias"
                  :error-messages="errors.category_ids"
                  :items="formOptions.categories"
                  item-value="id"
                  item-text="name"
                  persistent-placeholder
                  deletable-chips
                  outlined
                  multiple
                  chips
                >
                  <template #item="{ item }">
                    <v-breadcrumbs
                      :items="item.breadcrumbs_category"
                      class="py-0 pl-0"
                    />
                  </template>
                </v-select> -->
                <v-select
                  v-model="form.category_id"
                  label="Categoria"
                  :error-messages="errors.category_id"
                  :items="formOptions.categories"
                  item-value="id"
                  item-text="name"
                  outlined
                >
                  <template #selection="{ item }">
                    <v-breadcrumbs
                      :items="item.breadcrumbs_category"
                      class="py-0 pl-0"
                    />
                  </template>
                  <template #item="{ item }">
                    <v-breadcrumbs
                      :items="item.breadcrumbs_category"
                      class="py-0 pl-0"
                    />
                  </template>
                </v-select>
              </v-col>
              <v-col cols="8">
                <v-text-field
                  v-model="form.name"
                  :error-messages="errors.name"
                  label="Nombre del Equipo"
                  placeholder="Modelo"
                  outlined
                />
              </v-col>

              <v-col cols="4">
                <v-text-field
                  v-model="form.equipment_serial"
                  :error-messages="errors.equipment_serial"
                  label="No. Serie Equipo"
                  outlined
                />
              </v-col>
              <v-col cols="4">
                <v-text-field
                  v-model="form.economic_serial"
                  :error-messages="errors.economic_serial"
                  label="No. Economico"
                  outlined
                />
              </v-col>
              <v-col cols="4">
                <v-text-field
                  v-model="form.engine_serial"
                  :error-messages="errors.engine_serial"
                  label="No. Serie Motor"
                  outlined
                />
              </v-col>
              <v-col cols="12">
                <v-textarea
                  v-model="form.description"
                  :error-messages="errors.description"
                  label="Comentario"
                  placeholder="Descripcion o Comentario del Equipo"
                  persistent-placeholder
                  outlined
                />
              </v-col>
            </v-row>
          </v-form>
        </v-tab-item>
        <v-tab-item value="prices-info">
          <v-form ref="FormPricesInfo">
            <v-row class="mt-2">
              <v-col cols="6">
                <v-text-field
                  v-model.number="form.cost_price"
                  :error-messages="errors.cost_price"
                  label="Costo Adquisicion del Equipo"
                  type="number"
                  prepend-inner-icon="mdi-currency-usd"
                  outlined
                />
              </v-col>
              <v-col cols="6">
                <v-text-field
                  v-model.number="form.value_price"
                  :error-messages="errors.value_price"
                  label="Costo Actual del Equipo"
                  type="number"
                  prepend-inner-icon="mdi-currency-usd"
                  outlined
                />
              </v-col>
              <v-col cols="6">
                <v-text-field
                  v-model="form.acquisition_date"
                  :error-messages="errors.acquisition_date"
                  label="Fecha de Adquisicion"
                  type="date"
                  outlined
                />
              </v-col>
              <v-col cols="6">
                <v-text-field
                  v-model="form.warranty_date"
                  :error-messages="errors.warranty_date"
                  label="F. Garantia Extendida"
                  type="date"
                  outlined
                />
              </v-col>
              <v-col cols="6">
                <v-text-field
                  v-model.number="form.percent_depreciation"
                  :error-messages="errors.percent_depreciation"
                  :rules="[
                    () => !!form.percent_depreciation || 'Requerido',
                    () =>
                      form.percent_depreciation <= 100 ||
                      'No debe ser Mayor a 100',
                    () => form.percent_depreciation > 1 || 'Debe ser Mayor a 1',
                  ]"
                  label="% Depreciacion"
                  type="number"
                  suffix="%"
                  outlined
                />
              </v-col>
              <v-col cols="6">
                <v-text-field
                  v-model="form.invoice"
                  :error-messages="errors.invoice"
                  label="Folio Factura"
                  outlined
                />
              </v-col>
              <v-col cols="6">
                <v-text-field
                  v-model.number="form.hours_work"
                  :error-messages="errors.hours_work"
                  type="number"
                  label="Horas de Trabajo"
                  suffix="hrs"
                  outlined
                />
              </v-col>
            </v-row>
          </v-form>
        </v-tab-item>
        <v-tab-item value="images">
          <v-row class="mt-2">
            <v-col cols="12">
              <v-file-input
                v-model="images"
                label="Subir imagenes"
                color="purple"
                :error-messages="errors.images"
                :rules="[
                  (value) =>
                    !value ||
                    value.size < 2000000 ||
                    'Imagen debe Pesar Menos de 2 MB!',
                ]"
                accept="image/png, image/jpeg, image/bmp"
                placeholder="Pick an avatar"
                prepend-icon="mdi-camera"
                outlined
                multiple
                append-outer-icon="mdi-upload"
                @change="
                  (items) => {
                    if (images.length > 0) {
                      $emit('upload-images', items);
                      images = [];
                    }
                  }
                "
              />
            </v-col>
            <v-col cols="12">
              <images-list :images="Images" />
            </v-col>
          </v-row>
        </v-tab-item>
      </v-tabs-items>
    </v-card-text>
  </v-card>
</template>
<script>
import ImagesList from "../../Shared/ImagesList.vue";
export default {
  name: "MachineryForm",
  components: { ImagesList },

  props: {
    form: { type: Object, required: true },
    errors: Object,
    formOptions: Object,
  },

  data() {
    return {
      images: [],
      tab: null,
      icons: ["mdi-rewind", "mdi-play", "mdi-fast-forward"],
    };
  },
  computed: {
    Images() {
      return this.form.images;
    },
  },
};
</script>
<style scoped>
/* .v-card {
  transition: opacity 0.4s ease-in-out;
} */

/* .v-card:not(.on-hover) {
  opacity: 0.6;
} */

.show-btns {
  color: rgba(255, 255, 255, 1) !important;
}
</style>

<template>
  <v-container fluid>
    <v-row dense>
      <v-col cols="12" md="8">
        <v-card class="mb-4 elevation-4">
          <v-card-title> Informacion Maquinaria </v-card-title>
          <v-card-text>
            <v-text-field
              v-model="form.name"
              :error-messages="errors.name"
              label="Nombre del Equipo"
              placeholder="Modelo"
              outlined
            />

            <v-row class="d-flex">
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.equipment_serial"
                  :error-messages="errors.equipment_serial"
                  label="No. Serie Equipo"
                  outlined
                />
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.engine_serial"
                  :error-messages="errors.engine_serial"
                  label="No. Serie Motor"
                  outlined
                />
              </v-col>
            </v-row>

            <v-text-field
              v-model="form.economic_serial"
              :error-messages="errors.economic_serial"
              label="No. Economico"
              outlined
            />

            <v-textarea
              v-model="form.description"
              :error-messages="errors.description"
              label="Nota"
              placeholder="Descripcion o Nota del Equipo"
              persistent-placeholder
              outlined
            />
          </v-card-text>
        </v-card>

        <v-card class="elevation-4">
          <v-card-title> Imagenes </v-card-title>
          <v-card-text>
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
            <images-list :images="Images" />
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" md="4">
        <v-card class="mb-2 elevation-4">
          <v-card-title> Categoria </v-card-title>
          <v-card-text>
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

            <v-text-field
              v-model.number="form.hours_work"
              :error-messages="errors.hours_work"
              type="number"
              label="Horas de Trabajo"
              suffix="hrs"
              outlined
            />
          </v-card-text>
        </v-card>
        <v-card class="elevation-4">
          <v-card-title> Adquisicion </v-card-title>
          <v-card-text>
            <v-text-field
              v-model="form.invoice"
              :error-messages="errors.invoice"
              label="Folio Factura"
              outlined
            />
            <v-text-field
              v-model.number="form.cost_price"
              :error-messages="errors.cost_price"
              label="Costo Adquisicion del Equipo"
              type="number"
              prepend-inner-icon="mdi-currency-usd"
              outlined
            />
            <v-text-field
              v-model.number="form.value_price"
              :error-messages="errors.value_price"
              label="Costo Actual del Equipo"
              type="number"
              prepend-inner-icon="mdi-currency-usd"
              outlined
            />
            <v-text-field
              v-model="form.acquisition_date"
              :error-messages="errors.acquisition_date"
              label="Fecha de Adquisicion"
              type="date"
              outlined
            />

            <v-text-field
              v-model="form.warranty_date"
              :error-messages="errors.warranty_date"
              label="F. Garantia Extendida"
              type="date"
              outlined
            />
            <v-dialog
              ref="dialog"
              v-model="modal"
              :return-value.sync="form.dates"
              persistent
              width="400px"
            >
              <template v-slot:activator="{ on, attrs }">
                <v-text-field
                  v-model="dateRangeText"
                  label="Periodo Financiamiento"
                  prepend-icon="mdi-calendar"
                  :hint="`${jdfTermsInMonth} Meses`"
                  clearable
                  persistent-hint
                  outlined
                  filled
                  readonly
                  v-bind="attrs"
                  v-on="on"
                />
              </template>
              <v-date-picker v-model="form.dates" scrollable range>
                <v-spacer />
                <v-btn text color="primary" @click="modal = false">
                  Cancel
                </v-btn>
                <v-btn
                  text
                  color="primary"
                  @click="$refs.dialog.save(form.dates)"
                >
                  OK
                </v-btn>
              </v-date-picker>
            </v-dialog>

            <v-text-field
              v-model.number="form.jdf_amount"
              :error-messages="errors.jdf_amount"
              label="Monto de Financiamiento"
              hint="monto mensual"
              persistent-hint
              type="number"
              prepend-inner-icon="mdi-currency-usd"
              outlined
            />
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>
<script>
import { differenceInMonths, parseISO } from "date-fns";
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
      modal: false,
    };
  },
  computed: {
    Images() {
      return this.form.images;
    },
    dateRangeText() {
      return this.form.dates.join(" ~ ");
    },
    jdfTermsInMonth() {
      return !this.form.dates[1]
        ? 0
        : differenceInMonths(
            parseISO(this.form.dates[1]),
            parseISO(this.form.dates[0])
          );
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

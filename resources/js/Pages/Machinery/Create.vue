<template>
  <layout>
    <template #breadcrumbs>
      <breadcrumbs :items="breadcrumbs" />
    </template>
    <v-card-text>
      <v-row>
        <v-col cols="12" md="6">
          <v-subheader>Datos de Maquinaria</v-subheader>
          <MachineryForm
            :form.sync="form"
            :errors="errors"
            :form-options="formOptions"
          />
        </v-col>
        <v-col cols="12" md="6">
          <v-row>
            <v-col cols="12">
              <v-subheader>Informacion Adicional</v-subheader>
            </v-col>
            <v-col cols="12">
              <v-subheader>Imagenes del Equipo</v-subheader>
              <v-col cols="12">
                <v-file-input
                  v-model="form.images"
                  :error="errors.images"
                  label="Fotos del Equipo"
                  prepend-icon="mdi-camera"
                  accept="image/*"
                  truncate-length="30"
                  small-chips
                  multiple
                  show-size
                  counter
                  outlined
                  dense
                />
              </v-col>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </v-card-text>
    <v-card-actions>
      <v-btn block :loading="sending" color="primary" @click="submit">
        Guardar
      </v-btn>
    </v-card-actions>
  </layout>
</template>

<script>
import Layout from "@/Shared/Layout";
import MachineryForm from "@/Components/Machinery/Form";
import Breadcrumbs from "@/Shared/Breadcrumbs.vue";

export default {
  name: "MachineryCreate",
  metaInfo: { title: "Registrar Maquinaria" },
  // layout: Layout,
  components: { MachineryForm, Breadcrumbs, Layout },
  props: {
    formOptions: Object,
    errors: Object,
  },
  remember: "form",
  data() {
    return {
      sending: false,
      form: {
        category_id: null,
        no_serie: null,
        model: null,
        description: null,
        price: null,
        sale_price: null,
        acquisition_date: null,
        images: [],
      },
      breadcrumbs: [
        {
          text: "Maquinarias",
          disabled: false,
          href: this.route("machineries"),
        },
        { text: "Crear", disabled: true },
      ],
    };
  },
  methods: {
    submit() {
      this.$inertia.post(this.route("machineries.store"), this.form, {
        onStart: () => (this.sending = true),
        onFinish: () => (this.sending = false),
      });
    },
  },
};
</script>

<style></style>

<template>
  <v-card flat>
    <breadcrumbs :items="breadcrumbs" class="overline" />

    <v-card-text>
      <MachineryForm
        :form.sync="form"
        :errors="errors"
        :form-options="formOptions"
      />
    </v-card-text>
    <v-card-actions>
      <v-btn block :loading="sending" color="primary" @click="submit">
        Guardar
      </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
import Layout from "@/Shared/Layout";
import MachineryForm from "@/Components/Machinery/Form";
import Breadcrumbs from "@/Shared/Breadcrumbs.vue";

export default {
  metaInfo: { title: "Registrar Maquinaria" },
  layout: Layout,
  components: { MachineryForm, Breadcrumbs },
  props: {
    formOptions: Array,
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

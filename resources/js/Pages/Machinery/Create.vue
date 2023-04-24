<template>
  <layout>
    <template #breadcrumbs>
      <breadcrumbs :items="breadcrumbs" />
    </template>
    <v-card-text>
      <v-row>
        <v-col cols="12" md="4">
          <v-subheader>Datos de Maquinaria</v-subheader>
          <MachineryForm
            :form.sync="form"
            :errors="errors"
            :form-options="formOptions"
          />
          <v-subheader>Imagenes del Equipo</v-subheader>
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
        <v-col cols="12" md="8">
          <v-row>
            <v-col cols="12">
              <v-subheader>Informacion Adicional...</v-subheader>
              <v-card flat>
                <!-- <fixes-costs-table :items="form.fixes_costs" /> -->
                <expenses-table :items.sync="form.expenses" />
              </v-card>
            </v-col>
            <v-col cols="12">
              <v-card flat>
                <services-expenses-table :items.sync="form.services_expenses" />
              </v-card>
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
// import FixesCostsTable from "@/Components/FixesCosts/FixesCostsTable.vue";
import ServicesExpensesTable from "@/Components/ServiceExpenses/ServicesExpensesTable.vue";
import ExpensesTable from "@/Components/Expenses/ExpensesTable.vue";

export default {
  name: "MachineryCreate",
  metaInfo: { title: "Registrar Maquinaria" },
  // layout: Layout,
  components: {
    MachineryForm,
    Breadcrumbs,
    Layout,
    // FixesCostsTable,
    ServicesExpensesTable,
    ExpensesTable,
  },
  props: {
    formOptions: Object,
    errors: Object,
  },
  remember: "form",
  data: function () {
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
        // fixes_costs: JSON.parse(JSON.stringify(this.formOptions.fixesCosts)),
        // expenses: JSON.parse(
        //   JSON.stringify(this.formOptions.expenses)
        // ),
        // services_expenses: JSON.parse(
        //   JSON.stringify(this.formOptions.servicesExpenses)
        // ),
        expenses: [],
        services_expenses: [],
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
  computed: {
    // TotalAmountFixesCosts() {
    //   return this.form.fixes_costs.reduce((acc, curr) => acc + curr.amount, 0);
    // },
    TotalAmountServicesExpenses() {
      return this.form.services_expenses.reduce(
        (acc, curr) => acc + curr.amount,
        0
      );
    },
  },
  methods: {
    submit() {
      this.$inertia.post(this.route("machineries.store"), this.form, {
        onStart: () => (this.sending = true),
        onFinish: () => (this.sending = false),
        preserveState: true,
      });
    },
  },
};
</script>

<style></style>

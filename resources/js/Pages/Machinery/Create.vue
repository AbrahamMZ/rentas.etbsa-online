<template>
  <layout>
    <template #breadcrumbs>
      <breadcrumbs :items="breadcrumbs" />
    </template>

    <v-card-title flat class="d-flex flex-wrap justify-space-between gap-4">
      <div class="d-flex flex-column justify-center">
        <h4 class="text-h4">Registrar Maquinaria</h4>
        <span class="text-medium-emphasis">
          Equipo para el departamento de Rentas
        </span>
      </div>

      <div class="d-flex gap-4 align-center flex-wrap">
        <v-btn class="ml-2" :loading="sending" color="primary" @click="submit">
          Guardar
        </v-btn>
      </div>
    </v-card-title>

    <MachineryForm
      :form.sync="form"
      :errors="errors"
      :form-options="formOptions"
      @upload-images="uplodadImages"
    />
    <!-- <v-card-actions>
      <v-btn block :loading="sending" color="primary" @click="submit">
        Guardar
      </v-btn>
    </v-card-actions> -->
  </layout>
</template>

<script>
import Layout from "@/Shared/Layout";
import MachineryForm from "@/Components/Machinery/Form";
import Breadcrumbs from "@/Shared/Breadcrumbs.vue";
import { differenceInMonths, parseISO } from "date-fns";

export default {
  name: "MachineryCreate",
  metaInfo: { title: "Registrar Maquinaria" },
  components: {
    MachineryForm,
    Breadcrumbs,
    Layout,
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
        category_ids: [],
        category_id: null,
        name: "",
        equipment_serial: "",
        economic_serial: "",
        engine_serial: "",
        description: "",
        cost_price: "",
        value_price: "",
        invoice: "",
        hours_work: null,
        percent_depreciation: 25,
        acquisition_date: "",
        warranty_date: "",
        expenses: [],
        services_expenses: [],
        leases_incomes: [],
        images: [],
        dates: [null, null],
        jdf_amount: [],
        jdf_start_date: null,
        jdf_end_date: null,
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
    TotalAmountServicesExpenses() {
      return this.form.services_expenses.reduce(
        (acc, curr) => acc + curr.amount,
        0
      );
    },
  },
  mounted() {
    this.$eventBus.$on("remove-image", ({ index }) => {
      this.form.images.splice(index, 1);
    });
  },
  methods: {
    uplodadImages(images = []) {
      const _this = this;
      images.forEach((img) => {
        _this.form.images.push(img);
      });
    },
    submit() {
      let payload = {
        ...this.form,
        jdf_start_date: this.form.dates[0],
        jdf_end_date: this.form.dates[1],
        jdf_terms: differenceInMonths(
          parseISO(this.form.dates[1]),
          parseISO(this.form.dates[0])
        ),
        percent_depreciation: this.form.percent_depreciation / 100,
      };
      this.$inertia.post(this.route("machineries.store"), payload, {
        forceFormData: true,
        onStart: () => (this.sending = true),
        onFinish: () => (this.sending = false),
        preserveState: true,
      });
    },
  },
};
</script>

<style></style>

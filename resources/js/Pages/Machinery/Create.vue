<template>
  <layout>
    <template #breadcrumbs>
      <breadcrumbs :items="breadcrumbs" />
    </template>

    <MachineryForm
      :form.sync="form"
      :errors="errors"
      :form-options="formOptions"
      @upload-images="uplodadImages"
    />
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
        percent_depreciation: 25,
        acquisition_date: "",
        warranty_date: "",
        expenses: [],
        services_expenses: [],
        leases_incomes: [],
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
    TotalAmountServicesExpenses() {
      return this.form.services_expenses.reduce(
        (acc, curr) => acc + curr.amount,
        0
      );
    },
  },
  mounted() {
    this.$eventBus.$on("remove-image", ({index}) => {
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

<template>
  <v-card flat>
    <breadcrumbs :items="breadcrumbs" class="overline" />
    <trashed-message v-if="item.deleted_at" class="mb-6" @restore="restore">
      Este Registro a sido Eliminado.
    </trashed-message>

    <v-card-text>
      <MachineryForm
        :form.sync="form"
        :errors="errors"
        :form-options="formOptions"
      />
    </v-card-text>
    <v-card-actions>
      <v-btn v-if="!item.deleted_at" color="error" @click="destroy">
        Eliminar
      </v-btn>
      <v-spacer />
      <v-btn :loading="sending" color="primary" @click="submit">Guardar</v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
import Layout from "@/Shared/Layout";
import TrashedMessage from "@/Shared/TrashedMessage";
import MachineryForm from "@/Components/Machinery/Form";
import Breadcrumbs from "@/Shared/Breadcrumbs.vue";

export default {
  metaInfo: { title: "Registrar Maquinaria" },
  layout: Layout,
  components: {
    MachineryForm,
    TrashedMessage,
    Breadcrumbs, 
  },
  props: {
    errors: Object,
    item: Object,
    formOptions: Object,
  },
  remember: "form",
  data() {
    return {
      sending: false,
      form: {
        category_id: this.item.category_id,
        no_serie: this.item.no_serie,
        model: this.item.model,
        description: this.item.description,
        price: this.item.price,
        sale_price: this.item.sale_price,
        acquisition_date: this.item.acquisition_date,
        images: [],
      },
      breadcrumbs: [
        {
          text: "Maquinarias",
          disabled: false,
          href: this.route("machineries"),
          exact: true,
        },
        { text: `${this.item.no_serie}`, 
          href: this.route("machineries.show", { machinery: this.item.id }) },
        { text: "Editar", disabled: true },
      ],
    };
  },
  methods: {
    submit() {
      this.$inertia.put(
        this.route("machineries.update", this.item.id),
        this.form,
        {
          onStart: () => (this.sending = true),
          onFinish: () => (this.sending = false),
        }
      );
    },
    destroy() {
      if (confirm("Desea Eliminar la Maquinaria?")) {
        this.$inertia.delete(this.route("machineries.destroy", this.item.id));
      }
    },
    restore() {
      if (confirm("Desea Restaurar la Maquinaria?")) {
        this.$inertia.put(this.route("machineries.restore", this.item.id));
      }
    },
  },
};
</script>

<style></style>

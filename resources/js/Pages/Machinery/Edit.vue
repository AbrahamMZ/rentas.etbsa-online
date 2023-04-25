<template>
  <layout>
    <template #breadcrumbs>
      <breadcrumbs :items="breadcrumbs" />
    </template>
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
  </layout>
</template>

<script>
import Layout from "@/Shared/Layout";
import TrashedMessage from "@/Shared/TrashedMessage";
import MachineryForm from "@/Components/Machinery/Form";
import Breadcrumbs from "@/Shared/Breadcrumbs.vue";

export default {
  name: "MachineryEdit",
  metaInfo: { title: "Registrar Maquinaria" },
  // layout: ,
  components: {
    MachineryForm,
    TrashedMessage,
    Breadcrumbs,
    Layout,
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
        name: this.item.name,
        description: this.item.description,
        equipment_serial: this.item.equipment_serial,
        economic_serial: this.item.economic_serial,
        engine_serial: this.item.engine_serial,
        cost_price: this.item.cost_price,
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
        {
          text: `${this.item.name}`,
          href: this.route("machineries.show", { machinery: this.item.id }),
        },
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

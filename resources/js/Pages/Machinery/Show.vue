<template>
  <v-card flat>
    <breadcrumbs :items="breadcrumbs" class="overline" />
    <trashed-message v-if="item.deleted_at" class="mb-6" @restore="restore">
      Este Registro fue Eliminado.
    </trashed-message>
    <v-card-text>
      <v-row dense align="center">
        <v-col cols="12" md="4">
          <v-card color="grey lighten-4">
            <v-card-text class="text-center">
              <h3 class="text-h4 mb-2">
                {{ item.model }}
              </h3>
              <div class="blue--text mb-2 font-weight-bold">
                {{ item.category }}
              </div>
            </v-card-text>
            <v-divider />
            <v-row dense class="text-left" tag="v-card-text">
              <v-col class="text-right mr-4 mb-2" tag="strong" cols="5">
                No. Serie:
              </v-col>
              <v-col>{{ item.no_serie }}</v-col>
              <v-col class="text-right mr-4 mb-2" tag="strong" cols="5">
                Valor de Maquina:
              </v-col>
              <v-col>
                {{ item.price | currency }}
              </v-col>
              <v-col class="text-right mr-4 mb-2" tag="strong" cols="5">
                Fecha de Adquisicion:
              </v-col>
              <v-col>
                {{ item.acquisition_date | placeholder("No Especificado") }}
              </v-col>
            </v-row>
            <v-card-actions>
              <v-btn v-if="!item.deleted_at" color="error" @click="destroy">
                Eliminar
              </v-btn>
              <v-spacer />
              <v-btn dark color="blue" @click="edit(item.id)">
                Modificar
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-col>
        <v-col cols="12" md="8">
          <v-card color="grey lighten-4">
            <v-toolbar flat color="secondary" dark>
              <v-toolbar-title>Informacion</v-toolbar-title>
            </v-toolbar>
            <v-tabs vertical color="secondary">
              <v-tab>
                <v-icon left> mdi-ballot-recount </v-icon>
                Rentas
              </v-tab>
              <v-tab>
                <v-icon left> mdi-toolbox </v-icon>
                Servicios
              </v-tab>
              <v-tab>
                <v-icon left> mdi-folder </v-icon>
                Expedientes
              </v-tab>

              <v-tab-item>
                <v-card flat>
                  <v-card-text>
                    <services-table />
                  </v-card-text>
                </v-card>
              </v-tab-item>
              <v-tab-item>
                <v-card flat>
                  <v-card-text>
                    <services-table />
                  </v-card-text>
                </v-card>
              </v-tab-item>
              <v-tab-item>
                <v-card flat height="200px">
                  <v-card-text />
                </v-card>
              </v-tab-item>
            </v-tabs>
          </v-card>
        </v-col>
      </v-row>
    </v-card-text>
  </v-card>
</template>

<script>
import Layout from "@/Shared/Layout";
import TrashedMessage from "@/Shared/TrashedMessage";
import ServicesTable from "@/Components/Machinery/ServicesTable.vue";
import Breadcrumbs from "@/Shared/Breadcrumbs.vue";

export default {
  metaInfo: { title: "Detalle Maquinaria" },
  layout: Layout,
  components: {
    TrashedMessage,
    ServicesTable,
    Breadcrumbs,
  },
  props: {
    errors: Object,
    item: Object,
  },
  remember: "form",
  data() {
    return {
      sending: false,
      breadcrumbs: [
        {
          text: "Maquinarias",
          disabled: false,
          href: this.route("machineries"),
          exact: true,
        },
        { text: `${this.item.no_serie}`, disabled: true },
        {
          text: "Detalle",
          disabled: true,
        },
      ],
    };
  },
  methods: {
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
    edit(_item_id) {
      this.$inertia.visit(this.route("machineries.edit", _item_id));
    },
  },
};
</script>

<style></style>

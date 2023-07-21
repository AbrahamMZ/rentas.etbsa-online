<template>
  <layout>
    <template #breadcrumbs>
      <breadcrumbs :items="breadcrumbs" />
    </template>

    <search-filter v-model="form.search" @reset="reset">
      <v-col cols="12" md="3" class="pa-1">
        <v-select
          v-model="form.trashed"
          :items="options"
          label="Filtro Elminados:"
          dense
          hide-details
          outlined
        />
      </v-col>
      <v-col cols="12" md="3" class="pa-1">
        <v-text-field
          v-model="form.search"
          label="Filtro:"
          placeholder=""
          outlined
          dense
          append-icon="mdi-magnify"
          hide-details
        />
      </v-col>
    </search-filter>
    <v-row class="text-end px-4" no-gutters justify="end" align="center">
      <v-col cols="12" md="3" class="pa-2">
        <v-btn @click="create()">Registrar Maquinaria</v-btn>
      </v-col>
    </v-row>

    <v-card-text>
      <v-data-table
        :headers="headers"
        :items="items.data"
        :items-per-page.sync="form.per_page"
        :page.sync="form.page"
        :server-items-length="items.total"
        calculate-widths
        fixed-header
        caption
        dense
      >
        <template #[`item.name`]="{ item }">
          <div class="d-flex align-center">
            <v-avatar size="34" class="me-3">
              <v-img v-if="item.images[0]" :src="item.images[0].path" />
              <v-img
                v-else
                :src="`https://picsum.photos/10/6?image=${5 + 10}`"
              />
            </v-avatar>

            <div class="d-flex flex-column">
              <div class="font-weight-medium mb-0">
                {{ item.name }}
              </div>
              <span class="text-caption">
                {{ item.category }}
              </span>
            </div>
          </div>
        </template>
        <template #[`item.equipment_serial`]="{ item }">
          <div class="font-weight-bold">
            {{ item.equipment_serial }}
          </div>
          <div class="grey--text text-subtitle-2">
            N.E: {{ item.economic_serial }}
          </div>
        </template>
        <template #[`item.total_monthly_expenses_amount`]="{ value }">
          {{ value | currency }}
        </template>
        <template #[`item.total_expenses_amount`]="{ value }">
          {{ value | currency }}
        </template>
        <template #[`item.total_service_expenses_amount`]="{ value }">
          {{ value | currency }}
        </template>
        <template #[`item.total_cost_equipment`]="{ value }">
          {{ value | currency }}
        </template>

        <template #[`item.action`]="{ item }">
          <v-chip v-if="item.deleted_at" color="warning" outlined x-small>
            Eliminado
          </v-chip>

          <v-btn text icon small @click="show(item.id)">
            <v-icon small>mdi-eye</v-icon>
          </v-btn>
          <v-btn text icon small @click="edit(item.id)">
            <v-icon small>mdi-pencil</v-icon>
          </v-btn>
        </template>
      </v-data-table>
    </v-card-text>
  </layout>
</template>

<script>
import Layout from "@/Shared/Layout";
import mapValues from "lodash/mapValues";
import pickBy from "lodash/pickBy";
import throttle from "lodash/throttle";
import SearchFilter from "@/Shared/SearchFilter";
import Breadcrumbs from "@/Shared/Breadcrumbs.vue";

export default {
  name: "MachineryIndex",
  metaInfo: { title: "Maquinaria" },
  components: {
    SearchFilter,
    Breadcrumbs,
    Layout,
  },
  props: { items: Object, filters: Object },
  data() {
    return {
      headers: [
        { text: "Nombre Equipo", value: "name" },
        { text: "Serie Equipo", value: "equipment_serial" },
        {
          text: "Gasto Mensual",
          value: "total_monthly_expenses_amount",
        },
        {
          text: "Gastos",
          value: "total_expenses_amount",
        },
        {
          text: "Cargos Internos",
          value: "total_service_expenses_amount",
        },
        { text: "Costo Total Equipo", value: "total_cost_equipment" },
        {
          text: "",
          align: "end",
          value: "action",
          sortable: false,
        },
      ],
      form: {
        search: this.filters.search,
        trashed: this.filters.trashed,
        page: Number(this.filters.page) || 1,
        per_page: Number(this.filters.per_page) || 10,
      },
      options: [
        { text: "(Vacio)", value: "" },
        { divider: true },
        { text: "Con", value: "with" },
        { text: "Solamente", value: "only" },
      ],
      breadcrumbs: [{ text: "Inventario Maquinaria", disabled: true }],
    };
  },

  watch: {
    form: {
      handler: throttle(function () {
        let query = pickBy(this.form);
        this.$inertia.get(
          this.route(
            "machineries",
            Object.keys(query).length ? query : { remember: "forget" }
          ),
          {},
          { preserveState: true }
        );
      }, 150),
      deep: true,
    },
  },
  methods: {
    search(value) {
      // eslint-disable-next-line no-console
      console.log(value, "input");
    },
    reset() {
      this.form = mapValues(this.form, () => null);
      this.form.page = 1;
    },
    create() {
      this.$inertia.visit(this.route("machineries.create"));
    },
    edit(_item_id) {
      this.$inertia.visit(this.route("machineries.edit", _item_id));
    },
    show(_item_id) {
      this.$inertia.visit(this.route("machineries.show", _item_id));
    },
  },
};
</script>

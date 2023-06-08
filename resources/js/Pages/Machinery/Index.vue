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
      <data-table-wrapper
        :items="items.data"
        :headers="headers"
        with-search
        sort-by="id"
        sort-desc
      >
        <template #item="{ item }">
          <tr>
            <td class="text-no-wrap">
              <div class="font-weight-bold">{{ item.name }}</div>
              <span class="caption">{{ item.category }}</span>
            </td>
            <td class="text-no-wrap">
              <div class="font-weight-bold">
                {{ item.equipment_serial }}
              </div>
              <div class="grey--text text-subtitle-2">
                N.E: {{ item.economic_serial }}
              </div>
            </td>
            <td class="text-no-wrap">
              {{
                item.total_monthly_expenses_amount
                  | currency("$", 2, {
                    spaceBetweenAmountAndSymbol: true,
                  })
              }}
              MXN
            </td>
            <td class="text-no-wrap">
              {{
                item.total_expenses_amount
                  | currency("$", 2, {
                    spaceBetweenAmountAndSymbol: true,
                  })
              }}
              MXN
            </td>
            <td class="text-no-wrap">
              {{
                item.total_service_expenses_amount
                  | currency("$", 2, {
                    spaceBetweenAmountAndSymbol: true,
                  })
              }}
              MXN
            </td>
            <td class="text-no-wrap">
              {{
                item.total_cost_equipment
                  | currency("$", 2, {
                    spaceBetweenAmountAndSymbol: true,
                  })
              }}
              MXN
            </td>

            <td class="text-right">
              <v-chip v-if="item.deleted_at" color="warning" outlined x-small>
                Eliminado
              </v-chip>

              <v-btn text icon small @click="show(item.id)">
                <v-icon small>mdi-eye</v-icon>
              </v-btn>
              <v-btn text icon small @click="edit(item.id)">
                <v-icon small>mdi-pencil</v-icon>
              </v-btn>
            </td>
          </tr>
        </template>
      </data-table-wrapper>
    </v-card-text>
    <Pagination
      v-if="items.links.length > 1"
      :links="items.links"
      :page="form.page"
      route="machineries"
      @input="(v) => (form.page = v)"
    />
  </layout>
</template>

<script>
import Layout from "@/Shared/Layout";
import DataTableWrapper from "@/Shared/DataTableWrapper";
import mapValues from "lodash/mapValues";
import pickBy from "lodash/pickBy";
import throttle from "lodash/throttle";
import SearchFilter from "@/Shared/SearchFilter";
import Pagination from "@/Shared/Pagination";
import Breadcrumbs from "@/Shared/Breadcrumbs.vue";

export default {
  name: "MachineryIndex",
  metaInfo: { title: "Maquinaria" },
  // layout: Layout,
  components: {
    DataTableWrapper,
    SearchFilter,
    Pagination,
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
        page: this.filters.page | 1,
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

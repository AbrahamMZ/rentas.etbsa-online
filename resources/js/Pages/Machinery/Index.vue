<!-- eslint-disable no-unused-vars -->
<!-- eslint-disable vue/html-self-closing -->
<!-- eslint-disable vue/valid-v-for -->
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
      <v-col v-if="viewPort == 'calendar'" cols="12" md="3" class="pa-1">
        <v-select
          v-model="form.year"
          label="Año:"
          :items="[2026, 2025, 2024, 2023, 2022, 2021, 2020, 2019, 2018, 2017]"
          hide-details
          outlined
          dense
        />
      </v-col>
    </search-filter>
    <v-row class="text-end px-4" no-gutters justify="end" align="center">
      <v-btn-toggle v-model="viewPort" borderless dense>
        <v-btn value="table">
          <span class="hidden-sm-and-down">TABLA</span>

          <v-icon right> mdi-table </v-icon>
        </v-btn>

        <v-btn value="calendar">
          <span class="hidden-sm-and-down">CALEDARIO</span>

          <v-icon right> mdi-calendar </v-icon>
        </v-btn>
      </v-btn-toggle>
      <v-spacer />
      <v-col cols="12" md="3" class="pa-2">
        <v-btn @click="create()">Registrar Maquinaria</v-btn>
      </v-col>
    </v-row>

    <v-card-text v-if="viewPort == 'table'">
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
        <template #[`item.ocupancy_rate`]="{ value }">
          {{ value | percent }}
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
        <template #[`item.current_sale_price`]="{ value }">
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
    <v-card-text v-if="viewPort == 'calendar'">
      <v-simple-table max-height="500px" fixed-header dense>
        <template v-slot:default>
          <thead class="text-uppercase">
            <tr>
              <th class="text-left text-h6">Totales:</th>
              <th v-for="month in 12" :key="month"></th>
              <th class="text-right text-h6">
                {{ getTotalIncome || 0 | currency }}
              </th>
              <th class="text-right text-h6">
                {{ getTotalBalance || 0 | currency }}
              </th>
            </tr>
          </thead>
          <thead>
            <tr>
              <th class="text-left">No Serie</th>
              <th v-for="month in 12" :key="month" class="text-uppercase">
                {{ getMonthNameByIndex(month) }}
              </th>
              <th class="text-right">Ingreso Esperado</th>
              <th class="text-right">Ingreso Total</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="machinery in calendar" :key="machinery.equipment_serial">
              <td>
                <div class="d-flex flex-column">
                  <div class="font-weight-medium mb-0">
                    {{ machinery.name }}
                  </div>
                  <span class="text-caption">
                    {{ machinery.equipment_serial }}
                  </span>
                </div>
              </td>
              <td
                v-for="month in 12"
                :key="`row-${machinery.equipment_serial}-${month}`"
                class="pa-0"
              >
                <!-- <template v-if="hasRentInRange(month, machinery.lease_incomes)"> -->
                <v-menu
                  v-if="hasRentInRange(month, machinery.lease_incomes)"
                  v-model="menu[`menu-${machinery.equipment_serial}-${month}`]"
                  :close-on-content-click="false"
                  :nudge-width="200"
                  offset-x
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-sheet
                      :key="`sheet-${machinery.equipment_serial}-${month}`"
                      :color="getRentColor(month, machinery.lease_incomes)"
                      height="100%"
                      width="100%"
                      tile
                      v-bind="attrs"
                      v-on="on"
                    />
                  </template>

                  <v-card>
                    <v-list>
                      <v-list-item>
                        <v-list-item-content>
                          <v-list-item-title>
                            <!-- {{ item.equipment_serial }} -->
                            Cliente
                            {{
                              getLeaseInfo(month, machinery.lease_incomes)
                                .reference
                            }}
                          </v-list-item-title>
                          <v-list-item-subtitle class="text-uppercase">
                            MES: {{ getMonthNameByIndex(month, "MMMM") }}
                          </v-list-item-subtitle>
                          <v-list-item-subtitle>
                            Start
                            {{
                              getLeaseInfo(month, machinery.lease_incomes)
                                .start_date
                            }}
                            - End
                            {{
                              getLeaseInfo(month, machinery.lease_incomes)
                                .end_date
                            }}
                          </v-list-item-subtitle>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>

                    <v-divider />

                    <v-card-actions>
                      <v-spacer />

                      <v-btn
                        text
                        @click="
                          menu[
                            `menu-${machinery.equipment_serial}-${month}`
                          ] = false
                        "
                      >
                        Cancel
                      </v-btn>
                      <v-btn color="primary" text @click="show(machinery.id)">
                        Ir a Detalle
                      </v-btn>
                    </v-card-actions>
                  </v-card>
                </v-menu>
                <!-- </template> -->

                <v-sheet
                  v-else-if="month == new Date().getMonth() + 1"
                  :key="`sheet-${machinery.equipment_serial}-${month}`"
                  color="green lighten-3"
                  height="100%"
                  width="100%"
                  tile
                />
              </td>
              <td class="text-right">
                {{ machinery.total_income || 0 | currency }}
              </td>
              <td class="text-right">
                {{ machinery.total_balance || 0 | currency }}
              </td>
              <!-- <td>{{ machinery.total_expenses_amount | currency }}</td>
              <td>{{ machinery.total_cost_equipment | currency }}</td> -->
            </tr>
          </tbody>
        </template>
      </v-simple-table>
    </v-card-text>
  </layout>
</template>

<script>
import { format, parse, isWithinInterval } from "date-fns";
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
  props: { items: Object, filters: Object, calendar: Array },
  data() {
    return {
      fav: {},
      menu: {},
      viewPort: "table",
      headers: [
        { text: "Nombre Equipo", value: "name" },
        { text: "Serie Equipo", value: "equipment_serial" },
        { text: "Tasa Ocupacion %", value: "ocupancy_rate" },
        { text: "Valor Comercial", value: "current_sale_price" },
        // {
        //   text: "Gasto Mensual",
        //   value: "total_monthly_expenses_amount",
        // },
        // {
        //   text: "Gastos",
        //   value: "total_expenses_amount",
        // },
        // {
        //   text: "Cargos Internos",
        //   value: "total_service_expenses_amount",
        // },
        // { text: "Costo Total Equipo", value: "total_cost_equipment" },
        {
          text: "Acciones",
          align: "end",
          value: "action",
          sortable: false,
        },
      ],
      form: {
        search: this.filters.search,
        trashed: this.filters.trashed,
        year: Number(this.filters.year) || 2023,
        page: Number(this.filters.page) || 1,
        per_page: Number(this.filters.per_page) || 10,
      },
      options: [
        { text: "(Vacio)", value: "" },
        { divider: true },
        { text: "Con", value: "with" },
        { text: "Solamente", value: "only" },
      ],
      colors: [
        "#1867c0",
        "#fb8c00",
        "#000000",
        "#1867c0",
        "#fb8c00",
        "#000000",
      ],
      breadcrumbs: [{ text: "Inventario Maquinaria", disabled: true }],
    };
  },

  computed: {
    getTotalBalance() {
      return this.calendar.reduce((acc, curr) => acc + curr.total_balance, 0);
    },
    getTotalIncome() {
      return this.calendar.reduce((acc, curr) => acc + curr.total_income, 0);
    },
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
    reset() {
      this.form = mapValues(this.form, () => {
        null;
      });
      this.form = {
        year: new Date().getFullYear(),
        page: 1,
      };
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
    getMonthNameByIndex(monthIndex, _format = "MMM") {
      const date = new Date(this.filters.year, monthIndex - 1, 1);
      return format(date, _format);
    },

    isMonthYearInRange(month, startDate, endDate) {
      // Parsear el mes/año a una fecha
      const monthYear = `${month}/${this.filters.year}`;
      const parsedMonthYear = parse(monthYear, "MM/yyyy", new Date());

      // Obtener el primer día del mes/año
      const firstDayOfMonthYear = new Date(
        parsedMonthYear.getFullYear(),
        parsedMonthYear.getMonth(),
        1
      );

      // Parsear las fechas de inicio y fin del rango
      const parsedStartDate = parse(startDate, "yyyy-MM-dd", new Date());
      const parsedEndDate = parse(endDate, "yyyy-MM-dd", new Date());

      // eslint-disable-next-line no-console
      // console.log(firstDayOfMonthYear, parsedStartDate, parsedEndDate);
      // Verificar si el mes/año se encuentra dentro del rango
      return isWithinInterval(firstDayOfMonthYear, {
        start: parsedStartDate,
        end: parsedEndDate,
      });
    },
    hasRentInRange(month, leases) {
      return leases.some((lease) =>
        this.isMonthYearInRange(month, lease.start_date, lease.end_date)
      );
    },
    getLeaseInfo(month, leases) {
      const foundLease = leases.find((lease) =>
        this.isMonthYearInRange(month, lease.start_date, lease.end_date)
      );
      return foundLease || {};
    },
    getRentColor(month, leases) {
      const foundLease = leases.find((lease) =>
        this.isMonthYearInRange(month, lease.start_date, lease.end_date)
      );
      return foundLease
        ? this.colors[leases.indexOf(foundLease)]
        : "transparent";
    },
  },
};
</script>

<style>
.sheet-text {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  text-align: center;
  width: 100%;
}
</style>
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
        <v-select
          v-model="form.category_ids"
          label="Filtro Categoria"
          :items="optionsFilters.categories"
          item-value="id"
          item-text="name"
          outlined
          clearable
          dense
          hide-details
          multiple
          chips
          small-chips
          deletable-chips
        >
          <template #item="{ item }">
            <v-breadcrumbs
              :items="item.breadcrumbs_category"
              class="py-0 pl-0"
            />
          </template>
        </v-select>
      </v-col>
      <v-col cols="12" md="3" class="pa-1">
        <v-text-field
          v-model="form.search"
          label="Buscar:"
          placeholder=""
          outlined
          dense
          append-icon="mdi-magnify"
          hide-details
        />
      </v-col>
      <v-col v-show="viewPort == 'calendar'" cols="12" md="3" class="pa-1">
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
          <span class="hidden-sm-and-down">CALENDARIO</span>

          <v-icon right> mdi-calendar </v-icon>
        </v-btn>
      </v-btn-toggle>
      <v-spacer />
      <v-col cols="12" md="3" class="pa-2">
        <v-btn color="primary" dark @click="create()">Registrar Equipo</v-btn>
      </v-col>
    </v-row>

    <v-card-text v-show="viewPort == 'table'">
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
    <v-card-text v-show="viewPort == 'calendar'">
      <v-simple-table max-height="500px" fixed-header dense class="caption">
        <template v-slot:default>
          <thead class="text-uppercase">
            <tr>
              <th class="text-left title">Totales:</th>
              <!-- <th class="text-right title text-no-wrap">
                {{ getTotalAnualIncome || 0 | currency }}
              </th> -->
              <th class="text-right title text-no-wrap">
                {{ getTotalIncome || 0 | currency }}
              </th>
              <th class="text-right title text-no-wrap">
                {{ getTotalBalance || 0 | currency }}
              </th>
              <th v-for="month in 12" :key="month"></th>
            </tr>
          </thead>
          <thead>
            <tr>
              <th class="text-left">No Serie</th>
              <!-- <th class="text-right text-no-wrap">Ingreso Estimado</th> -->
              <th class="text-right text-no-wrap">Ingreso Total Estimado</th>
              <th class="text-right">Ingreso Total</th>
              <th
                v-for="month in 12"
                :key="month"
                class="text-uppercase text-center pa-0"
                style="width: 3px"
              >
                {{ getMonthNameByIndex(month) }}
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="machinery in calendar" :key="machinery.equipment_serial">
              <td>
                <div class="d-flex flex-column my-1">
                  <div class="font-weight-medium mb-0 text-wrap">
                    <v-label @click="show(machinery.id)">
                      <span class="blue--text" style="cursor: pointer">
                        {{ machinery.equipment_serial }}
                      </span>
                    </v-label>
                  </div>
                  <span class="text-caption">
                    {{ machinery.name }}
                  </span>
                </div>
              </td>

              <!-- <td class="text-right">
                {{
                  getAccumulatedLeaseAmount(machinery.lease_incomes) | currency
                }}
              </td> -->
              <td class="text-right">
                {{ machinery.total_income || 0 | currency }}
              </td>
              <td class="text-right">
                <!-- {{ machinery.total_balance || 0 | currency }} -->
                {{
                  getAccumulatedLeaseFeesAmount(machinery.lease_incomes)
                    | currency
                }}
              </td>
              <td
                v-for="month in 12"
                :key="`row-${machinery.equipment_serial}-${month}`"
                class="pa-0"
                style="border: 1px solid; width: 3px"
              >
                <v-menu
                  v-if="hasRentInRange(month, machinery.lease_incomes)"
                  :key="`sheet-${machinery.equipment_serial}-${month}`"
                  v-model="menu[`menu-${machinery.equipment_serial}-${month}`]"
                  :close-on-content-click="false"
                  :nudge-width="200"
                  offset-y
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-sheet
                      :color="getRentColor(month, machinery.lease_incomes)"
                      v-bind="attrs"
                      v-on="on"
                    >
                      <v-row align="center" class="text-center white--text">
                        <v-col>
                          {{
                            getLeaseInfo(month, machinery.lease_incomes)
                              .daysInMonth
                          }}
                        </v-col>
                      </v-row>
                    </v-sheet>
                  </template>
                  <v-card class="mx-auto" max-width="360">
                    <v-card-text>
                      <div class="text-uppercase">
                        {{
                          `${getMonthNameByIndex(month, "MMMM")}/${form.year}`
                        }}
                        <v-spacer />
                        {{
                          getLeaseInfo(month, machinery.lease_incomes)
                            .start_date
                        }}
                        -
                        {{
                          getLeaseInfo(month, machinery.lease_incomes).end_date
                        }}
                      </div>
                      <p class="subheader text--primary">
                        {{
                          getLeaseInfo(month, machinery.lease_incomes).reference
                        }}
                      </p>
                      <!-- <div class="text--primary">
                        {{ machinery.description }}
                      </div> -->
                    </v-card-text>
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
                        Cerrar
                      </v-btn>
                      <v-btn color="primary" text @click="show(machinery.id)">
                        Ir a Detalle
                      </v-btn>
                    </v-card-actions>
                  </v-card>
                </v-menu>
              </td>
            </tr>
          </tbody>
        </template>
      </v-simple-table>
    </v-card-text>
  </layout>
</template>

<script>
import {
  format,
  parse,
  startOfMonth,
  endOfMonth,
  getDaysInMonth,
  getMonth,
  getYear,
  differenceInDays,
  getDate,
} from "date-fns";
const currentDate = new Date();
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
  props: {
    items: Object,
    filters: Object,
    calendar: Array,
    optionsFilters: Object,
  },
  data() {
    return {
      fav: {},
      menu: {},
      viewPort: "calendar",
      headers: [
        { text: "Nombre Equipo", value: "name" },
        { text: "Serie Equipo", value: "equipment_serial" },
        { text: "Tasa Ocupacion %", value: "ocupancy_rate" },
        { text: "Valor Comercial", value: "current_sale_price" },
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
        category_ids: this.filters.category_ids || [],
        year: Number(this.filters.year) || getYear(currentDate),
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
      usedColors: new Set(),
      selectedColor: null,
      selectedLeaseIncome: [],
      breadcrumbs: [{ text: "Inventario Maquinaria", disabled: true }],
    };
  },

  computed: {
    getTotalAnualIncome() {
      const _this = this;
      return this.calendar.reduce(
        (acc, machinery) =>
          acc + _this.getAccumulatedLeaseAmount(machinery.lease_incomes),
        0
      );
    },
    getTotalIncome() {
      return this.calendar.reduce((acc, curr) => acc + curr.total_income, 0);
    },
    getTotalBalance() {
      // return this.calendar.reduce((acc, curr) => acc + curr.total_balance, 0);
      const _this = this;
      return this.calendar.reduce(
        (acc, machinery) =>
          acc + _this.getAccumulatedLeaseFeesAmount(machinery.lease_incomes),
        0
      );
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

    isWithinMonthYearInterval(date, { start, end }) {
      const startOfMonthYear = startOfMonth(start);
      const endOfMonthYear = endOfMonth(end);
      const dateYear = startOfMonth(date);

      return dateYear >= startOfMonthYear && dateYear <= endOfMonthYear;
    },

    isMonthYearInRange(month, startDate, endDate) {
      const yearFilter = this.filters.year || new Date().getFullYear();
      const monthYear = `${month}/${yearFilter}`;
      const parsedMonthYear = parse(monthYear, "MM/yyyy", new Date());

      const parsedStartDate = parse(startDate, "yyyy-MM-dd", new Date());
      const parsedEndDate = parse(endDate, "yyyy-MM-dd", new Date());

      return this.isWithinMonthYearInterval(parsedMonthYear, {
        start: parsedStartDate,
        end: parsedEndDate,
      });
    },
    isDateInMonthYearInRange(date, month, year) {
      const parsedDate = parse(date, "yyyy-MM-dd", new Date());
      return getMonth(parsedDate) + 1 === month && getYear(parsedDate) === year;
    },
    hasRentInRange(month, leases) {
      const _this = this;
      return leases.length > 0
        ? leases.some((lease) =>
            _this.isMonthYearInRange(month, lease.start_date, lease.end_date)
          )
        : false;
    },
    getDaysInMonthRange(month, year, { start, end }) {
      let currentStartMonth = startOfMonth(new Date(year, month - 1, 1));
      // const currentEndMonth = endOfMonth(new Date(year, month - 1, 1));
      const isStartDateInMonthYear =
        getMonth(start) + 1 === month && getYear(start) === year;
      const isEndDateInMonthYear =
        getMonth(end) + 1 === month && getYear(end) === year;
      let daysInMonthElapsed = 0;
      if (isStartDateInMonthYear)
        daysInMonthElapsed =
          differenceInDays(endOfMonth(currentStartMonth), start) - 1;
      else if (isEndDateInMonthYear) daysInMonthElapsed = getDate(end) + 1;
      else {
        daysInMonthElapsed = getDaysInMonth(new Date(year, month - 1, 1));
      }

      return daysInMonthElapsed;
    },
    getLeaseInfo(month, leases) {
      const _this = this;
      let foundLease = leases
        .map((item) => {
          return {
            ...item,
            daysInMonth: _this.getDaysInMonthRange(month, _this.form.year, {
              start: new Date(item.start_date),
              end: new Date(item.end_date),
            }),
          };
        })
        .find((lease) =>
          _this.isMonthYearInRange(month, lease.start_date, lease.end_date)
        );

      return foundLease || false;
    },
    getRentColor(month, leases) {
      const foundLease = leases.find((lease) =>
        this.isMonthYearInRange(month, lease.start_date, lease.end_date)
      );
      return foundLease
        ? this.colors[leases.indexOf(foundLease)]
        : "transparent";
    },
    getAccumulatedLeaseAmount(leases) {
      if (Array.isArray(leases)) {
        return leases.reduce((acumulado, objeto) => {
          if (objeto.lease_fees && Array.isArray(objeto.lease_fees)) {
            const amountIncomeAcumulado = objeto.lease_fees.reduce(
              (subtotal, leaseFee) =>
                subtotal + leaseFee.amount_income * objeto.term_in_days,
              0
            );
            return acumulado + amountIncomeAcumulado;
          }
          return acumulado;
        }, 0);
      }
    },
    getAccumulatedLeaseFeesAmount(leases) {
      if (Array.isArray(leases)) {
        return leases.reduce((acumulado, objeto) => {
          if (objeto.lease_fees && Array.isArray(objeto.lease_fees)) {
            const amountIncomeAcumulado = objeto.lease_fees.reduce(
              (subtotal, leaseFee) => subtotal + leaseFee.amount_income,
              0
            );
            return acumulado + amountIncomeAcumulado;
          }
          return acumulado;
        }, 0);
      }
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

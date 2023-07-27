<template>
  <v-form ref="formLeaseFees" lazy-validation>
    <v-row>
      <v-col cols="12">
        <v-currency-field
          v-model.number="form.amount_income"
          label="Monto del Pago"
          :error-messages="form.errors.amount_income"
          :rules="[
            (v) => !!v || 'Importe Requerido',
          ]"
          :default-value="form.amount_income"
          :allow-negative="false"
          value-as-integer
          prefix="$"
          suffix="MXN"
          outlined
          dense
        />
      </v-col>
      <v-col cols="6">
        <v-text-field
          v-model="form.folio"
          label="Folio Comprobante"
          :error-messages="form.errors.folio"
          outlined
          filled
          dense
        />
      </v-col>
      <v-col cols="6">
        <v-text-field
          v-model="form.payment_date"
          label="Fecha de Pago"
          :rules="[(v) => !!v || 'Fecha Requerida']"
          type="date"
          :error-messages="form.errors.payment_date"
          outlined
          filled
          dense
        />
      </v-col>
      <v-col cols="12">
        <v-textarea
          v-model="form.note"
          label="Nota/Comentario"
          :error-messages="form.errors.note"
          outlined
          filled
        />
      </v-col>
    </v-row>
  </v-form>
</template>
<script>
export default {
  name: "LeaseFeesForm",
  data() {
    return {
      form: this.$inertia.form({
        folio: null,
        amount_income: 0,
        payment_date: null,
        note: "",
        lease_id: null,
      }),
    };
  },

  mounted() {
    const _this = this;
    this.$eventBus.$on("STORE_LEASE_FEES", ({ lease_id }) => {
      _this.store(lease_id);
    });
    this.$eventBus.$on("CLOSE_LEASE_FEES", () => {
      // _this.$refs.formLeaseFees.reset();
      _this.form.reset();
      _this.$refs.formLeaseFees.resetValidation();
    });
  },
  methods: {
    async store(lease_id) {
      const _this = this;
      if (!_this.$refs.formLeaseFees.validate()) return;

      await _this.form
        .transform((data) => ({
          ...data,
          lease_id: lease_id,
        }))
        .post(_this.route("lease_fees.store"), {
          onStart: () => {},
          onFinish: () => {
            _this.form.reset();
            _this.$eventBus.$emit("REFRESH_LEASES_INCOME");
          },
          only: ["item", "flash", "errors"],
          preserveState: true,
           preserveScroll: true,
        });
    },
  },
};
</script>
<style>
</style>
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
        <v-btn @click="create()">Registrar Nuevo Costo Fijo</v-btn>
      </v-col>
    </v-row>

    <v-card-text>
      <data-table-wrapper
        :items="items.data"
        :headers="headers"
        with-search
        sort-by="name"
      >
        <template #item="{ item }">
          <tr>
            <!-- <td>
              {{ item.id }}
            </td> -->
            <td class="text-no-wrap">
              {{ item.name }}
            </td>

            <td class="text-right">
              <v-chip v-if="item.deleted_at" color="warning" outlined x-small>
                Eliminado
              </v-chip>
              <v-btn text icon @click="editItem(item)">
                <v-icon color="blue">mdi-pencil</v-icon>
              </v-btn>
              <v-btn
                v-if="!item.deleted_at"
                text
                icon
                @click="deleteItem(item)"
              >
                <v-icon color="red">mdi-trash-can</v-icon>
              </v-btn>
              <v-btn v-else text icon @click="restore(item)">
                <v-icon color="orange">mdi-delete-restore</v-icon>
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
      route="fixes-costs"
      @input="(v) => (form.page = v)"
    />
    <dialog-modal :show="dialog" max-width="500" persistent @close="close()">
      <template #title> {{ formTitle }} Costo Fijo </template>
      <template #content>
        <v-text-field
          v-model="editedForm.name"
          outlined
          label="Nombre Costo Fijo:"
          :error-messages="editedForm.errors.name"
        />
      </template>
      <template #footer>
        <v-btn block dark :loading="sending" @click="save()">
          {{ formTitle }}
        </v-btn>
      </template>
    </dialog-modal>
    <confirmation-modal
      :show="dialogDelete"
      max-width="500"
      @close="closeDelete"
    >
      <template #title> Confirmar Eliminacion </template>
      <template #content>
        ¿Seguro en Eliminar el Registro <b>"{{ editedForm.name }}"</b> ?
      </template>
      <template #footer>
        <v-spacer />
        <v-btn color="blue darken-1" dark @click="closeDelete">Cancel</v-btn>
        <v-btn color="red darken-1" text @click="deleteItemConfirm">OK</v-btn>
      </template>
    </confirmation-modal>
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
import DialogModal from "@/Shared/DialogModal.vue";
import ConfirmationModal from "@/Shared/ConfirmationModal.vue";

export default {
  name: "FixesCostsIndex",
  metaInfo: { title: "Costos Fijos" },
  // layout: Layout,
  components: {
    DataTableWrapper,
    SearchFilter,
    Pagination,
    Breadcrumbs,
    Layout,
    DialogModal,
    ConfirmationModal,
  },
  props: { items: Object, filters: Object, errors: Object },
  data() {
    return {
      headers: [
        { text: "Nombre Costo Fijo ", value: "name" },
        {
          text: "",
          align: "end",
          width: "250",
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
      breadcrumbs: [{ text: "Costos Fijos", disabled: true }],
      dialog: false,
      dialogDelete: false,
      sending: false,
      editedIndex: -1,
      editedForm: this.$inertia.form({
        name: "",
      }),
    };
  },
  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "Nuevo" : "Editar";
    },
  },

  watch: {
    form: {
      handler: throttle(function () {
        let query = pickBy(this.form);
        this.$inertia.get(
          this.route(
            "fixes-costs",
            Object.keys(query).length ? query : { remember: "forget" }
          ),
          {},
          { preserveState: true }
        );
      }, 2000),
      deep: true,
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null);
      this.form.page = 1;
    },
    create() {
      this.dialog = true;
    },
    editItem(item) {
      this.editedIndex = this.items.data.indexOf(item);
      this.editedForm.id = item.id;
      this.editedForm.name = item.name;
      this.dialog = true;
    },
    close() {
      const _this = this;
      this.dialog = false;
      this.$nextTick(() => {
        _this.editedForm.reset();
        _this.editedForm.clearErrors();
        _this.editedIndex = -1;
      });
    },
    closeDelete() {
      const _this = this;
      this.dialogDelete = false;
      this.$nextTick(() => {
        _this.editedForm.reset();
        _this.editedForm.clearErrors();
        _this.editedIndex = -1;
      });
    },
    save() {
      if (this.editedIndex > -1) {
        this.editedForm.put(
          this.route("fixes-costs.update", this.editedForm.id),
          {
            onStart: () => (this.sending = true),
            onFinish: () => (this.sending = false),
            onSuccess: () => this.close(),
          }
        );
      } else {
        this.editedForm.post(this.route("fixes-costs.store"), {
          onStart: () => (this.sending = true),
          onFinish: () => (this.sending = false),
          onSuccess: () => this.close(),
        });
      }
    },
    deleteItem(item) {
      this.editedIndex = this.items.data.indexOf(item);
      this.editedForm.id = item.id;
      this.editedForm.name = item.name;
      this.dialogDelete = true;
    },
    deleteItemConfirm() {
      this.editedForm.delete(
        this.route("fixes-costs.destroy", this.editedForm.id)
      );
      this.closeDelete();
    },
    restore(_item) {
      if (confirm("Desea Restaurar la Costo Fijo?")) {
        this.$inertia.put(this.route("fixes-costs.restore", _item.id));
      }
    },
  },
};
</script>

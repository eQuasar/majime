<template>
  <b-container fluid>
    <div class="header_title">
      <div class="header_inner">
        <h3><strong>Product List</strong></h3>
        <br />
      </div>
    </div>

    <div class="content_bar card">
      <div class="card-body">
        <div class="call-center-dashboard">
          <b-col xl="10" lg="10" md="10">
            <b-alert show variant="danger" v-if="create_error">{{
              create_error
            }}</b-alert>
            <b-form @submit="onSubmit" class="date_range">
              <div class="datepiker-block">
                <span>From:&nbsp;</span>
                <b-form-datepicker
                  id="from"
                  v-model="date_from"
                  :date-format-options="{
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                  }"
                  locale="en-IN"
                ></b-form-datepicker>
              </div>
              <div class="datepiker-block">
                <span>To:&nbsp;</span>
                <b-form-datepicker
                  id="to"
                  v-model="date_to"
                  :date-format-options="{
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                  }"
                  locale="en-IN"
                ></b-form-datepicker>
              </div>
              <b-button type="submit" variant="primary">Submit</b-button>
            </b-form>
          </b-col>
          <b-col>
            <button
              type="button"
              class="download-btn btn btn-primary"
              v-on:click="download"
            >
              Download
            </button>
          </b-col>
          <div class="blue-bar"></div>
          <div class="content_bar card list-appointments space-bottom">
            <div class="col-sm-12">
              <b-row>
                <b-col xl="5" lg="5" md="5">
                  <b-form-group class="mb-0">
                    Show
                    <b-form-select
                      id="per-page-select"
                      v-model="perPage"
                      :options="pageOptions"
                      size="sm"
                    ></b-form-select>
                    entries
                  </b-form-group>
                </b-col>
                <b-col xl="7" lg="7" md="7" class="search_field">
                  <b-form-input
                    id="filter-input"
                    v-model="filter"
                    type="search"
                    placeholder="Type to Search"
                  ></b-form-input>
                  <b-input-group-append>
                    <b-button :disabled="!filter" @click="filter = ''"
                      >Clear</b-button
                    >
                  </b-input-group-append>
                </b-col>
              </b-row>
            </div>
          </div>
        </div>
        <b-table
          striped
          hover
          responsive
          :items="items"
          :sort-by.sync="sortBy"
          :sort-desc.sync="sortDesc"
          sort-icon-left
          :filter-included-fields="filterOn"
          :filter="filter"
          :fields="fields"
          :per-page="perPage"
          :current-page="currentPage"
          show-empty
        >
          <template #empty="scope">
            <p style="text-align: center">
              No record found, choose date filter to found the result.
            </p>
          </template>
          <template v-slot:cell(oid)="row">
            {{ row.item.Order_id }}
          </template>

          <template v-slot:cell(size)="row">
            {{ row.item.name }}
          </template>
          <template v-slot:cell(color)="row">
            {{ row.item.name }}
          </template>
        </b-table>
        <div class="text-center" v-if="seen">
          <b-spinner variant="primary" label="Text Centered"></b-spinner>
        </div>
        <b-pagination
          v-model="currentPage"
          :total-rows="rows"
          :per-page="perPage"
          aria-controls="my-table"
        ></b-pagination>
      </div>
    </div>
  </b-container>
</template>

<script>
import order from "../../api/order.js";
import product_list from "../../api/Product.js";
import user from "../../api/user.js";
import * as XLSX from "xlsx/xlsx.mjs";

export default {
  props: {},
  mounted() {
    this.getVidz();
  },
  data() {
    return {
      ariaDescribedby: "",
      time: "",
      date: "",
      time_slots: [],
      seen: false,
      date_from: "",
      vid: 0,
      date_to: "",
      sortBy: "date",
      sortDesc: true,
      perPage: 10,
      currentPage: 1,
      pageOptions: [5, 10, 15, 20, 50, 100],
      filter: null,
      filterOn: [],
      fields: [
        {
          key: "product_id",
          label: "Product Id",
          sortable: true,
        },

        {
          key: "name",
          label: "Name",
          sortable: true,
        },
        {
          key: "quantity",
          label: "Quantity",
          sortable: true,
        },
        {
          key: "categories",
          label: "Category",
          sortable: true,
        },
        {
          key: "size",
          label: "Size",
          sortable: true,
        },
        {
          key: "color",
          label: "Color",
          sortable: true,
        },
        {
          key: "price",
          label: "Amount",
          sortable: true,
        },
      ],
      items: [],
      errors_create: [],
      successful: false,
      create_error: "",
    };
  },
  computed: {
    rows() {
      return this.items.length;
    },
  },
  methods: {
    onSubmit(event) {
      event.preventDefault();
      this.create_error = "";
      if (!this.date_from) {
        this.create_error += "Add date from,";
      }
      if (!this.date_to) {
        this.create_error += "Add date to,";
      }
      if (this.create_error != "") {
        return false;
      }
      let formData = new FormData();
      formData.append("date_from", this.date_from);
      formData.append("date_to", this.date_to);

      appointment
        .appointmentSearch(formData)
        .then((response) => {
          this.items = response.data.data;
        })
        .catch((error) => {
          console.log(error);
          if (error.response.status == 422) {
            this.errors_create = error.response.data.errors;
          }
        });
    },

    getVidz() {
      let formData = new FormData();
      formData.append("user_id", this.$userId);
      user
        .getVid(formData)
        .then((response) => {
          this.vid = response.data;
          console.log(response);
          this.ProductDetail();
        })
        .catch((response) => {
          this.successful = false;
          alert("something went wrong");
        });

      // alert("aaa");
    },

    ProductDetail() {
      let formData = new FormData();
      formData.append("vid", this.vid);
      product_list
        .getProductSaleDetail(formData)
        .then((response) => {
          console.log(response);
          this.items = response.data;
          console.log(this.items);
        })
        .catch((response) => {
          this.successful = false;
          alert("something went wrong");
        });
    },
    download: function () {
      const data = XLSX.utils.json_to_sheet(this.items);
      const wb = XLSX.utils.book_new();
      XLSX.utils.book_append_sheet(wb, data, "data");
      XLSX.writeFile(wb, "download_list.xlsx");
    },
  },
};
</script>

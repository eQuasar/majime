<template>
  <b-container fluid>
    <div class="header_title">
      <div class="header_inner">
        <b-row>
          <b-col xl="4" lg="4" md="4">
            <h3><strong>Zone Rate Card View Detail</strong></h3>
            <br />
          </b-col>
        </b-row>
      </div>
    </div>
    <div class="content_bar">
      <div class="card-body card">
        <div class="call-center-dashboard">
          <b-row>
            <b-col xl="6" lg="" md="6">
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
            <b-col xl="3" lg="3" md="3">
              <button
                type="button"
                class="download-btn btn btn-primary"
                v-on:click="download"
              >
                Download
              </button>
            </b-col>
          </b-row>
        </div>
      </div>
      <div class="blue-bar"></div>
      <div class="card list-appointments space-bottom">
        <div class="col-sm-12">
          <b-row>
            <b-col xl="4" lg="4" md="4">
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
            <b-col xl="8" lg="8" md="8" class="search_field">
              <b-form-input
                id="filter-input"
                v-model="filter"
                type="search"
                placeholder="Type to Search"
              >
              </b-form-input>
              <b-input-group-append>
                <b-button :disabled="!filter" @click="filter = ''"
                  >Clear</b-button
                >
              </b-input-group-append>
            </b-col>
          </b-row>
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
        class="tbl-blk"
      >
        <template #empty="scope">
          <p style="text-align: center">
            No record found, choose date filter to found the result.
          </p>
        </template>

        <template v-slot:cell(oid)="row"> #{{ row.item.id }} </template>
        <template v-slot:cell(action)="row">
          <router-link
            :to="{
              name: 'OrderProfile',
              params: { id: row.item.id.toString() },
            }"
            ><b-icon icon="eye-fill" aria-hidden="true"></b-icon
          ></router-link>
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
      >
      </b-pagination>
    </div>
  </b-container>
</template>
<script>
import viewTran from "../../api/TransactionDetail.js";
import user from "../../api/user.js";
import * as XLSX from "xlsx/xlsx.mjs";

export default {
  props: {},
  mounted() {
    //this.getVidz();
    this.zonerateDetails();
  },
  data() {
    return {
      vendor: null,
      status_assign: "",
      key: "",
      oid: 0,
      selected: null,
      options: [{ value: null, text: "Vendor Wise Detail" }],
      ariaDescribedby: "",
      time: "",
      date: "",
      vid: 0,
      time_slots: [],
      status_assign_array: [],
      seen: false,
      date_from: "",
      city: "",
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
          key: "id",
          label: "Id",
          sortable: true,
        },
        {
          key: "zoneno",
          label: "Zone No",
          sortable: true,
        },
        {
          key: "fwd",
          label: "Forword",
          sortable: true,
        },
        {
          key: "dto",
          label: "DTO",
          sortable: true,
        },
        {
          key: "rto",
          label: "RTO",
          sortable: true,
        },

        // {
        //   key: 'action',
        //   label: 'Action',
        //   sortable: false
        // }
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
    zonerateDetails(vid) {
      let formData = new FormData();
      formData.append("vid", vid);
      formData.append("type", "get");
      // formData.append('Description', 'description');
      // formData.append('Transaction Type', 'type');
      // formData.append('Amount', 'amount');
      // formData.append('Date', 'date');
      viewTran
        .zonerate(formData)
        .then((response) => {
          console.log(response);
          this.items = response.data;
          // this.countrys = response.data.data;
          console.log(this.items);
          //this.seen = false;
        })
        .catch((response) => {
          this.successful = false;
          alert("something went wrong");
        });
    },
    onSubmit(event) {
      event.preventDefault();
      console.log(this.date_from);
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
      view_Transaction;
      formData.append("date_to", this.date_to);

      viewTran
        .ViewTras(formData)
        .then((response) => {
          this.items = response.data;
          console.log(this.items);

          // }
        })
        .catch((error) => {
          console.log(error);
          if (error.response.status == 422) {
            this.errors_create = error.response.data.errors;
          }
          // loader.hide();
        });
    },

    // getVidz()
    //    {
    //     let formData= new FormData();
    //     formData.append("user_id", this.$userId);
    //     user.getVid(formData)
    //      .then(( response ) => {view_Transaction
    //         this.vid = response.data;
    //         localStorage.setItem("ivid", this.vid);
    //         this.getOrderDetails(this.vid);
    //       })
    //       .catch(response => {
    //           this.successful = false;
    //           alert('something went wrong');
    //       })

    //     // alert("aaa");
    //    },

    download: function () {
      const data = XLSX.utils.json_to_sheet(this.items);
      const wb = XLSX.utils.book_new();
      XLSX.utils.book_append_sheet(wb, data, "data");
      XLSX.writeFile(wb, "download_list.xlsx");
    },
  },
};
</script>

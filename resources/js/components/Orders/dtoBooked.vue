<template>
  <b-container fluid>
    <b-overlay
      :show="show"
      rounded="sm"
      spinner-type="grow"
      spinner-variant="primary"
      spinner-small
    >
      <div class="header_title">
        <div class="header_inner">
          <h2><strong>DTO-Booked</strong></h2>
        </div>
      </div>
      <div class="clear">&nbsp;</div>
      <b-row>
        <b-col xl="12" lg="12" md="12">
          <div class="list-appointments content_bar">
            <div class="card-body card">
              <b-row>
                <div class="select-list"></div>
                <b-col>
                  <button
                    type="button"
                    class="download-btn btn btn-primary"
                    v-on:click="pendingdownload"
                    style="margin-left: 15px"
                  >
                    Download
                  </button>
                </b-col>
              </b-row>
            </div>
            <br />
            <div class="groomer-page">
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
                <template #head(select)="data">
                  <span class="text-info"
                    ><input
                      type="checkbox"
                      @click="selectedAll"
                      v-model="allSelected"
                    />&nbsp;{{ data.label }}</span
                  >
                </template>
                <template #empty="scope">
                  <p style="text-align: center">
                    No record found, choose date filter to found the result.
                  </p>
                </template>
                <template v-slot:cell(sr)="row">
                  {{ (currentPage - 1) * perPage + row.index + 1 }}
                </template>
                <template v-slot:cell(select)="row">
                  <input
                    type="checkbox"
                    :value="row.item.oid"
                    v-model="allSelected"
                  />
                </template>
                <template v-slot:cell(action)="row">
                  <p class="h3 mb-2">
                    <!-- <b-link @click="RefundstatusOID(row.item.oid)"><b-icon icon="arrow-counterclockwise" animation="spin-reverse" variant="primary" aria-hidden="true" data-toggle="tooltip" title="Refund Status" v-model="statusAssign"></b-icon></b-link> -->&nbsp;&nbsp;<router-link
                      :to="{
                        name: 'OrderProfile',
                        params: { oid: row.item.oid.toString() },
                      }"
                      ><b-icon icon="eye-fill" aria-hidden="true"></b-icon
                    ></router-link>
                  </p>
                </template>
              </b-table>
            </div>
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
        </b-col>
      </b-row>
    </b-overlay>
  </b-container>
</template>

<script>
import Returndto from "../../api/returndto.js";
import order from "../../api/order.js";
import user from "../../api/user.js";
import * as XLSX from "xlsx/xlsx.mjs";

export default {
  props: {},
  mounted() {
    this.getVidz();
    //this.getPackdetailRefund();
  },
  data() {
    return {
      vendor: null,
      oid: 0,
      //selected: null,
      ariaDescribedby: "",
      time: "",
      date: "",
      vid: 0,
      time_slots: [],
      status_assign_array: [],
      allSelected: false,
      seen: false,
      date_from: "",
      date_to: "",
      // statusAssign:'',
      sortBy: "date",
      sortDesc: true,
      perPage: 10,
      currentPage: 1,
      allSelected: [],
      selectall: [],
      status: "",
      pageOptions: [5, 10, 15, 20, 50, 100],
      filter: null,
      filterOn: [],
      fields: [
        {
          key: "select",
          label: "",
          sortable: true,
        },

        {
          key: "oid",
          label: "Order ID",
          sortable: true,
        },
        {
          key: "date_created_gmt",
          label: "Date",
          sortable: true,
        },
        {
          key: "first_name",
          label: "First Name",
          sortable: true,
        },
        {
          key: "last_name",
          label: "Last Name",
          sortable: true,
        },
        {
          key: "phone",
          label: "Contact",
          sortable: true,
        },
        {
          key: "email",
          label: "Email",
          sortable: true,
        },
        {
          key: "payment_method",
          label: "Payment",
          sortable: true,
        },
        {
          key: "action",
          label: "Action",
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
    async selectedAll() {
      if (this.allSelected) {
        this.selectall = [];
      } else {
        const selected = this.items.map((u) => u.oid);
        this.selectall = selected;
      }
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
      formData.append("date_to", this.date_to);
      order
        .orderSearch(formData)
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

    getVidz() {
      if (this.$userId == 1) {
        this.vid = JSON.parse(localStorage.getItem("ivid"));
        localStorage.setItem("ivid", this.vid);
        this.getstatus(this.vid);
      } else {
        let formData = new FormData();
        formData.append("user_id", this.$userId);
        user
          .getVid(formData)
          .then((response) => {
            this.vid = response.data;
            localStorage.setItem("ivid", this.vid);
            this.getstatus(this.vid);
          })
          .catch((response) => {
            this.successful = false;
            //alert('something went wrong');
          });
      }
      // alert("aaa");
    },

    getstatus(vid) {
      Returndto.get_Statusdto(vid, "dtobooked")
        .then((response) => {
          if (response.data) {
            this.items = response.data;
          }
        })
        .catch((error) => {
          console.log(error);
        });
      console.log("Pack - 3");
    },

    clearData() {
      this.oid = "";
    },
    pendingdownload() {
      this.show = true;
      let formData = new FormData();
      formData.append("allSelected", this.allSelected);
      formData.append("vid", this.vid);
      formData.append("status", "dtobooked");
      order
        .Pending_downloadsheet(formData)
        .then((response) => {
          console.log(response.data[0]);
          this.items2 = response.data[0];
          const data = XLSX.utils.json_to_sheet(this.items2);
          const wb = XLSX.utils.book_new();
          XLSX.utils.book_append_sheet(wb, data, "data");
          XLSX.writeFile(wb, "DtoBooked_orders.xlsx");
        })
        .catch((error) => {
          console.log(error);
          if (error.response.status == 422) {
            this.errors_create = error.response.data.errors;
          }
          // loader.hide();
        });
      this.show = false;
    },
  },
};
</script>

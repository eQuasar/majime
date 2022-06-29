<template>
  <b-container fluid>
    <b-overlay :show="show" rounded="sm">
      <div class="header_title">
        <div class="header_inner">
          <h2><strong>Pending Refunds</strong></h2>
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
                  <button
                    type="button"
                    class="download-btn btn btn-primary"
                    v-on:click="Refundstatus"
                  >
                    Refund
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
                    <b-link @click="RefundstatusOID(row.item.oid)"
                      ><b-icon
                        icon="arrow-counterclockwise"
                        animation="spin-reverse"
                        variant="primary"
                        aria-hidden="true"
                        data-toggle="tooltip"
                        title="Refund Status"
                        v-model="statusAssign"
                      ></b-icon></b-link
                    >&nbsp;&nbsp;<router-link
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
      <b-modal id="modal-1" title="Change Status:" hide-footer size="lg">
        <b-form>
          <b-alert show variant="danger" v-if="create_error">{{
            create_error
          }}</b-alert>
          <div
            :class="['form-group m-1 p-3', successful ? 'alert-success' : '']"
            v-show="successful"
          >
            <span v-if="successful" class="label label-sucess">Published!</span>
          </div>
          <b-form-input
            v-model="oid"
            id="oid_val"
            style="display: none"
          ></b-form-input>

          <b-form-select v-model="status_assign">
            <template v-slot:first>
              <b-form-select-option value="null" disabled selected
                >-- Select Status --</b-form-select-option
              >
            </template>
            <b-form-select-option value="dtobooked"
              >Change status to DTO Booked</b-form-select-option
            >
            <b-form-select-option value="dispatched"
              >Change status to Dispatched</b-form-select-option
            >
            <b-form-select-option value="intransit"
              >Change status to Intransit</b-form-select-option
            >
            <b-form-select-option value="confirmed"
              >Change status to Confirmed</b-form-select-option
            >
            <b-form-select-option value="processing"
              >Change status to Processing</b-form-select-option
            >
            <b-form-select-option value="completed"
              >Change status to Completed</b-form-select-option
            >
            <b-form-select-option value="cancelled"
              >Change status to Cancelled</b-form-select-option
            >
            <b-form-select-option value="packed"
              >Change status to Packed</b-form-select-option
            >
            <b-form-select-option value="rtd-online"
              >Change status to RTD-Online</b-form-select-option
            >
            <b-form-select-option value="rtd-cod"
              >Change status to RTD-COD</b-form-select-option
            >
            <b-form-select-option value="refunded"
              >Change status to Refunded</b-form-select-option
            >
            <b-form-select-option value="deliveredtocust"
              >Change status to delivered to customer</b-form-select-option
            >
            <b-form-select-option value="dtodel2warehouse"
              >Change status to DTO Delivery to Warehouse</b-form-select-option
            >
            <b-form-select-option value="dtointransit"
              >Change status to DTO Intransit</b-form-select-option
            >
            <b-form-select-option value="pickedup"
              >Change status to Picked up</b-form-select-option
            >
            <b-form-select-option value="on-hold"
              >Change status to on-hold</b-form-select-option
            >
          </b-form-select>
          <b-button
            type="submit"
            @click.prevent="assign_status"
            variant="primary"
            >Submit</b-button
          >
        </b-form>
      </b-modal>
    </b-overlay>
  </b-container>
</template>

<script>
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
      show: false,
      vendor: null,
      status_assign: "",
      oid: 0,
      //selected: null,
      options: [{ value: null, text: "Vendor Wise Detail" }],
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
      statusAssign: "",
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
    getvendor() {
      let formData = new FormData();
      formData.append("vendor", this.vendor);
      formData.append("type", "get");
      console.log(this.vendor);
      order
        .getOrderDetails(formData)
        .then((response) => {
          console.log(response);
          this.items = response.data;
          // this.countrys = response.data.data;
          console.log(this.items);
          //this.seen = false;
        })
        .catch((response) => {
          this.successful = false;
          //alert('something went wrong');
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
        this.getPackdetailRefund(this.vid);
      } else {
        let formData = new FormData();
        formData.append("user_id", this.$userId);
        user
          .getVid(formData)
          .then((response) => {
            this.vid = response.data;
            localStorage.setItem("ivid", this.vid);
            this.getPackdetailRefund(this.vid);
          })
          .catch((response) => {
            this.successful = false;
            //alert('something went wrong');
          });
      }
      // alert("aaa");
    },

    getPackdetailRefund(vid) {
      order
        .getPackdetail_Refund(vid)
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

    Refundstatus() {
      if (this.allSelected != "") {
        this.addstatus();
      } else {
        alert("Please choose at least one value from checkbox...");
      }
    },
    RefundstatusOID(oid) {
      this.oid = oid;
      this.allSelected = false;
      this.addstatus();
    },
    addstatus() {
      const modalTimeoutSeconds = 3;
      const modalId = "confirm-modal";
      let modalSetTimeout = null;
      this.$bvModal
        .msgBoxConfirm(`Are You Sure Want to Change Refund Status`, {
          id: modalId,
        })
        .then((wasOkPressed) => {
          this.show = true;
          if (wasOkPressed) {
            this.vid = JSON.parse(localStorage.getItem("ivid"));
            let formData = new FormData();
            formData.append("oid", this.oid);
            formData.append("status_assign", "refunded");
            formData.append("allSelected", this.allSelected);
            formData.append("vid", this.vid);
            formData.append("status", this.status);
            order
              .changeProcessingStatus(formData)
              .then((response) => {
                alert("Status Update Successfully");
                this.show = false;
                this.getVidz();
              })
              .catch((error) => {
                // console.log(error);
                if (error.response.status == 422) {
                  this.errors_create = error.response.data.errors;
                }
                // loader.hide();
              });
          } else {
            /* Do something else */
          }
        })
        .catch(() => {
          console.log("The modal closed unexpectedly");
        })
        .finally(() => {
          clearTimeout(modalSetTimeout);
        });

      modalSetTimeout = setTimeout(() => {
        this.$bvModal.hide(modalId);
      }, modalTimeoutSeconds * 2000);
    },
    Pendingassign_status() {
      this.vid = JSON.parse(localStorage.getItem("ivid"));
      let formData = new FormData();
      formData.append("selectall", this.selectall);
      formData.append("status", this.status);
      formData.append("vid", this.vid);
      order
        .PendingRefund_changeStatus(formData)
        .then((response) => {
          alert(response.data.msg);
          window.location.reload();
        })
        .catch((response) => {
          this.successful = false;
          //alert('something went wrong');
        });
    },

    // check(event)
    // {
    //   alert(this.allSelected);
    //              // this.oid += oid;
    //              // let formData= new FormData();
    //              //  formData.append("oid", this.oid);
    //              //this.$bvModal.show("modal-1");
    //              // console.log(this.oid);
    // },
    clearData() {
      this.oid = "";
    },
    pendingdownload() {
      this.show = true;
      let formData = new FormData();
      formData.append("allSelected", this.allSelected);
      formData.append("vid", this.vid);
      formData.append("status", "dtodel2warehouse");
      order
        .Pending_downloadsheet(formData)
        .then((response) => {
          console.log(response.data[0]);
          this.items2 = response.data[0];
          const data = XLSX.utils.json_to_sheet(this.items2);
          const wb = XLSX.utils.book_new();
          XLSX.utils.book_append_sheet(wb, data, "data");
          XLSX.writeFile(wb, "DtoDel2warehouse_orders.xlsx");
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

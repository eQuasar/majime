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
          <h3><strong>Ready to Pack</strong></h3>
          <br />
        </div>
      </div>
      <div class="content_bar">
        <div class="card-body card">
          <div class="call-center-dashboard">
            <b-row>
              <b-col xl="6" lg="6" md="6" class="search_field">
                <b-form-input
                  id="filter-input"
                  v-model="filter"
                  type="search"
                  placeholder="Type to Search"
                ></b-form-input>
                <b-input-group-append
                  ><b-button :disabled="!filter" @click="filter = ''"
                    >Clear</b-button
                  ></b-input-group-append
                >
              </b-col>
              <b-col xl="6" lg="6" md="6">
                <button
                  type="button"
                  class="download-btn btn btn-primary"
                  v-on:click="confirmAssignAWB"
                  style="margin-left: 15px"
                >
                  Assign AWB
                </button>
                <button
                  type="button"
                  class="download-btn btn btn-primary"
                  v-on:click="Confirmdownload"
                >
                  Download Pickup List
                </button>
              </b-col>
            </b-row>
            <div class="blue-bar"></div>
            <div class="content_bar card list-appointments space-bottom">
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
                </b-row>
              </div>
            </div>
          </div>
        </div>
        <br />
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
                v-model="selectallcheckbox"
              />&nbsp;{{ data.label }}</span
            >
          </template>
          <template #empty="scope">
            <p style="text-align: center">
              No record found, choose date filter to found the result.
            </p>
          </template>
          <template v-slot:cell(oid)="row"> #{{ row.item.oid }} </template>
          <template v-slot:cell(sr)="row">
            {{ (currentPage - 1) * perPage + row.index + 1 }}
          </template>
          <template v-slot:cell(name)="row">
                {{(row.item.first_name)}} {{(row.item.last_name)}} 
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
              <b-link @click="assignAWBOrder(row.item.oid, row.index)"
                ><b-icon
                  icon="truck"
                  aria-hidden="true"
                  data-toggle="tooltip"
                  title="Assign AWB Number"
                ></b-icon
              ></b-link>
              &nbsp;&nbsp;<router-link
                :to="{
                  name: 'OrderProfile',
                  params: { oid: row.item.oid.toString() },
                }"
                ><b-icon icon="eye-fill" aria-hidden="true"></b-icon
              ></router-link>
              &nbsp;<b-link @click="cancelstatusOID(row.item.oid)"
                ><b-icon
                  icon="x-square-fill"
                  variant="primary"
                  aria-hidden="true"
                  data-toggle="tooltip"
                  title="Cancel Status"
                  v-model="statusAssign"
                ></b-icon
              ></b-link>
            </p>
          </template>

          <b-modal id="modal-1" title="Change Status:" hide-footer size="lg">
            <b-form>
              <b-alert show variant="danger" v-if="create_error">{{
                create_error
              }}</b-alert>
              <div
                :class="[
                  'form-group m-1 p-3',
                  successful ? 'alert-success' : '',
                ]"
                v-show="successful"
              >
                <span v-if="successful" class="label label-sucess"
                  >Published!</span
                >
              </div>
              <b-form-input
                v-model="oid"
                id="oid_val"
                style="display: none"
              ></b-form-input>
              <b-form-select
                v-model="status_assign"
                class=""
                :options="status_assign_array"
                value-field="id"
                text-field="vehicle_number"
              >
                <template v-slot:first>
                  <b-form-select-option :value="0" disabled
                    >-- Select Status --</b-form-select-option
                  >
                </template>
              </b-form-select>
              <b-button type="submit" @click.prevent="s" variant="primary"
                >Submit
              </b-button>
            </b-form>
          </b-modal>
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
  },
  data() {
    return {
      show: false,
      vendor: null,
      status_assign: 0,
      oid: 0,
      statusAssign: "",
      selected: null,
      options: [{ value: null, text: "Vendor Wise Detail" }],
      ariaDescribedby: "",
      time: "",
      date: "",
      vid: 0,
      time_slots: [],
      status_assign_array: [],
      selectall: [],
      allSelected: false,
      selectallcheckbox: false,
      seen: false,
      date_from: "",
      date_to: "",
      sortBy: "date",
      sortDesc: true,
      perPage: 10,
      allSelected: [],
      currentPage: 1,
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
          key: 'oid',
          label: 'Order ID',
          sortable: true
        },
        {
          key: 'date_created_gmt',
          label: 'Order Date ',
          sortable: true
        },
        {
          key: 'total',
          label: 'Amount',
          sortable: true
        },
        {
          key: 'name' ,
          label: 'Name',
          sortable: true
        },
        {
          key: 'state',
          label: 'State',
          sortable: true
        },
        {
          key: 'city',
          label: 'City',
          sortable: true
        },
        {
          key: 'phone',
          label: 'Contact',
          sortable: false
        },
        {
          key: 'payment_method_title',
          label: 'Payment Mode',
          sortable: false
        },
        {
          key: 'status',
          label: 'Status',
          sortable: true
        },
        {
          key: 'action',
          label: 'Action',
          sortable: false
        }
      ],
      items: [],
      packedItems: [],
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
      if (this.selectallcheckbox) {
        this.allSelected = [];
      } else {
        const selected = this.items.map((u) => u.oid);
        this.allSelected = selected;
      }
    },
    getPackdetail(vid) {
      order
        .getPackDetail(vid)
        .then((response) => {
          if (response.data) {
            this.packedItems = response.data;
          }
        })
        .catch((error) => {
          console.log(error);
        });
      console.log("Pack - 3");
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
        order
          .getOrderOnStatus(this.vid, "confirmed")
          .then((response) => {
            if (response.data) {
              this.items = response.data;
            }
          })
          .catch((error) => {
            console.log(error);
          });
      } else {
        let formData = new FormData();
        formData.append("user_id", this.$userId);
        user
          .getVid(formData)
          .then((response) => {
            this.vid = response.data;
            localStorage.setItem("ivid", this.vid);
            order
              .getOrderOnStatus(this.vid, "confirmed")
              .then((response) => {
                if (response.data) {
                  this.items = response.data;
                }
              })
              .catch((error) => {
                console.log(error);
              });
          })
          .catch((response) => {
            this.successful = false;
            alert("something went wrong");
          });
      }
    },
    confirmAssignAWB() {
      if (this.allSelected != "") {
        this.assignAWB();
      } else {
        this.$alert("", "Please choose at least one value from checkbox...");
      }
    },
    assignAWB() {
      // alert('Assigned Successfully');
      this.show = true;
      this.vid = JSON.parse(localStorage.getItem("ivid"));
      let formData = new FormData();
      // formData.append("oid", this.oid);
      formData.append("allSelected", this.allSelected);
      formData.append("vid", this.vid);
      order
        .assignAWB(formData)
        .then((response) => {
          this.$alert("", response.data.msg);
          this.show = false;
          this.getVidz();
        })
        .catch((response) => {
          this.successful = false;
          alert("something went wrong");
        });
    },
    assignAWBOrder(oid, index) {
      this.show = true;
      // alert('Assigned Successfully');
      this.vid = JSON.parse(localStorage.getItem("ivid"));
      let formData = new FormData();
      formData.append("vid", this.vid);
      formData.append("oid", oid);
      order
        .assignAWBOrder(formData)
        .then((response) => {
          this.$alert("", response.data.msg);
          this.show = false;
          this.getVidz();
          // this.items.splice(this.items.indexOf(index), 1);
        })
        .catch((response) => {
          this.successful = false;
          alert("something went wrong");
        });
    },

    cancelstatusOID(oid) {
      this.oid = oid;
      this.allSelected = false;
      this.cancelstatus();
    },
    cancelstatus() {
      const modalTimeoutSeconds = 3;
      const modalId = "confirm-modal";
      let modalSetTimeout = null;
      this.$bvModal
        .msgBoxConfirm(`Are You Sure Want to Change Cancel Status`, {
          id: modalId,
        })
        .then((wasOkPressed) => {
          this.show = true;
          if (wasOkPressed) {
            this.vid = JSON.parse(localStorage.getItem("ivid"));
            let formData = new FormData();
            formData.append("oid", this.oid);
            formData.append("status_assign", "cancelled");
            formData.append("allSelected", this.allSelected);
            formData.append("vid", this.vid);
            formData.append("status", this.status);
            order
              .changeProcessingStatus(formData)
              .then((response) => {
                this.$alert("", response.data.msg);
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
            this.show = false;
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
    clearData() {
      this.oid = "";
    },
    //       download : function() {
    //             const data = XLSX.utils.json_to_sheet(this.packedItems)
    //             const wb = XLSX.utils.book_new()
    //             XLSX.utils.book_append_sheet(wb, data, 'data')
    //             XLSX.writeFile(wb,'readytopack_orders.xlsx')
    //         },
    //   },

    // };
    Confirmdownload() {
      this.show = true;
      let formData = new FormData();
      formData.append("allSelected", this.allSelected);
      formData.append("vid", this.vid);
      order
        .Confirm_downloadsheet(formData)
        .then((response) => {
          console.log(response.data[0]);
          this.items2 = response.data[0];
          const data = XLSX.utils.json_to_sheet(this.items2);
          const wb = XLSX.utils.book_new();
          XLSX.utils.book_append_sheet(wb, data, "data");
          XLSX.writeFile(wb, "PickupList.xlsx");
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

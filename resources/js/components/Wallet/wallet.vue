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
          <h4><strong>Wallet</strong></h4>
          <br />
        </div>
      </div>

      <div class="status-block">
        <div class="stats">
          <h4>In Transit Orders</h4>
          <span>Total Orders: {{ stats.inTransitCount }}</span>
          <p><i>₹ </i>{{ stats.inTransitSaleAmount }}</p>
        </div>

        <div class="stats">
          <h4>Upcoming Payments</h4>
          <span>Total Orders: {{ stats.unProcessedCount }}</span>
          <p><i>₹ </i>{{ stats.unProcessedSaleAmount }}</p>
        </div>

        <div class="stats">
          <h4>Next Remittance</h4>
          <span>Due Date: {{ stats.nextDate }}</span>
          <p><i>₹ </i>{{ stats.dueAmount }}</p>
        </div>

        <div class="stats">
          <h4>Pending Dispatch</h4>
          <span>Total Orders: {{ stats.pendingDispatch }}</span>
          <p><i>₹ </i>{{ stats.pendingDispatchAmount }}</p>
        </div>
      </div>
      <br />
      <div class="content_bar">
        <div class="card-body card">
          <div class="call-center-dashboard">
            <b-row>
              <b-col xl="8" lg="8" md="8" class="tbl-blk">
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
              <b-col xl="4" lg="4" md="4" class="search_field">
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
            </b-row>
          </div>
        </div>
        <br />
        <div class="card-body card right-blk">
          <div class="balance">
            Opening Balance<span>{{ values.opening_bal }}</span>
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
            class="tbl-blk"
          >
            <template #empty="scope">
              <p style="text-align: center">
                No record found, choose date filter to found the result.
              </p>
            </template>

            <template v-slot:cell(created_at)="row">
              <div class="my_tag">
                <span v-for="tag in row.item.created_at.split(' ')" :tag="tag">
                  {{ tag }}
                </span>
              </div>
            </template>

            <template v-slot:cell(oid)="row">
              {{ row.item.Order_id }}
            </template>
            <template v-slot:cell(status)="row">
              <span :class="row.item.status"> {{ row.item.status }}</span>
            </template>
          </b-table>
          <br /><br />
          <div class="balance">
            Closing Balance<span>{{ values.closing_bal }}</span>
          </div>
        </div>
        <div class="text-center" v-if="seen">
          <b-spinner variant="primary" label="Text Centered"></b-spinner>
        </div>
        <br />
        <div class="card-body card">
          <div class="list-appointments">
            <div class="col-sm-12">
              <b-row>
                <b-col xl="6" lg="6" md="6">
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
                <b-col xl="6" lg="6" md="6">
                  <b-pagination
                    v-model="currentPage"
                    :total-rows="rows"
                    :per-page="perPage"
                    aria-controls="my-table"
                  ></b-pagination>
                </b-col>
              </b-row>
            </div>
          </div>
        </div>
      </div>
    </b-overlay>
  </b-container>
</template>
<script>
import wallet from "../../api/wallet.js";
import user from "../../api/user.js";
import order from "../../api/order.js";
export default {
  props: {},
  mounted() {
    this.getVidz();
  },
  data() {
    return {
      //ariaDescribedby: "",
      show: false,
      tag: [],
      time: "",
      date: "",
      oid: "",
      time_slots: [],
      seen: false,
      date_from: "",
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
          key: "created_at",
          label: "Date",
          sortable: true,
        },
        {
          key: "id",
          label: "Txn Id",
          sortable: true,
        },
        {
          key: "orderno",
          label: "Order Id",
          sortable: true,
        },
        {
          key: "payment_mode",
          label: "Pay Mode",
          sortable: true,
        },
        {
          key: "status",
          label: "Status",
          sortable: true,
        },
        {
          key: "sale_amount",
          label: "Sale Amount",
          sortable: true,
        },
        {
          key: "Wallet_used",
          label: "Wallet Used",
          sortable: true,
        },
        {
          key: "logistic_cost",
          label: "Logistic Cost",
          sortable: true,
        },
        {
          key: "payment_gateway_charges",
          label: "Gateway Charges",
          sortable: true,
        },
        {
          key: "sms_cost",
          label: "SMS Cost",
          sortable: true,
        },
        {
          key: "majime_charges",
          label: "Majime Charges",
          sortable: true,
        },
        {
          key: "net_amount",
          label: "Net Amount",
          sortable: true,
        },
        {
          key: "current_wallet_bal",
          label: "Wallet Balance",
          sortable: true,
        },

        // {
        //   key: "starting_amount",
        //   label: "Closing Balance",
        //   sortable: true,
        // },
      ],
      items: [],
      values: [],
      stats: [],
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
      this.show = true;
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
      formData.append("vid", this.vid);
      order
        .walletSearch(formData)
        .then((response) => {
          var resp = response.data;
          this.items = resp.order;
          this.values = resp;
          this.show = false;
          console.log(this.values);
        })
        .catch((error) => {
          console.log(error);
          if (error.response.status == 422) {
            this.errors_create = error.response.data.errors;
          }
        });
    },

    getVidz() {
      if (this.$userId == 1) {
        this.show = true;
        this.vid = JSON.parse(localStorage.getItem("ivid"));
        localStorage.setItem("ivid", this.vid);
        this.getWalletDetail(this.vid);
        this.getStats(this.vid);
        this.show = false;
      } else {
        this.show = true;
        let formData = new FormData();
        formData.append("user_id", this.$userId);
        user
          .getVid(formData)
          .then((response) => {
            this.vid = response.data;
            localStorage.setItem("ivid", this.vid);
            this.getWalletDetail(this.vid);
            this.getStats(this.vid);
            this.show = false;
          })
          .catch((response) => {
            this.successful = false;
            //alert('something went wrong');
          });
      }
      // alert("aaa");
    },

    getWalletDetail(vid) {
      let formData = new FormData();
      formData.append("vid", vid);
      formData.append("type", "get");
      wallet
        .getWalletdetail(formData)
        .then((response) => {
          var resp = response.data;
          this.items = resp.order;
          this.values = resp;
          this.show = false;
          console.log(this.values);
        })
        .catch((response) => {
          this.successful = false;
          alert("something went wrong");
        });
    },

    getStats(vid) {
      let formData = new FormData();
      console.log("GetStat");
      wallet
        .getStats(vid)
        .then((response) => {
          var resp = response.data;
          this.stats = resp;
          console.log(this.stats);
        })
        .catch((response) => {
          this.successful = false;
          alert("Stats Not Available");
        });
    },
  },
};
</script>

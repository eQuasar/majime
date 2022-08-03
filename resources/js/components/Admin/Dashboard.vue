<template>
  <b-container fluid>
    <b-overlay
      :show="show"
      rounded="sm"
      spinner-type="grow"
      spinner-variant="primary"
      spinner-small
    >
      <div class="Dashboard_title">
        <div class="header_inner">
          <h3><strong>Dashboard</strong></h3>
          <br />
        </div>
      </div>

      <div class="content_bar card">
        <div class="card-body">
          <div class="call-center-dashboard">
            <b-col xl="8" lg="8" md="8">
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
                      month: 'short',
                      day: '2-digit',
                      weekday: 'short',
                    }"
                    locale="en"
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
                    locale="en"
                  ></b-form-datepicker>
                </div>
                <b-button type="submit" variant="primary">Submit</b-button>
              </b-form>
            </b-col>
            <!-- <div class="content_bar card list-appointments space-bottom">
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
            </div> -->
          </div>
        </div>
      </div>
      <br />
      <div class="status-block">
        <div class="stats blu">
          <h4>Total Orders</h4>
          <span>Total Orders:{{ dashboardData.totalcount }}</span>
          <p><i>₹ </i>{{ dashboardData.totalSaleAmount }}</p>
        </div>

        <div class="stats cancel">
          <h4>Cancelled Orders</h4>
          <span>Total Orders:{{ dashboardData.canceltotalcount }} </span>
          <p><i>₹ </i>{{ dashboardData.canceltotalSaleAmount }}</p>
        </div>

        <div class="stats orng">
          <h4>Failed Orders</h4>
          <span>Total Orders: {{ dashboardData.failtotalcount }}</span>
          <p><i>₹ </i>{{ dashboardData.failtotalSaleAmount }}</p>
        </div>

        <div class="stats red">
          <h4>On Hold Orders</h4>
          <span>Total Orders:{{ dashboardData.holdtotalcount }} </span>
          <p><i>₹ </i>{{ dashboardData.holdtotalSaleAmount }}</p>
        </div>
        <div class="stats blu">
          <h4>Processing Orders</h4>
          <span>Total Orders:{{ dashboardData.processingtotalcount }}</span>
          <p><i>₹ </i>{{ dashboardData.processingtotalSaleAmount }}</p>
        </div>

        <div class="stats confirme">
          <h4>Confirmed Orders</h4>
          <span>Total Orders:{{ dashboardData.confirmtotalcount }} </span>
          <p><i>₹ </i>{{ dashboardData.confirmtotalSaleAmount }}</p>
        </div>

        <div class="stats Pack">
          <h4>Packed Orders</h4>
          <span>Total Orders: {{ dashboardData.packedtotalcount }}</span>
          <p><i> ₹ </i>{{ dashboardData.packedtotalSaleAmount }}</p>
        </div>

        <div class="stats Dispatch">
          <h4>Dispatch Orders</h4>
          <span>Total Orders:{{ dashboardData.dispatchtotalcount }} </span>
          <p><i>₹ </i>{{ dashboardData.dispatchtotalSaleAmount }}</p>
        </div>
        <div class="stats blu">
          <h4>In-transit Orders</h4>
          <span>Total Orders:{{ dashboardData.transittotalcount }} </span>
          <p><i>₹ </i>{{ dashboardData.transittotalSaleAmount }}</p>
        </div>
        <div class="stats orng">
          <h4>Deliverd Orders</h4>
          <span>Total Orders:{{ dashboardData.deltotalcount }}</span>
          <p><i>₹ </i>{{ dashboardData.deltotalSaleAmount }}</p>
        </div>
        <div class="stats grn">
          <h4>RTO Orders</h4>
          <span>Total Orders:{{ dashboardData.rtototalcount }} </span>
          <p><i>₹ </i>{{ dashboardData.rtototalSaleAmount }}</p>
        </div>
        <div class="stats red">
          <h4>DTO Booked Orders</h4>
          <span>Total Orders:{{ dashboardData.dtobktotalcount }} </span>
          <p><i>₹ </i>{{ dashboardData.dtobktotalSaleAmount }}</p>
        </div>
        <div class="stats blu">
          <h4>DTO In-transit Orders</h4>
          <span>Total Orders:{{ dashboardData.dtointtotalcount }} </span>
          <p><i>₹ </i>{{ dashboardData.dtointtotalSaleAmount }}</p>
        </div>
        <div class="stats orng">
          <h4>DTO Deliverd Orders</h4>
          <span>Total Orders:{{ dashboardData.dtodeltotalcount }} </span>
          <p><i>₹ </i>{{ dashboardData.dtodeltotalSaleAmount }}</p>
        </div>
        <div class="stats grn">
          <h4>DTO Refunded Orders</h4>
          <span>Total Orders:{{ dashboardData.dtoreftotalcount }} </span>
          <p><i>₹ </i>{{ dashboardData.dtoreftotalSaleAmount }}</p>
        </div>
      </div>
      <br />
      <br />
      <div class="status-block-2">
        <div class="margin-report-title">
          <h3><strong>Margin Report</strong></h3>
          <h6>(Estimate Value)</h6>
        </div>
        <div class="stats blu">
          <h4>Gross sale</h4>
          <span>Total Orders:{{ dashboardData.grosscount }}</span>
          <p><i>₹ </i>{{ dashboardData.grossSaleAmount }}</p>
        </div>
        <div class="stats red">
          <h4>Estimated Net Sale</h4>
          <span>{{ dashboardData.netcount }}</span>
          <p><i>₹ </i>{{ dashboardData.netsale }}</p>
        </div>
        <div class="stats orng">
          <h4>Estimated Product Cost</h4>
          <span>N/A</span>
          <p><i>₹ </i>N/A</p>
        </div>
        <div class="stats grn">
          <h4>Ad Cost</h4>
          <span>N/A</span>
          <p><i>₹ </i>N/A</p>
        </div>
      </div>
      <b-pagination
        v-model="currentPage"
        :total-rows="rows"
        :per-page="perPage"
        aria-controls="my-table"
      ></b-pagination>
    </b-overlay>
  </b-container>
</template>

<script>
import dashboard from "../../api/dashboard.js";
import user from "../../api/user.js";
export default {
  props: {},
  mounted() {
    this.getVidz();
  },
  data() {
    return {
      show: false,
      ariaDescribedby: "",
      time: "",
      date: "",
      time_slots: [],
      seen: false,
      date_from: "",
      date_to: "",
      sortBy: "date",
      sortDesc: true,
      perPage: 10,
      currentPage: 1,
      filter2: null,
      pageOptions: [5, 10, 15, 20, 50, 100],
      filter: null,
      filterOn2: [],
      filterOn: [],
      fields: [
        {
          key: "oid",
          label: "Total Orders",
          sortable: true,
        },
        {
          key: "quantity",
          label: "Cancelled Orders",
          sortable: true,
        },
        {
          key: "subtotal",
          label: "Failed Orders",
          sortable: true,
        },
        {
          key: "state",
          label: "Processing",
          sortable: true,
        },
        {
          key: "city",
          label: "Hold",
          sortable: true,
        },
        {
          key: "value",
          label: "Packed",
          sortable: true,
        },
        {
          key: "date_created_gmt",
          label: "Dispetch",
          sortable: true,
        },
        {
          key: "currency",
          label: "In-transit",
          sortable: true,
        },
        {
          key: "status",
          label: "Deliverd",
          sortable: true,
        },
        {
          key: "status",
          label: "RTO",
          sortable: true,
        },
        {
          key: "status",
          label: "DTO Booked",
          sortable: true,
        },
        {
          key: "status",
          label: "DTO Intransit",
          sortable: true,
        },
        {
          key: "status",
          label: "DTO Deliverd",
          sortable: true,
        },
        {
          key: "status",
          label: "DTO Refunded",
          sortable: true,
        },
      ],

      items: [],
      errors_create: [],
      dashboardData: [],
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
    getVidz() {
      if (this.$userId == 1) {
        this.show = true;
        this.vid = JSON.parse(localStorage.getItem("ivid"));
        localStorage.setItem("ivid", this.vid);
        // this.getWalletDetail(this.vid);
        this.Orderdetail(this.vid);
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
            // this.getWalletDetail(this.vid);
            this.Orderdetail(this.vid);
            this.show = false;
          })
          .catch((response) => {
            this.successful = false;
            //alert('something went wrong');
          });
      }
      // alert("aaa");
    },
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
      formData.append("vid", this.vid);
      dashboard
        .dashbaordSearch(formData)
        .then((response) => {
          var resp = response.data;
          this.dashboardData = resp;
          console.log(this.dashboardData);

          // }
        })
        .catch((error) => {
          console.log(error);
          if (error.response.status == 422) {
            this.errors_create = error.response.data.errors;
          }
          // loader.hide();
        });
      // console.log(this.date_from);
      // console.log(this.date_to);
      // alert(JSON.stringify(this.form))
    },

    Orderdetail(vid) {
      // this.seen = true;
      let formData = new FormData();

      dashboard
        .getdashboard_detail(vid)
        .then((response) => {
          var resp = response.data;
          this.dashboardData = resp;
          console.log(this.dashboardData);
        })
        .catch((response) => {
          this.successful = false;
          alert("something went wrong");
        });
    },
  },
};
</script>

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
          <h3><strong>Performance</strong></h3>
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
                <!-- <b-button type="submit" variant="primary">Submit</b-button> -->
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
       <div class="status-block">
          <div class="stats blu">
            <h4>Total Orders</h4>
            <span>Total Orders:</span>
            <p><i>₹ </i>{{inTransitSaleAmount }}</p>
          </div>

          <div class="stats cancel">
            <h4>Cancelled Orders</h4>
            <span>Total Orders: </span>
            <p><i>₹ </i></p>
          </div>

          <div class="stats Fail">
            <h4>Failed Orders</h4>
            <span>Due Date: </span>
            <p><i>₹ </i></p>
          </div>

          <div class="stats red">
            <h4>On Hold Orders</h4>
            <span>Total Orders: </span>
            <p><i>₹ </i></p>
          </div>
           <div class="stats blu">
            <h4>Processing Orders</h4>
            <span>Total Orders:</span>
            <p><i>₹ </i></p>
          </div>

          <div class="stats confirme">
            <h4>Confirmed Orders</h4>
            <span>Total Orders: </span>
            <p><i>₹ </i></p>
          </div>

          <div class="stats Pack">
            <h4>Packed Orders</h4>
            <span>Due Date: </span>
            <p><i> ₹ </i></p>
          </div>

          <div class="stats red">
            <h4>Dispatch Orders</h4>
            <span>Total Orders: </span>
            <p><i>₹ </i></p>
          </div>
          <div class="stats blu">
            <h4>In-transit Orders</h4>
            <span>Total Orders: </span>
            <p><i>₹ </i></p>
          </div>
          <div class="stats orng">
            <h4>Deliverd Orders</h4>
            <span>Total Orders: </span>
            <p><i>₹ </i></p>
          </div>
          <div class="stats grn">
            <h4>RTO Orders</h4>
            <span>Total Orders: </span>
            <p><i>₹ </i></p>
          </div>
          <div class="stats red">
            <h4>DTO Booked Orders</h4>
            <span>Total Orders: </span>
            <p><i>₹ </i></p>
          </div>
          <div class="stats blu">
            <h4>DTO In-transit Orders</h4>
            <span>Total Orders: </span>
            <p><i>₹ </i></p>
          </div>
          <div class="stats orng">
            <h4>DTO Deliverd Orders</h4>
            <span>Total Orders: </span>
            <p><i>₹ </i></p>
          </div>
           <div class="stats grn">
            <h4>DTO Refunded Orders</h4>
            <span>Total Orders: </span>
            <p><i>₹ </i></p>
          </div>
        </div>
        <div class="status-block-2">
        	<div class="margin-report-title">
        		<h3><strong>Margin Report</strong></h3>
        		<h6>(Estimate Value)</h6>
        	</div>
          <div class="stats blu">
            <h4>Gross sale</h4>
            <span>Total Orders:</span>
            <!-- <p><i>₹ </i>{{inTransitSaleAmount }}</p> -->
          </div>
          <div class="stats red">
            <h4>Estimate Net Sale</h4>
            <span>33% of Gross order</span>
            <!-- <p><i>₹ </i></p> -->
          </div>
          <div class="stats orng">
            <h4>Estimate Product Cost</h4>
            <span>N/A</span>
            <!-- <p><i>₹ </i></p> -->
          </div>
           <div class="stats grn">
            <h4>Add Cost</h4>
            <span>Average Per Order</span>
            <!-- <p><i>₹ </i></p> -->
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
export default {
  props: {},
  mounted() {
    // this.Orderdetail();
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

    Orderdetail() {
      // this.seen = true;
       this.vid = JSON.parse(localStorage.getItem("vid"));
       let formData = new FormData();
      formData.append("vid",this.vid);
      dashboard.getdashboard_detail(formData)
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
  },
};
</script>

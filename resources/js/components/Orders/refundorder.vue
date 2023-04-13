<template>
  <b-container fluid>
    <div class="content_bar">
      <div class="card-body">
        <b-row>
          <b-col xl="12" lg="12" md="12">
            <b-row>
              <b-col xl="6" lg="6" md="6">
                <div class="profile_info">
                  <h3 class="own-heading">
                    <p class="h2 mb-2">
                      <strong>Order ID:{{ oid }}</strong>
                    </p>
                  </h3>
                </div>
              </b-col>
            </b-row>
          </b-col>
        </b-row>
      </div>
    </div>
    <div>
      <!-- <button @click="showModal = true">Open Modal</button>
      <div v-if="showModal" class="modal">
        <b-modal id="modal-1" title="Refund Amount" hide-footer size="lg">
          <b-form>
            <b-col xl="12" lg="12" md="12">
              <b-form-group
                id="input-group-username"
                label="Refund Amount"
                label-for="input-username"
              >
                <b-form-input
                  id="input-username"
                  v-model="amount"
                  type="text"
                  required
                  placeholder="Enter Refund Amount"
                ></b-form-input>
              </b-form-group>
            </b-col>
            <!-- <form @submit.prevent="submitForm">
              <div class="form-group">
                <label for="name">Enter Refund Amount</label>
                <input id="name" type="text" v-model="amount" required>
              </div> -->
      <!-- <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" v-model="email" required>
              </div> -->
      <!-- <b-btn variant="outline-primary" @click="submitForm">Submit</b-btn> -->
      <!-- </form> -->
      <!-- </b-form>
        </b-modal>
      </div> -->
      -->
    </div>

    <!-- <div class="order-info">
        <div class="content_bar card">
          <div class="card-body">
            <b-row>
              <b-col xl="4" lg="4" md="4">
                
              
              </b-col>
            </b-row>
          </div>
        </div>
      </div> -->
    <div class="tenpxdiv"></div>
    <!-- modal show -->
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
        <input
          type="text"
          placeholder="Enter Refund Amount"
          v-model="refund_amount"
        />
        <b-button type="submit" @click.prevent="submitForm" variant="primary"
          >Submit</b-button
        >
      </b-form>
    </b-modal>
    <!-- modal end -->
    <div class="content_bar">
      <div class="card-body">
        <b-row>
          <b-col xl="12" lg="12" md="12" class="d-table">
            <b-table
              striped
              hover
              responsive
              :items="items"
              :sort-by.sync="sortBy"
              sort-icon-left
              :filter-included-fields="filterOn"
              :filter="filter"
              :fields="fields"
              :per-page="perPage"
              :current-page="currentPage"
              show-empty
            >
              <template v-slot:cell(sr)="row">
                {{ (currentPage - 1) * perPage + row.index + 1 }}
              </template>
              <template v-slot:cell(action)="row">
                <p class="h3 mb-2">
                  <b-link
                    ><b-icon
                      icon="pencil-fill"
                      animation="spin-reverse"
                      variant="primary"
                      aria-hidden="true"
                      data-toggle="tooltip"
                      title="Refund Amount Edit"
                      @click="addstatusOID(row.item.suborder_id)"
                    ></b-icon></b-link
                  >&nbsp;
                  <!-- <router-link
                        :to="{
                          name: 'refundorder',
                          params: { oid: row.item.oid.toString() },
                        }"
                        ><b-icon icon="pencil-fill" aria-hidden="true"></b-icon
                      ></router-link>
                      &nbsp;
                      <router-link
                        :to="{
                          name: 'OrderProfile',
                          params: { oid: row.item.oid.toString() },
                        }"
                        ><b-icon icon="eye-fill" aria-hidden="true"></b-icon
                      ></router-link> -->
                </p>
              </template>
            </b-table>
          </b-col>
        </b-row>
      </div>
    </div>
  </b-container>
</template>

<script>
import order from "../../api/order.js";
export default {
  props: {
    oid: {
      type: String,
      required: true,
      default: 0,
    },
  },
  mounted() {
    this.suborder_details();
    //   this.getOrderItems();
  },
  data() {
    return {
      refund_amount: "",
      amount: "",
      show: false,
      currentPage: 1,
      image: "",
      rowClass: "",
      image_file: null,
      quantity: "",
      first_name: "",
      last_name: "",
      address_1: "",
      address_2: "",
      // oid: 0,
      email: "",
      total_amount: "",
      phone: "",
      payment_method: "",
      state: "",
      city: "",
      wallet_price: "",
      postcode: "",
      country: "",
      status: "",
      total: "",
      filter: null,
      filterOn: [],
      date_created_gmt: "",
      amount: 0,
      successful: false,
      sortBy: "date",
      errors_create: [],
      create_error: "",
      selected: "first",
      vid: 0,
      perPage: 10,
      options: [],
      fields: [
        {
          key: "sr",
          label: "S.No.",
        },
        {
          key: "suborder_id",
          label: "Sub-Order ID",
          sortable: true,
        },
        {
          key: "refund_amount",
          label: "Refund Amount",
          sortable: true,
        },
        {
          key: "invoice_no",
          label: "Invoice No.",
          sortable: true,
        },
        {
          key: "action",
          label: "Action",
          sortable: true,
        },
      ],
      items: [],
    };
  },
  methods: {
    // showModal() {
    //   this.$bvModal.show("modal-1");
    // },
    addstatusOID(suborderId) {
      // alert(suborderId);
      // this.allSelected = -false;
      this.SoId = suborderId;
      console.log(this.SoId);
      // this.status = status;
      // // this.allSelected = "false";
      this.$bvModal.show("modal-1");
      // // this.addstatus();
    },
    submitForm() {
      this.vid = JSON.parse(localStorage.getItem("ivid"));
      let formData = new FormData();
      formData.append("vid", this.vid);
      formData.append("suborder_id", this.SoId);
      formData.append("refund_amount", this.refund_amount);
      formData.append("oid", this.oid);

      order
        .suborder_refund_amount(formData)
        .then((response) => {
          // alert(response.data.msg);
          this.items = response.data.data;
          this.$bvModal.hide("modal-1");
          this.suborder_details();
          // this.show=false;
        })
        .catch((response) => {
          this.successful = false;
          alert("something went wrong");
        });
    },

    suborder_details() {
      this.vid = localStorage.getItem("ivid");
      order
        .suborder_details(this.oid, this.vid)
        .then((response) => {
          if (response.data) {
            this.suborder_id = response.data[0].suborder_id;
            this.invoice_no = response.data[0].invoice_no;
            this.amount = response.data[0].amount;
            dd(this.suborder_id);
            die();
            //   this.email = response.data[0].email;
            //   this.phone = response.data[0].phone;
            //   this.payment_method = response.data[0].payment_method;
            //   this.first_name = response.data[0].first_name;
            //   this.last_name = response.data[0].last_name;
            //   this.address_1 = response.data[0].address_1;
            //   this.address_2 = response.data[0].address_2;
            //   this.state = response.data[0].state;
            //   this.city = response.data[0].city;
            //   this.postcode = response.data[0].postcode;
            //   this.country = response.data[0].country;
            //   this.date_created_gmt = response.data[0].date_created_gmt;
            //   this.status = response.data[0].status;
            //   this.oid = response.data[0].oid;
            //   this.total_amount = response.data[0].total_main;
            //   this.wallet_price = response.data[0].total_main-response.data[0].total;
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    suborder_details() {
      // alert("asdas");
      // alert(this.oid);
      // this.show=true;
      // // alert('Assigned Successfully');
      this.vid = JSON.parse(localStorage.getItem("ivid"));
      let formData = new FormData();
      formData.append("vid", this.vid);
      formData.append("oid", this.oid);
      order
        .suborder_details(formData)
        .then((response) => {
          // alert(response.data.msg);
          this.items = response.data;
          // this.show=false;
        })
        .catch((response) => {
          this.successful = false;
          alert("something went wrong");
        });
    },
    goBack() {
      return this.$router.go(-1);
    },
    getItems() {
      order
        .getOrderItems(this.oid, this.vid)
        .then((response) => {
          if (response.data) {
            this.items = response.data;
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
  },
};
</script>

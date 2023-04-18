<template>
  <b-container fluid>
    <div class="header_title">
      <div class="header_inner">
        <h3><strong>Billing</strong></h3>
        <br />
      </div>
    </div>
    <div class="status-block">
      <div class="stats red">
        <span>Net Taxable Amount</span>
        <p><i>₹ </i>{{ calculatedTotalnet_texable_amount }}</p>
      </div>
      <div class="stats blu">
        <span>Net Invoice Amount</span>
        <p><i>₹ </i>{{ calculatedTotalnet_invoice_amount }}</p>
      </div>

      <div class="stats orng">
        <span>Net IGST</span>
        <p><i>₹ </i>{{ calculatedTotalnet_igst }}</p>
      </div>

      <div class="stats grn">
        <span>Net CGST</span>
        <p><i>₹ </i>{{ calculatedTotalnet_cgst }}</p>
      </div>
      <br />
    </div>
    <div class="content_bar">
    
    <b-row>
      
        <b-col xl="6" lg="6" md="6">
          <b-form @submit="onSubmit" class="date_range">
                  <b-alert show variant="danger" v-if="create_error">{{create_error}}</b-alert>
                    
                      <div class="datepiker-block"><span>From:&nbsp;</span> <b-form-datepicker id="from" v-model="date_from" :date-format-options="{ year: 'numeric', month: '2-digit', day: '2-digit' }"
                          locale="en"></b-form-datepicker></div>
                      <div class="datepiker-block"><span>To:&nbsp;</span> <b-form-datepicker id="to" v-model="date_to" :date-format-options="{ year: 'numeric', month: '2-digit', day: '2-digit' }" locale="en"></b-form-datepicker></div>
                      <b-button type="submit" variant="primary">Submit</b-button>
                    </b-form>
              </b-col>
           
    </b-row>
  </div>
</div>
    <!-- <div class="card-body">
      <b-row>
        <b-col xl="4" lg="4" md="4">
          <template>
            <b-form @submit="selected" class="date_range">
              <VueDatePicker
                v-model="date"
                :min-date="minDate"
                :max-date="maxDate"
                :range-presets="presets"
                placeholder="Select Date Range"
                range
                fullscreen-mobile
                validate
              />
              <b-button
                type="submit"
                variant="primary"
                style="margin-left: 10px"
                >Submit</b-button
              >
            </b-form>
          </template>
        </b-col>
      </b-row>
    </div> -->
    <div class="card-body">
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
            <b-button :disabled="!filter" @click="filter = ''">Clear</b-button>
          </b-input-group-append>
        </b-col>
      </b-row>
    </div>
    <div class="content_bar card">
      <div class="card-body">
        <b-row>
          <b-col>
            <button
              type="button"
              class="download-btn btn btn-primary"
              style="margin-top: 30px"
              v-on:click="saleinvoice_download"
            >
              Bill Wise Sales
            </button>
          </b-col>
          <b-col>
            <button
              type="button"
              class="download-btn btn btn-primary"
              style="margin-top: 30px"
              v-on:click="hsn_download"
            >
              HSN Wise Report
            </button>
          </b-col>
          <b-col>
            <button
              type="button"
              class="download-btn btn btn-primary"
              style="margin-top: 30px"
              v-on:click="state_download"
            >
              State Wise Report
            </button>
          </b-col>
          <b-col>
            <button
              type="button"
              class="download-btn btn btn-primary"
              style="margin-top: 30px"
              v-on:click="salereturn_download"
            >
              Bill Wise Sales Return
            </button>
          </b-col>
          <b-col>
            <button
              type="button"
              class="download-btn btn btn-primary"
              style="margin-top: 30px"
              v-on:click="Billing_download"
            >
              Master Download
            </button>
          </b-col>
        </b-row>
      </div>
    </div>
    <!-- <div>
      <b-button pill variant="danger">Sale</b-button>
  <b-button pill variant="info">Return</b-button>
  <b-button pill variant="primary">Net</b-button>
    </div> -->
    
    <div class="clear">&nbsp;</div>
    <b-overlay :show="show" rounded="sm" spinner-variant="primary">
      <div class="content_bar card">
        <div class="card-body" style="overflow-x: auto">
          <div class="Row">
      <div class="Column1"><b>SALE</b></div>
    <div class="Column2"><b>RETURN</b></div>
    <div class="Column3"><b>NET</b></div>
    <!-- <div class="saletable">SALE</div> -->
    </div>
          <b-table
            hover
            :items="itemsstate"
            :filter-included-fields="filterOn"
            :filter="filter"
            :fields="fields"
            :per-page="perPage"
            :current-page="currentPage"
            show-empty
          >
            <template #empty="scope"> No result found. </template>
            <template v-slot:cell(sr)="row">
              {{ (currentPage - 1) * perPage + row.index + 1 }}
            </template>
          </b-table>
          <!-- <b-pagination
            v-model="currentPage"
            :total-rows="rows"
            :per-page="perPage"
            aria-controls="my-table"
          ></b-pagination> -->
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
import billings from "../../api/billings.js";
import * as XLSX from "xlsx/xlsx.mjs";
// import { VueDatePicker } from "@mathieustan/vue-datepicker";
// import "@mathieustan/vue-datepicker/dist/vue-datepicker.min.css";

export default {


  props: {},
  mounted() {
    this.getVidz();
    this.getBilling_detail();
    // this.gethsn_detail();
    this.getstate_detail();
    // this.salereturn_detail();
    this.saleinvoice_detail();
    this.hsn_wise_detail_copy();
    this.sale_return_wise_detail();
  },
  data() {
    return {
      // date: new Date(),
      // date:"",
      // currentDate: new Date(),
      date_from: "",
      create_error:'',
      date_to: "",
      billing: "",
      net_igst: 0,
      net_cgst: 0,
      net_invoice_amount: 0,
      net_texable_amount: 0,
      net_sgst: "",
      perPage: 10,
      currentPage: 1,
      pageOptions: [5, 10, 15, 20, 50, 100],
      filter: null,
      show: false,
      filterOn: [],
      fields: [
        {
          key: "sr",
          label: "S No.",
          sortable: true,
          variant: "secondary",
        },
        {
          key: "order_to",
          label: "State",
          sortable: true,
          variant: "secondary",
        },
        {
          key: "sale_texable_amount",
          label: "Taxable Amount",
          sortable: true,
          variant: "danger",
        },
        {
          key: "sale_igst",
          label: "IGST",
          sortable: true,
          variant: "danger",
        },
        {
          key: "sale_cgst",
          label: "CGST",
          sortable: true,
          variant: "danger",
        },
        {
          key: "sale_sgst",
          label: "SGST",
          sortable: true,
          variant: "danger",
        },
        {
          key: "sale_invoice_amount",
          label: "Invoice Amount",
          sortable: true,
          variant: "danger",
        },
        {
          key: "return_texable_amount",
          label: "Taxable Amount",
          sortable: true,
          variant: "info",
        },
        {
          key: "return_igst",
          label: "IGST",
          sortable: true,
          variant: "info",
        },
        {
          key: "return_cgst",
          label: "CGST",
          sortable: true,
          variant: "info",
        },
        {
          key: "return_sgst",
          label: "SGST",
          sortable: true,
          variant: "info",
        },
        {
          key: "return_invoice_amount",
          label: "Invoice Amount",
          sortable: true,
          variant: "info",
        },
        {
          key: "net_texable_amount",
          label: "Taxable Amount",
          sortable: true,
          variant: "primary",
        },
        {
          key: "net_igst",
          label: "IGST",
          sortable: true,
          variant: "primary",
        },
        {
          key: "net_cgst",
          label: "CGST",
          sortable: true,
          variant: "primary",
        },
        {
          key: "net_sgst",
          label: "SGST",
          sortable: true,
          variant: "primary",
        },
        {
          key: "net_invoice_amount",
          label: "Invoice Amount",
          sortable: true,
          variant: "primary",
        },
      ],
      items: [],
      items_sale_return: [],
      items_sale_invoice: [],
      itemshsn: [],
      itemsstate: [],
      sale_return: [],
    };
  },
  computed: {
    calculatedTotalnet_igst: function () {
      return this.itemsstate.reduce(function (acc, item) {
        return acc + item.net_igst;
      }, 0);
    },
    calculatedTotalnet_cgst: function () {
      return this.itemsstate.reduce(function (acc, item) {
        return acc + item.net_cgst;
      }, 0);
    },
    calculatedTotalnet_invoice_amount: function () {
      return this.itemsstate.reduce(function (acc, item) {
        return acc + item.net_invoice_amount;
      }, 0);
    },
    calculatedTotalnet_texable_amount: function () {
      return this.itemsstate.reduce(function (acc, item) {
        return acc + item.net_texable_amount;
      }, 0);
    },
    rows() {
      return this.itemsstate.length;
    },
  },
  methods: {
    onSubmit(event) {
      this.show=true;
      event.preventDefault()
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
        this.vid = JSON.parse(localStorage.getItem("ivid"));
      let formData = new FormData();
      formData.append("vid", this.vid);
      formData.append("date_from", this.date_from);
      formData.append("date_to", this.date_to);
      billings
        .get_state_detail(formData)
        .then((response) => {
          // console.log(response.data);
          this.itemsstate = response.data;
          this.getBilling_detail();
          // this.getstate_detail();
          this.saleinvoice_detail();
          this.hsn_wise_detail_copy();
          this.sale_return_wise_detail();
          console.log(this.itemsstate);
          this.show = false;
      // let formData = new FormData();
      // formData.append("date_from", this.date_from);
      // formData.append("date_to", this.date_to);
      // formData.append("vid", this.vid);
      // billings.get_biiling_filter(formData)
      // .then((response) => {
      //   // console.log(response.data);
      //   this.items = response.data;
      //   this.show=false;
      })
      .catch((error) => {
        if (error.response.status == 422) {
          this.errors_create = error.response.data.errors;
        }
      });
    },
    // onSubmit(){
    // this.getBilling_detail();
  //  this.getstate_detail();
    // this.saleinvoice_detail();
    // this.hsn_wise_detail_copy();
    // this.sale_return_wise_detail();
    // },
    getBilling_detail() {
      this.show = true;
      this.vid = JSON.parse(localStorage.getItem("ivid"));
      let formData = new FormData();
      formData.append("vid", this.vid);
      formData.append("date_from", this.date_from);
      formData.append("date_to", this.date_to);
      billings
        .get_biiling_detail(formData)
        .then((response) => {
          // console.log(response.data);
          this.items = response.data;
          this.show = false;
        })
        .catch((error) => {
          if (error.response.status == 422) {
            this.errors_create = error.response.data.errors;
          }
        });
    },
    hsn_wise_detail_copy() {
      this.vid = JSON.parse(localStorage.getItem("ivid"));
      let formData = new FormData();
      formData.append("vid", this.vid);
      formData.append("date_from", this.date_from);
      formData.append("date_to", this.date_to);

      billings
        .get_hsn_detail(formData)
        .then((response) => {
          // console.log(response.data);
          this.itemshsn = response.data;
          this.show = false;
        })
        .catch((error) => {
          if (error.response.status == 422) {
            this.errors_create = error.response.data.errors;
          }
        });
    },
    sale_invoice_wise_detail() {
      this.vid = JSON.parse(localStorage.getItem("ivid"));
      let formData = new FormData();
      formData.append("vid", this.vid);
      billings
        .sale_invoice_wise_detail(formData)
        .then((response) => {
          // console.log(response.data);
          this.itemshsn = response.data;
          this.show = false;
        })
        .catch((error) => {
          if (error.response.status == 422) {
            this.errors_create = error.response.data.errors;
          }
        });
    },
    sale_return_wise_detail() {
      this.vid = JSON.parse(localStorage.getItem("ivid"));
      let formData = new FormData();
      formData.append("vid", this.vid);
      formData.append("date_from", this.date_from);
      formData.append("date_to", this.date_to);

      billings
        .get_salereturn_detail(formData)
        .then((response) => {
          // console.log(response.data);
          this.sale_return = response.data;
          this.show = false;
        })
        .catch((error) => {
          if (error.response.status == 422) {
            this.errors_create = error.response.data.errors;
          }
        });
    },
    getstate_detail() {
      this.vid = JSON.parse(localStorage.getItem("ivid"));
      let formData = new FormData();
      formData.append("vid", this.vid);
      formData.append("date_from", this.date_from);
      formData.append("date_to", this.date_to);
      billings
        .get_state_detail(formData)
        .then((response) => {
          // console.log(response.data);
          this.itemsstate = response.data;
          console.log(this.itemsstate);
          this.show = false;
          // var net_cgst = 0;
          // var net_igst = 0;
          // var net_invoice_amount = 0;
          // var net_sgst = 0;
          // var net_texable_amount = 0;
          // this.itemsstate.forEach((element) => {
          //   net_cgst += element.net_cgst;
          //   net_igst += element.net_igst;
          //   net_invoice_amount += element.net_invoice_amount;
          //   net_sgst += element.net_sgst;
          //   net_texable_amount += element.net_texable_amount;
          // });
          // console.log(net_cgst);
          // console.log(net_igst);
          // console.log(net_invoice_amount);
          // console.log(net_texable_amount);
          // console.log(net_sgst);
        })
        .catch((error) => {
          // if (error.response.status == 422) {
          //   this.errors_create = error.response.data.errors;
          // }
        });
    },
    saleinvoice_detail() {
      this.vid = JSON.parse(localStorage.getItem("ivid"));
      let formData = new FormData();
      formData.append("vid", this.vid);
      formData.append("date_from", this.date_from);
      formData.append("date_to", this.date_to);

      billings
        .get_saleinvoice_detail(formData)
        .then((response) => {
          // console.log(response.data);
          this.items_sale_invoice = response.data;
          // console.log(this.items_sale_invoice);
          this.show = false;
        })
        .catch((error) => {
          if (error.response.status == 422) {
            this.errors_create = error.response.data.errors;
          }
        });
    },
    // salereturn_detail() {
    //   this.vid = JSON.parse(localStorage.getItem("ivid"));
    //   let formData = new FormData();
    //   formData.append("vid", this.vid);
    //   billings
    //     .get_salereturn_detail(formData)
    //     .then((response) => {
    //       // console.log(response.data);
    //       this.items_sale_return = response.data;
    //       this.show = false;
    //     })
    //     .catch((error) => {
    //       if (error.response.status == 422) {
    //         this.errors_create = error.response.data.errors;
    //       }
    //     });
    // },
    Billing_download: function () {
      const data = XLSX.utils.json_to_sheet(this.items);
      const wb = XLSX.utils.book_new();
      /* fix headers */
      XLSX.utils.sheet_add_aoa(
        data,
        [
          [
            "Sr no",
            "Vid",
            "Vendor name",
            "Invoie Type",
            "Invoice Name",
            "SubOrder Id",
            "Texable Amount",
            "IGST",
            "SGST",
            "CGST",
            "Invoice Amount",
            " HSN Code",
            "Tax Percentage",
            "Dispatch Date",
            "Order From",
            "Order To",
            "Delivered Date",
            "Dto Booked Date",
            "Dto Delivered Date",
            "Sale return Status",
            "Sale Return Date",
            "Refund Date",
            "Refund Amount",
            "Waybill No",
            "Wallet Processed Date",
            "Parent OrderId",
            "Order Status",
            "Order Date",
            "Customer Note",
            "First_name",
            "Last Name",
            "Address",
            "City",
            "Status",
            "Post Code",
            "Country",
            "State",
            "Email",
            "Cart Discount Amount",
            "Phone",
            "Payment Method",
            "Order SUbtotal Amount",
            "Order Amount",
            "Wallet Used",
            "Collectable Amount",
            "OrderRefund Date",
            "Product Id",
            "Product Name",
            "SKU",
            "Product Qty",
            "Item Cost",
            "Product Weight",
            "Invoice Date",
            "Updated at",
          ],
        ],
        { origin: "A1" }
      );
      XLSX.utils.book_append_sheet(wb, data, "data");
      XLSX.writeFile(wb, "Billing_detail.xlsx");
    },
    hsn_download: function () {
      const data = XLSX.utils.json_to_sheet(this.itemshsn);
      const wb = XLSX.utils.book_new();
      XLSX.utils.sheet_add_aoa(
        data,
        [
          [
            "HSN Code",
            "Sale Tax Amount",
            "Sale IGST",
            "Sale CGST",
            "Sale SGST",
            "Sale Invoice Amount",
            "Return Tax Amount",
            "Return IGST",
            "Return CGST",
            "Return SGST",
            "Return Invoice Amount",
            "Net Tax Amount",
            "Net IGST",
            "Net CGST",
            "Net SGST",
            "Net Invoice Amount",
          ],
        ],
        { origin: "A1" }
      );
      XLSX.utils.book_append_sheet(wb, data, "data");
      XLSX.writeFile(wb, "hsn_detail.xlsx");
    },
    state_download: function () {
      const data = XLSX.utils.json_to_sheet(this.itemsstate);
      const wb = XLSX.utils.book_new();
      /* fix headers */
      XLSX.utils.sheet_add_aoa(
        data,
        [
          [
            "State",
            "Sale Tax Amount",
            "Sale IGST",
            "Sale CGST",
            "Sale SGST",
            "Sale Invoice Amount",
            "Return Tax Amount",
            "Return IGST",
            "Return CGST",
            "Return SGST",
            "Return Invoice Amount",
            "Net Tax Amount",
            "Net IGST",
            "Net CGST",
            "Net SGST",
            "Net Invoice Amount",
          ],
        ],
        { origin: "A1" }
      );
      XLSX.utils.book_append_sheet(wb, data, "data");
      XLSX.writeFile(wb, "statewise_detail.xlsx");
    },
    salereturn_download: function () {
      const data = XLSX.utils.json_to_sheet(this.sale_return);
      const wb = XLSX.utils.book_new();

      XLSX.utils.sheet_add_aoa(
        data,
        [
          [
            "Invoice No",
            "Sale Return Date",
            "Taxble Amount",
            "IGST",
            "SGST",
            "CGST",
            "Refund Amount",
            "HSN Code",
            "Tax Percentage",
            "Product Name",
            "Product Qty",
            "SuborderId",
            "Parent OrderId",
            "State",
            "Status",
          ],
        ],
        { origin: "A1" }
      );
      XLSX.utils.book_append_sheet(wb, data, "data");
      XLSX.writeFile(wb, "sale_return_detail.xlsx");
    },
    saleinvoice_download: function () {
      const data = XLSX.utils.json_to_sheet(this.items_sale_invoice);
      const wb = XLSX.utils.book_new();
      /* fix headers */
      // XLSX.utils.sheet_add_aoa(
      //   data,
      //   [
      //     [
      //       "Sr No",
      //       "Vendor Name",
      //       "PROJECT NAME",
      //       "PENDING TASKS",
      //       "1ST APPOINTMENT",
      //       "DE-SNAGGING",
      //       "2ND APPOINTMENT",
      //       "KEY HANDOVER APPOINTMENT",
      //       "FINAL KEY HANDOVER DONE",
      //     ],
      //   ],
      //   { origin: "A1" }
      // );
      XLSX.utils.book_append_sheet(wb, data, "data");
      XLSX.writeFile(wb, "saleinvoice_detail.xlsx");
    },
    getVidz() {
      if (this.$userId == 1) {
        this.vid = JSON.parse(localStorage.getItem("ivid"));
        localStorage.setItem("ivid", this.vid);
      } else {
        let formData = new FormData();
        formData.append("user_id", this.$userId);
        user
          .getVid(formData)
          .then((response) => {
            this.vid = response.data;
            localStorage.setItem("ivid", this.vid);
          })
          .catch((response) => {
            this.successful = false;
            alert("something went wrong");
          });
      }
    },
  },
};
</script>
<style scoped>
.abhi4 {
  padding: 20px;
  width: 271px;
}
.saletable {
  background-color: #ff00482b;
width: 41%;
height: 26px;
font-weight: bold;
padding: 5px;
text-align: center;
margin-left: 111px;
}
.Row {
    /* display: table; */
    width: 90%; /*Optional*/
    float:right;
    table-layout: fixed;
    border-spacing: 0px; /*Optional*/
    /* margin-left: 110px; */
    display:flex;
}
.Column1 {
  display: table-cell;
background-color: #ffb2b2;
text-align: center;
width:100%;
/* width: 607px; */
}
.Column2 {
  background-color: #a9d0fe;
text-align: center;
/* width: 410px; */
display: table-cell;
width:100%;
}
.Column3 {
  background-color: #bcbae0;
text-align: center;
/* width: 497px; */
display: table-cell;
width:100%;
}
</style>

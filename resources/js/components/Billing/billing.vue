<template>
  <b-container fluid>
    <div class="header_title">
      <div class="header_inner">
        <h3><strong>Billing</strong></h3>
        <br />
      </div>
    </div>
    <div class="content_bar card">
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
              <b-button :disabled="!filter" @click="filter = ''"
                >Clear</b-button
              >
            </b-input-group-append>
          </b-col>
        </b-row>
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
            v-on:click="saleinvoice_download"
          >
            Bill Wise Sales
          </button>
        </b-col>
      </div>
    </div>
    <div class="clear">&nbsp;</div>
    <b-overlay :show="show" rounded="sm" spinner-variant="primary">
      <div class="content_bar card">
        <div class="card-body">
          <b-table
            striped
            hover
            responsive
            :items="items"
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
            <template v-slot:cell(name)="row">
              <span v-if='row.item.user'>{{row.item.user.name}}</span>
            </template> 
            <template v-slot:cell(email)="row">
              <span v-if='row.item.user'>{{row.item.user.email}}</span>
            </template>
            <template v-slot:cell(phone)="row">
              <span v-if='row.item.user'>{{row.item.user.phone}}</span>
            </template>  -->
          </b-table> 
          <b-pagination
            v-model="currentPage"
            :total-rows="rows"
            :per-page="perPage"
            aria-controls="my-table"
          ></b-pagination>
        </div>
      </div>
    </b-overlay>
  </b-container>
</template>

<script>
import billings from "../../api/billings.js";
import * as XLSX from "xlsx/xlsx.mjs";

export default {
  props: {},
  mounted() {
    this.getVidz();
    this.getBilling_detail();
    this.gethsn_detail();
    this.getstate_detail();
    this.salereturn_detail();
    this.saleinvoice_detail();
  },
  data() {
    return {
      billing: "",
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
        },
        {
          key: "parent_order_number",
          label: "Parent Order Number",
          sortable: true,
        },
        {
          key: "vid",
          label: "Vid",
          sortable: true,
        },
        {
          key: "vendor_name",
          label: "Vendor Name",
          sortable: true,
        },
        {
          key: "invoicing_type",
          label: "Invoicing Type",
          sortable: true,
        },
        {
          key: "invoice_no",
          label: " Customer Invoice No",
          sortable: true,
        },
        {
          key: "invoice_no",
          label: "Customer Invoice No",
          sortable: true,
        },
        {
          key: "created_at",
          label: " Customer Invoice Date",
          sortable: true,
        },
        {
          key: "sub_order_id",
          label: "Sub OrderID",
          sortable: true,
        },
        {
          key: "textable_amount",
          label: "Taxable Amount",
          sortable: true,
        },
        {
          key: "igst",
          label: "IGST",
          sortable: true,
        },
        {
          key: "sgst",
          label: "SGST",
          sortable: true,
        },
        {
          key: "cgst",
          label: "CGST",
          sortable: true,
        },
        {
          key: "invoice_amount",
          label: "Invoice Amount",
          sortable: true,
        },
        {
          key: "hsn_code",
          label: "HSN Code",
          sortable: true,
        },
        {
          key: "text_percentage",
          label: "Tax Percentage",
          sortable: true,
        },
        {
          key: "dispatch_date",
          label: "Dispatch Date",
          sortable: true,
        },
        {
          key: "order_from",
          label: "Order From",
          sortable: true,
        },
        {
          key: "order_to",
          label: "Order to ",
          sortable: true,
        },
        {
          key: "delivered_date",
          label: "Delivered Date",
          sortable: true,
        },
        {
          key: "dto_booked_date",
          label: "DtoBooked Date",
          sortable: true,
        },
        {
          key: "dto_delivered_to_warhouse_date",
          label: "DtoDelivered to Warehouse",
          sortable: true,
        },
        {
          key: "sale_return_date",
          label: "Sale Return Date",
          sortable: true,
        },
        {
          key: "refund_amount",
          label: "Refund Amount",
          sortable: true,
        },
        {
          key: "sale_return_date",
          label: "Sale Return Date",
          sortable: true,
        },
        {
          key: "status",
          label: "Order Status",
          sortable: true,
        },
        {
          key: "order_date",
          label: "Order Date",
          sortable: true,
        },
        {
          key: "customer_note",
          label: "Customer Note",
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
          key: "address",
          label: "Address",
          sortable: true,
        },
        {
          key: "city",
          label: "City",
          sortable: true,
        },
        {
          key: "sr",
          label: "status Code",
          sortable: true,
        },
        {
          key: "post_code",
          label: "Post Code",
          sortable: true,
        },
        {
          key: "country_code",
          label: "Country Code",
          sortable: true,
        },
        {
          key: "state",
          label: "state",
          sortable: true,
        },
        {
          key: "email",
          label: "Email",
          sortable: true,
        },
        {
          key: "phone",
          label: "Phone",
          sortable: true,
        },
        {
          key: "pay_method_title",
          label: "Payment Method",
          sortable: true,
        },
        {
          key: "cart_discount_amount",
          label: "Cart Discount Amount",
          sortable: true,
        },
        {
          key: "sr",
          label: "Shipping Charges",
          sortable: true,
        },
        {
          key: "coupon_discount",
          label: "Coupan Discount",
          sortable: true,
        },
        {
          key: "order_amount",
          label: "Order Amount",
          sortable: true,
        },
        {
          key: "product_id",
          label: "Product Id",
          sortable: true,
        },
        {
          key: "product_name",
          label: "Product Name",
          sortable: true,
        },
        {
          key: "sku",
          label: "Sku",
          sortable: true,
        },
        {
          key: "product_qty",
          label: "Product Qty",
          sortable: true,
        },
        {
          key: "item_cost",
          label: "Item Cost",
          sortable: true,
        },
        {
          key: "coupon_code",
          label: "Coupan Code",
          sortable: true,
        },
        {
          key: "product_weight",
          label: "Product Weight",
          sortable: true,
        },
      ],
      items: [],
      items_sale_return: [],
      itemshsn: [],
      itemsstate: [],
    };
  },
  computed: {
    rows() {
      return this.items.length;
    },
  },
  methods: {
    getBilling_detail() {
      this.show = true;
      this.vid = JSON.parse(localStorage.getItem("ivid"));
      let formData = new FormData();
      formData.append("vid", this.vid);
      billings
        .get_biiling_detail(formData)
        .then((response) => {
          console.log(response.data);
          this.items = response.data;
          this.show = false;
        })
        .catch((error) => {
          if (error.response.status == 422) {
            this.errors_create = error.response.data.errors;
          }
        });
    },
    gethsn_detail() {
      this.vid = JSON.parse(localStorage.getItem("ivid"));
      let formData = new FormData();
      formData.append("vid", this.vid);
      billings
        .get_hsn_detail(formData)
        .then((response) => {
          console.log(response.data);
          this.itemshsn = response.data;
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
      billings
        .get_state_detail(formData)
        .then((response) => {
          console.log(response.data);
          this.itemsstate = response.data;
          this.show = false;
        })
        .catch((error) => {
          if (error.response.status == 422) {
            this.errors_create = error.response.data.errors;
          }
        });
    },
    saleinvoice_detail() {
      this.vid = JSON.parse(localStorage.getItem("ivid"));
      let formData = new FormData();
      formData.append("vid", this.vid);
      billings
        .get_saleinvoice_detail(formData)
        .then((response) => {
          console.log(response.data);
          this.items_sale_invoice = response.data;
          this.show = false;
        })
        .catch((error) => {
          if (error.response.status == 422) {
            this.errors_create = error.response.data.errors;
          }
        });
    },
    salereturn_detail() {
      this.vid = JSON.parse(localStorage.getItem("ivid"));
      let formData = new FormData();
      formData.append("vid", this.vid);
      billings
        .get_salereturn_detail(formData)
        .then((response) => {
          console.log(response.data);
          this.items_sale_return = response.data;
          this.show = false;
        })
        .catch((error) => {
          if (error.response.status == 422) {
            this.errors_create = error.response.data.errors;
          }
        });
    },
    Billing_download: function () {
      const data = XLSX.utils.json_to_sheet(this.items);
      const wb = XLSX.utils.book_new();
      /* fix headers */
      XLSX.utils.sheet_add_aoa(
        data,
        [
          [
            "Sr No",
            
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
      XLSX.writeFile(wb, "hsn_detail.xlsx");
    },
    state_download: function () {
      const data = XLSX.utils.json_to_sheet(this.itemsstate);
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
      XLSX.writeFile(wb, "statewise_detail.xlsx");
    },
    salereturn_download: function () {
      const data = XLSX.utils.json_to_sheet(this.items_sale_return);
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

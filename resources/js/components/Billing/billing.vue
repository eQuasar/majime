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
            Download
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
            <!-- <template v-slot:cell(name)="row">
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
          label: "Invoice No. To Majime",
          sortable: true,
        },
        {
          key: "created_at",
          label: "Date Invoice No. To Majime ",
          sortable: true,
        },

        {
          key: "customer_invice_no",
          label: "Customer Invoice No",
          sortable: true,
        },

        {
          key: "customer_invoice_date",
          label: "Date Customer Invoice No.",
          sortable: true,
        },

        {
          key: "sub_order_id",
          label: "Sub Order ID",
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
          key: "cgst",
          label: "CGST",
          sortable: true,
        },
        {
          key: "sgst",
          label: "SGST",
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
          label: "Dispatched date",
          sortable: true,
        },
        {
          key: "order_from",
          label: "Invoice State Code (From)",
          sortable: true,
        },
        {
          key: "order_to",
          label: "Invoice State Code (To)",
          sortable: true,
        },
        {
          key: "delivered_date",
          label: "Delivery Date",
          sortable: true,
        },
        {
          key: "dto_booked_date",
          label: "DTO Booked Date",
          sortable: true,
        },
        {
          key: "dto_delivered_to_warhouse_date",
          label: "DTO Delivered to Warehouse Date",
          sortable: true,
        },
        {
          key: "sale_return_status",
          label: "Sale Return Status",
          sortable: true,
        },
        {
          key: "sale_return_date",
          label: "Sale Return Date",
          sortable: true,
        },
        {
          key: "refund_date",
          label: "Refund Date",
          sortable: true,
        },
        {
          key: "wallet_procesed_date",
          label: "Wallet Procesed Date",
          sortable: true,
        },
        {
          key: "waybill_no",
          label: "Waybill No.",
          sortable: true,
        },
        {
          key: "parent_order_number",
          label: "Parent Order Number",
          sortable: true,
        },
        {
          key: "order_status",
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
          key: "status_code",
          label: "State Code",
          sortable: true,
        },
        {
          key: "post_code",
          label: "Postcode",
          sortable: true,
        },
        {
          key: "country_code",
          label: "Country Code",
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
          label: "Payment Method Title",
          sortable: true,
        },
        {
          key: "order_subtotal_amount",
          label: "Order Subtotal Amount",
          sortable: true,
        },
        {
          key: "cart_discount_amount",
          label: "Cart Discount Amount",
          sortable: true,
        },
        {
          key: "coupon_discount",
          label: "Coupon Discount",
          sortable: true,
        },
        {
          key: "order_amount",
          label: "Order Amount",
          sortable: true,
        },
        {
          key: "wallet_used",
          label: "Wallet Used",
          sortable: true,
        },
        {
          key: "collectable_amount",
          label: "Collectable Amount",
          sortable: true,
        },
        {
          key: "orderrefund_amount",
          label: "Order Refund Amount",
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
          label: "SKU",
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
          label: "Coupon Code",
          sortable: true,
        },
        {
          key: "product_weight",
          label: "Product Weight",
          sortable: true,
        },
      ],
      items: [],
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
    Billing_download: function () {
      const data = XLSX.utils.json_to_sheet(this.items);
      const wb = XLSX.utils.book_new();
      /* fix headers */
      XLSX.utils.sheet_add_aoa(
        data,
        [
          [
            "EMPLOYEE NAME",
            "TL NAME",
            "PROJECT NAME",
            "PENDING TASKS",
            "1ST APPOINTMENT",
            "DE-SNAGGING",
            "2ND APPOINTMENT",
            "KEY HANDOVER APPOINTMENT",
            "FINAL KEY HANDOVER DONE",
          ],
        ],
        { origin: "A1" }
      );
      XLSX.utils.book_append_sheet(wb, data, "data");
      XLSX.writeFile(wb, "TaskReport-Civil.xlsx");
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

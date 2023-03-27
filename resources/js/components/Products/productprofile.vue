<template>
  <b-container fluid>
    <div class="content_bar card">
      <div class="card-body">
        <b-row>
          <b-col xl="12" lg="12" md="12">
            <template>
              <b-button
                pill
                variant="download-btn btn btn-primary"
                @click="goBack"
                >Go Back</b-button
              >

              <b-button
                pill
                variant="download-btn btn btn-primary"
                @click="goBack2"
                >Edit</b-button
              >
            </template>
            <div class="profile_info">
              <h3 class="own-heading">
                <p class="h2 mb-2">
                  <h3><b>Product Name :- </b>{{product_name}}</h3>
                  <!-- <strong>Product Name:-</strong> {{ product_name }} -->
                </p>
              </h3>
            </div>
          </b-col>
        </b-row>
        <b-row>
          <b-col xl="4" lg="4" md="4">
            <p><strong>WEIGHT: </strong> {{ weight }}</p>
            <p><strong>HSN CODE: </strong> {{ hsn_code }}</p>
          </b-col>
          <b-col xl="4" lg="4" md="4">
            <p><strong>STATUS: </strong> {{ status }}</p>
            <p><strong>CATEGORIES: </strong> {{ categories }}</p>
          </b-col>
          <b-col xl="4" lg="4" md="4">
            <p><strong>PRICE: </strong> {{ price }}</p>
            <p><strong>TOTAL SALES: </strong> {{ total_sales }}</p>
          </b-col>
          <b-col xl="4" lg="4" md="4">
            <p><strong>SKU: </strong> {{ sku }}</p>
         
          </b-col>
          <b-col xl="4" lg="4" md="4">
          
            <p><strong>PRODUCT ID: </strong> {{ product_id }}</p>
          </b-col>
        </b-row>
      </div>
    </div>
    <div class="tenpxdiv"></div>
    <div class="content_bar">
      <div class="card-body">
        <b-row>
          <b-col xl="12" lg="12" md="12" class="tbl-blk">
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
              <template v-slot:cell(variation)="row">
                {{ row.item.variation_id }}
              </template>
              <template v-slot:cell(sku)="row">
                {{ row.item.sku }}
              </template>
              <template v-slot:cell(stock_quantity)="row">
                {{ row.item.stock_quantity }}
              </template>
              <template v-slot:cell(stock_status)="row">
                {{ row.item.stock_status }}
              </template>
              <template v-slot:cell(price)="row">
                {{ row.item.price }}
              </template>
              <template v-slot:cell(tax_status)="row">
                {{ row.item.tax_status }}
              </template>
              <template v-slot:cell(color)="row">
                {{ row.item.color }}
              </template>
              <template v-slot:cell(size)="row">
                {{ row.item.size }}
              </template>
              <template v-slot:cell(p_src)="row">
                <img class="img-thumbnail" :src="row.item.src" alt="..." />
              </template>
              <template v-slot:cell(action)="row">
                <p class="h3 mb-2">   
                <router-link :to="{ name: 'editbutton2', params: { product_id:(row.item.product_id).toString(),variation_id:(row.item.variation_id).toString()}}"><b-icon icon="pencil-fill" aria-hidden="true"></b-icon></router-link></p>
              </template>
            
            </b-table>
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
  </b-container>
</template>

<script>
import ProductProfile from "../../api/Product.js";
import OrderProfile from "../../api/order.js";

export default {
  props: {
    product_id: {
      required: false,
    },
  },

  mounted() {
    this.getOrder();
    this.getOrderItems();
    this.getProduct_detail();
  },
  data() {
    return {
      show: false,
      currentPage: 1,
      image: "",
      src: "",
      rowClass: "",
      image_file: null,
      quantity: "",
      first_name: "",
      last_name: "",
      address_1: "",
      address_2: "",
      color:'',
      size:'',
      email: "",
      phone: "",
      payment_method: "",
      state: "",
      city: "",
      postcode: "",
      tax_status: "",
      name: "",
      weight: "",
      hsn_code: "",
      cost: "",
      slug: "",
      type: "",
      status: "",
      total_sales: "",
      categories: "",
      country: "",
      status: "",
      sku: "",
      price: "",
      parent_name: "",
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
      product_name: "",
      vid: 0,
      perPage: 10,

      options: [],
      fields: [
        {
          key: "sr",
          label: "S.No.",
        },
        // {
        //   key: 'product_id',
        //   label: 'Product ID',
        //   sortable: true
        // },
        // {
        //   key: 'oid',
        //   label: 'Order ID',
        //   sortable: true
        // },

        {
          key: "variation",
          label: "Variation ID",
          sortable: true,
        },
        {
          key: "sku",
          label: "SKU",
          sortable: true,
        },
        // {
        //   key: 'parent_name',
        //   label: 'Parent Name',
        //   sortable: true
        // },
        {
          key: "stock_quantity",
          label: "Quantity",
          sortable: true,
        },
        {
          key: "stock_status",
          label: "Stock Status",
          sortable: true,
        },
        {
          key: "price",
          label: "Price",
          sortable: true,
        },
        {
          key: "tax_status",
          label: "Tax Status",
          sortable: true,
        },
        {
          key: 'color',
          label: 'Color',
          sortable: true
        },
        {
          key: 'size',
          label: 'Size',
          sortable: true
        },
        {
          key: "p_src",
          label: "Image",
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
  computed: {
    rows() {
      return this.items.length;
    },
  },
  methods: {
    getOrder() {
      this.show = true;
      this.vid = localStorage.getItem("ivid");
      ProductProfile.getProductProfile(this.product_id, this.vid)
        .then((response) => {
          if (response.data) {
            this.order_id = response.data[0].order_id;
            this.product_id = response.data[0].product_id;
            this.sku = response.data[0].sku;
            this.name = response.data[0].name;
            this.parent_name = response.data[0].parent_name;
            this.price = response.data[0].price;
            this.total = response.data[0].total;
            this.quantity = response.data[0].quantity;
            this.amount = response.data[0].total;
            this.email = response.data[0].email;
            this.phone = response.data[0].phone;
            this.payment_method = response.data[0].payment_method;
            this.first_name = response.data[0].first_name;
            this.last_name = response.data[0].last_name;
            this.address_1 = response.data[0].address_1;
            this.address_2 = response.data[0].address_2;
            this.state = response.data[0].state;
            this.city = response.data[0].city;
            this.postcode = response.data[0].postcode;
            this.country = response.data[0].country;
            this.date_created_gmt = response.data[0].date_created_gmt;
            this.status = response.data[0].status;
            this.color = response.data[0].color;
            this.size = response.data[0].size;
          }
          this.show = false;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    goBack2() {
      this.$router.push({
        name: "editbutton",
        params: { product_id: this.product_id },
      });
    },
    goBack() {
      return this.$router.go(-1);
    },
    getOrderItems() {
      this.show = true;
      ProductProfile.getProductItems(this.product_id, this.vid)
        .then((response) => {
          if (response.data) {
            this.items = response.data;
          }
          this.show = false;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    getProduct_detail() {
      this.show = true;
      ProductProfile.get_Product_detail(this.product_id, this.vid)
        .then((response) => {
          console.log(response.data);
          if (response.data) {
            this.product_name = response.data[0].name;
            this.weight = response.data[0].weight;
            this.hsn_code = response.data[0].hsn_code;
            this.price = response.data[0].price;
            this.slug = response.data[0].slug;
            this.type = response.data[0].type;
            this.status = response.data[0].status;
            this.total_sales = response.data[0].total_sales;
            this.product_id = response.data[0].product_id;
            this.sku = response.data[0].sku;
            // this.quantity=response.data[0].quantity;
            this.tax_status = response.data[0].tax_status;
            this.categories = response.data[0].categories;
          }
          this.show = false;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    previewImage: function (event) {
      // Reference to the DOM input element
      var input = event.target;
      // Ensure that you have a file before attempting to read it
      if (input.files && input.files[0]) {
        // create a new FileReader to read this image and convert to base64 format
        var reader = new FileReader();
        // Define a callback function to run, when FileReader finishes its job
        reader.onload = (e) => {
          // Note: arrow function used here, so that "this.imageData" refers to the imageData of Vue component
          // Read image as base64 and set to imageData
          this.imageData = e.target.result;
          this.image_file = e.target.result;
        };
        // Start the reader job - read file as a data url (base64 format)
        reader.readAsDataURL(input.files[0]);
      }
    },
    deleteImage() {
      this.image = null;
      this.image_file = null;
    },
    getImgUrl(pet) {
      var images = "/public/uploads/otheruser/" + pet;
      return images;
    },
  },
};
</script>

<style scoped>
.img-thumbnail {
  height: 107px;
  width: 112px;

  border-radius: 50%;
  -ms-transform: scale(1.5);
  -webkit-transform: scale(1.5);
  transform: scale(1.3);
}
.img-thumbnail:hover {
  transform: scale(
    2.5
  ); /* (150% zoom - Note: if the zoom is too large, it will go outside of the viewport) */
}
</style>

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
                    <strong>
                      <router-link
                        :to="{
                          name: 'OrderProfile',
                          params: { oid: this.oid.toString() },
                        }"
                      ></router-link>
                    </strong>
                  </h3>
                </div>
              </b-col>
              <b-col xl="6" lg="6" md="6">
                <template>
                  <span v-if="this.status != 'dtobooked'">
                    <span v-if="this.status != 'delivered-to-customer'"
                      ><b-button
                        pill
                        variant="download-btn btn btn-primary"
                        @click="returnAWB"
                        >Return Order</b-button
                      ></span
                    >
                  </span>
                  &nbsp;&nbsp;<b-button
                    pill
                    variant="download-btn btn btn-primary"
                    @click="goBack"
                    >Go Back</b-button
                  >
                </template>
              </b-col>
            </b-row>
          </b-col>
        </b-row>
      </div>
    </div>
    <div class="order-info">
      <div class="content_bar card">
        <div class="card-body">
          <b-row>
            <b-col xl="4" lg="4" md="4">
              <h3>General</h3>
              <p><strong>Order Date:</strong> {{ date_created_gmt }}</p>
              <p><strong>Status:</strong> {{ status }}</p>
              <p><strong>Name:</strong> {{ first_name }} {{ last_name }}</p>
              <p><strong>Phone:</strong> {{ phone }}</p>
              <p><strong>Email:</strong> {{ email }}</p>
              <p><strong>Payment Method:</strong> {{ payment_method }}</p>
              <p><strong>Total Amount:</strong> {{ total_amount }}</p>
              <p><strong>Wallet Used:</strong> {{ wallet_price }}</p>
            </b-col>
            <b-col xl="4" lg="4" md="4">
              <h3>Billing</h3>
              <p><strong>First Name:</strong> {{ first_name }}</p>
              <p><strong>Last Name:</strong> {{ last_name }}</p>
              <p>
                <strong>Address:</strong> {{ address_1 }}<br />
                {{ address_2 }}<br />
                {{ city }}, {{ state }} {{ postcode }}, {{ country }}
              </p>
            </b-col>
            <b-col xl="4" lg="4" md="4">
              <h3>Shipping</h3>
              <p><strong>First Name:</strong> {{ first_name }}</p>
              <p><strong>Last Name:</strong> {{ last_name }}</p>
              <p>
                <strong>Address:</strong> {{ address_1 }}<br />
                {{ address_2 }}<br />
                {{ city }}, {{ state }} {{ postcode }}, {{ country }}
              </p>
            </b-col>
          </b-row>
        </div>
      </div>
    </div>
    <div class="tenpxdiv"></div>
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
                <b-link @click="target_list"
                      ><b-icon
                        icon="pencil-fill"
                        variant="primary"
                        aria-hidden="true"
                        data-toggle="tooltip"
                        title="Edit HSN, Weight, Cost"
                      ></b-icon
                    ></b-link>
                <!-- <router-link :to="{ name: 'productprofile', params: {product_id:(row.item.product_id).toString() }}"><b-icon icon="eye-fill" aria-hidden="true"></b-icon></router-link> -->
                <!-- <p class="h3 mb-2"><router-link :to="{ name: 'editproductprofile2', params: { product_id:(row.item.product_id).toString() }}"><b-icon icon="pencil-fill" aria-hidden="true"></b-icon></router-link></p> -->
           </template>
            </b-table>
          </b-col>
        </b-row>
      </div>
    </div>
<!-- modal show -->
<b-modal   id="modal-1" title="Edit Hsn Code and Weight of the Product" hide-footer size="lg">
          <div class="content_bar card">
            <div class="card-body">
              <b-alert show variant="danger" v-if='create_error'>{{create_error}}</b-alert>
        <div :class="['form-group m-1 p-3', (successful ? 'alert-success' : '')]" v-show="successful">
        <span v-if="successful" class="label label-sucess">Transaction Detail Enter Sucessfully</span>
        </div>
              <b-row>
                <b-col xl="12" lg="12" md="12">
                  <div class="profile_info">
                    <h3 class="own-heading"><p class="h2 mb-2"><strong>Product ID:{{order_product_id}}</strong></p></strong></h3>
                      </b-overlay>
                  </div>
                </b-col>
              </b-row>
        <div class="content_bar">
        <div class="card-body card-body-bg">
        <b-row>
          <div>
        <b-col xl="6" lg="6" md="6"> 
              <b-form-group id="input-group-hsndetail" label="HSN Code" label-for="input-hsndetail" >
                <b-form-select v-model="hsndetail" class="" :options="allhsn"  value-field="hsn_code" text-field="hsn_code" >
                    <template v-slot:first>
                          <b-form-select-option :value="0" disabled>-- Select HSN Code --</b-form-select-option>
                      </template>
                </b-form-select>
              </b-form-group> 
            </b-col>
          </div>
                    <b-col xl="6" lg="6" md="6"> 
                    <b-form-group id="input-group-weight" label="Enter Weight of the product" label-for="input-slab1">
                        <b-form-input id="input-weight"  v-model="weight" type="number" class="number" required placeholder="Enter Weight of Product" ></b-form-input>
                    </b-form-group>       

                    </b-col>
                        <b-col xl="6" lg="6" md="6">
                          <b-form-group
                              id="price"
                              label="Cost"
                              label-for="Cost"
                              >
                              <b-form-input
                                id="price"
                                v-model="cost"
                                type="text"
                                required
                                placeholder="Cost"
                              ></b-form-input>
                          </b-form-group>
                          </b-col>
                    </b-form-group>       
              
          </b-row>  
          <b-button type="submit" @click.prevent="create" variant="primary">Submit </b-button>
        </div>
        </div>
        </div>
        </div>
        </div>
  </b-modal>
  </b-container>
</template>

<script>
import order from "../../api/order.js";
import ProductProfile from '../../api/Product.js';
  import HSN from '../../api/hsn.js';
export default {
  props: {
    oid: {
      type: String,
      required: true,
      default: 0,
    },
    // product_id: {
    //       type: String,
    //       required: true
    //     },
  },
  mounted() {
    this.getOrder();
    this.getOrderItems();
    this.getHsn();
        this.ProductDetail();
  },
  data() {
    return {
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
      allhsn:[],
      allhsn_array:[],
      weight:'',
      hsn1:'',
      hsn:'',
      hsndetail:'',
      name:'',
      order_product_id:"",
      country:'',
      status:'',
      sku:'',
      price:'',
      cost:'',
      parent_name:'',
      total:'',
      filter: null,
      filterOn: [],
      date_created_gmt:'',
      amount:0,
      successful: false,
      sortBy: 'date',
      errors_create:[],
      create_error:'',
      selected: 'first',
      vid: 0,
      perPage: 10,
      weight:0,
      // oid: 0,
      email: "",
      total_amount:"",
      phone: "",
      payment_method: "",
      state: "",
      city: "",
      wallet_price:"",
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
      product_id:"",
      variation_id:"",
      perPage: 10,
      options: [],
      fields: [
        {
          key: "sr",
          label: "S.No.",
        },
        {
          key: "product_id",
          label: "Product ID",
          sortable: true,
        },
        // {
        //   key: "variation_id",
        //   label: "Variation ID",
        //   sortable: true,
        // },
        {
          key: "name",
          label: "Name",
          sortable: true,
        },
        {
          key: "hsn_code",
          label: "HSN",
          sortable: true,
        },
        {
          key: "weight",
          label: "Weight",
          sortable: true,
        },
        // {
        //   key: "quantity",
        //   label: "Quantity",
        //   sortable: true,
        // },
        {
          key: "cost",
          label: "Cost",
          sortable: true,
        },
        {
          key: "total",
          label: "Total",
          sortable: true,
        },
        {
            key: 'action',
            label: 'Action',
            sortable: true
          },
      ],
      
      items: [],
    };
  },
  watch: {
        weight(newVal, oldVal) {
         if (newVal.includes('.')) {
        this.weight = newVal.split('.')[0] + '.' + newVal.split('.')[1].slice(0, 2)
        }
         }
      },
  methods: {
    restrictDecimal () {
            this.weight=this.weight.match(/^\d+\.?\d{0,2}/);
          },
        create() {
          this.create_error = "";
          if (!this.hsndetail) {
            this.create_error += "Enter HSN Code,";
          }
          if (!this.weight) {
            this.create_error += "Enter Weight of prduct,";
          }
          if (!this.cost) {
              this.create_error += "Enter Cost,";
            }
          if (this.create_error != "") {
            return false;
          }
          let formData = new FormData();
          formData.append("hsn", this.hsndetail);
          formData.append("weight", this.weight); 
          formData.append("cost", this.cost); 
          formData.append("product_id", this.order_product_id); 
          HSN.update_hsn_weight(formData)
            .then((response) => {
              this.successful = true;
              this.error = true;
              this.getOrderItems();
              this.$router.push({name: 'OrderProfile'});
            })
            .catch((error) => {
                console.log(error);
                if (error.response.status == 422) {
                    this.errors_create = error.response.data.errors;
                }
            });
        },
      getHsn(){
      HSN.getHsn()
      .then((response) => {
          this.allhsn=response.data.data;
        })
        .catch((error) => {
            console.log(error);
            if (error.response.status == 422) {
                this.errors_create = error.response.data.errors;
            }
            loader.hide();
        });
    },
    ProductDetail(vid) {
      this.vid = JSON.parse(localStorage.getItem("ivid"));
      let formData= new FormData();
      formData.append("product_id",this.order_product_id);
      formData.append("vid",this.vid);
      HSN.getProduct_data(formData)
       .then(( response ) => {
          console.log(response);
          this.items=response.data;
          console.log(this.items);
        })
        .catch(response => {
            this.successful = false;
            alert('something went wrong');
        })
    },
    target_list(id){
        this.$bvModal.show("modal-1");
      },
    getOrder() {
      this.vid = localStorage.getItem("ivid");
      order
        .getOrderProfile(this.oid, this.vid)
        .then((response) => {
          console.log(response.data);
          if (response.data) {
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
            this.oid = response.data[0].oid;
            this.total_amount = response.data[0].total_main;
            this.wallet_price = response.data[0].total_main-response.data[0].total;
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    returnAWB() {
      // alert("asdas");
      // alert(this.oid);
      // this.show=true;
      // // alert('Assigned Successfully');
      this.vid = JSON.parse(localStorage.getItem("ivid"));
      let formData = new FormData();
      formData.append("vid", this.vid);
      formData.append("oid", this.oid);
      order
        .returnAWB(formData)
        .then((response) => {
          alert(response.data.msg);
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
    getOrderItems() {
      order
        .getOrderItems(this.oid, this.vid)
        .then((response) => {  
          console.log(response.data);
          this.order_product_id=response.data[0].product_id;
          this.weight=response.data[0].weight;
          this.cost=response.data[0].cost;
          this.hsndetail=response.data[0].hsn_code;
          this.$bvModal.hide('modal-1')
          if (response.data) {
            this.items = response.data;
          
          }
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

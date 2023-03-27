<template>
    <b-container fluid>  
      <div class="content_bar card">
                    <div class="card-body">
                      <b-alert show variant="danger" v-if='create_error'>{{create_error}}</b-alert>
              <div :class="['form-group m-1 p-3', (successful ? 'alert-success' : '')]" v-show="successful">
                <span v-if="successful" class="label label-sucess">Transaction Detail Enter Sucessfully</span>
              </div>
                      <b-row>
                        <b-col xl="12" lg="12" md="12">
                          <!-- <template>
                              <b-button pill variant="download-btn btn btn-primary" @click="goBack">Go Back</b-button>
                          </template> -->
                          <div class="profile_info">
                            <h3><b>Product Name :- </b>{{product_name}}</h3>
                            <!-- <h2><p><strong>Product Name:-</strong> {{product_name}}</p></h> -->
                              </b-overlay>
                          </div>
                        </b-col>
                      </b-row>
            <div class="content_bar">
              <div class="card-body card-body">
                <b-row>
                  <b-col xl="6" lg="6" md="6">
                    <b-form-group
                        id="product_name"
                        label="Product Name"
                        label-for="product_id"
                        >
                        <b-form-input
                          id="product_id"
                          v-model="product_name"
                          type="text"
                          required
                          placeholder="Product Name"
                        ></b-form-input>
                    </b-form-group>
                    </b-col>
                <b-col xl="6" lg="6" md="6">
                      <b-form-group
                          id="input-group-name"
                          label="WEIGHT"
                          label-for="input-Weight"
                          >
                          <b-form-input
                            id="input-name"
                            v-model="weight"
                            type="text"
                            required
                            placeholder="weight"
                          ></b-form-input>
                      </b-form-group>
                      </b-col>

                      <b-col xl="6" lg="6" md="6"> 
                        <b-form-group
                         id="input-group-hsndetail" label="HSN Code" label-for="input-hsndetail" >
                          <b-form-select v-model="hsndetail" class="" :options="allhsn"  value-field="hsn_code" text-field="hsn_code" >
                              <!-- <template v-slot:first>
                                    <b-form-select-option :value="0" disabled>-- Select HSN Code --</b-form-select-option>
                                </template> -->
                          </b-form-select>
                        </b-form-group> 
                      </b-col>

                       <b-col xl="6" lg="6" md="6">
                      <b-form-group
                          id="slug"
                          label="Slug"
                          label-for="Slug"
                          >
                          <b-form-input
                            id="Slug"
                            v-model="slug"
                            type="text"
                            required
                            placeholder="Slug"
                          ></b-form-input>
                      </b-form-group>
                      </b-col>

                       <b-col xl="6" lg="6" md="6">
                      <b-form-group
                          id="price"
                          label="Price"
                          label-for="price"
                          >
                          <b-form-input
                            id="price"
                            v-model="price"
                            type="text"
                            required
                            placeholder="Price"
                          ></b-form-input>
                      </b-form-group>
                      </b-col>

                       <b-col xl="6" lg="6" md="6">
                      <b-form-group
                          id="categories"
                          label="categories"
                          label-for="categories"
                          >
                          <b-form-input
                            id="categories"
                            v-model="categories"
                            type="text"
                            required
                            placeholder="categories"
                          ></b-form-input>
                      </b-form-group>
                      </b-col>

                       <b-col xl="6" lg="6" md="6">
                      <b-form-group
                          id="Sku"
                          label="Sku"
                          label-for="Sku"
                          >
                          <b-form-input
                            id="Sku"
                            v-model="sku"
                            type="text"
                            required
                            placeholder="Sku"
                          ></b-form-input>
                      </b-form-group>
                      </b-col>

                     
                    </b-row>
                      <b-button type="submit" @click.prevent="create" variant="primary">Submit </b-button>
                    
                </div>
            </div>
        </div>
       </div>
     </div>
    </b-container>
  </template>
  
  <script>
 import ProductProfile from "../../api/Product.js";
import OrderProfile from "../../api/order.js";
import HSN from "../../api/hsn.js";
  
    export default {
      props: {
          product_id: {
          required: true
        },
      },
  
      mounted() {
        // this.getOrder();
        // this.getOrderItems();
        this.getHsn();
        this.ProductDetail();
        this.getProduct_detail();
      },
      data() {
        return {
          show: false,
          currentPage: 1,
          image:'',
          rowClass:'',
          image_file:null,
          quantity:'',
          allhsn:[],
          allhsn_array:[],
          product_name:'',
          weight:'',
          Sku:'',
          categories:'',
          HSN:'',
          price:'',
          slug:'',
          hsn_code:'',
          first_name:'',
          last_name:'',
          address_1:'',
          address_2:'',
          sortDesc:'',
          email:'',
          weight:'',
          hsn1:'',
          hsn:'',
          hsndetail:'',
          phone:'',
          payment_method:'',
          state:'',
          city:'',
          postcode:'',
          name:'',
          country:'',
          status:'',
          sku:'',
          price:'',
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
  
          options: [],
          
          items: [],
          errors_create:[],
          successful: false,
          create_error:'',
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
          if (!this.product_name) {
            this.create_error += "Enter Product Name,";
          }
          if (!this.hsndetail) {
            this.create_error += "Enter HSN Code,";
          }
          if (!this.weight) {
            this.create_error += "Enter Weight of prduct,";
          }
          if (!this.slug) {
            this.create_error += "Enter Slug,";
          }
          if (!this.sku) {
            this.create_error += "Enter Sku,";
          }  if (!this.price) {
            this.create_error += "Enter Price,";
          }
          if (!this.categories) {
            this.create_error += "Enter Categories,";
          }
          if (this.create_error != "") {
            return false;
          }
          let formData = new FormData();
          formData.append("product_name", this.product_name);
          formData.append("product_id", this.product_id);
          formData.append("vid", this.vid);
          formData.append("hsn", this.hsndetail);
          formData.append("weight", this.weight); 
          formData.append("slug", this.slug); 
          formData.append("sku", this.sku); 
          formData.append("price", this.price); 
          formData.append("categories", this.categories); 
          ProductProfile.updateProduct_detail(formData)
            .then((response) => {
              this.successful = true;
              this.error = true;
              this.ProductDetail();
              this.$router.push({name: 'adminProductList'});
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

    getProduct_detail() {
      this.show = true;
      this.vid = localStorage.getItem("ivid");
      ProductProfile.get_Product_detail(this.product_id, this.vid)
        .then((response) => {
          console.log(response.data);
          if (response.data) {
            this.product_name = response.data[0].name;
            this.weight = response.data[0].weight;
            this.hsn = response.data[0].hsndetail;
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

    ProductDetail(vid) {
      this.vid = JSON.parse(localStorage.getItem("ivid"));
      let formData= new FormData();
      formData.append("product_id",this.product_id);
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
      },
    };
  </script>
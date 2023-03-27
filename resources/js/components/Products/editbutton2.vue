<template>
  <b-container fluid>  
    <div class="content_bar card">
                  <div class="card-body">
                    <b-alert show variant="danger" v-if='create_error'>{{create_error}}</b-alert>
            <div :class="['form-group m-1 p-3', (successful ? 'alert-success' : '')]" v-show="successful">
              <span v-if="successful" class="label label-sucess">Variation Details successfully Updated</span>
            </div>
                    <b-row>
                      <b-col xl="12" lg="12" md="12">
                        <!-- <template>
                            <b-button pill variant="download-btn btn btn-primary" @click="goBack">Go Back</b-button>
                        </template> -->
                        <div class="profile_info">
                          <h3><b>Variation Id :- </b>{{variation_id}}</h3>
                          <!-- <h3 class="own-heading"><p class="h2 mb-2"><strong>Variation Id:{{variation_id}}</strong></p> </strong></h3> -->
                            </b-overlay>
                        </div>
                      </b-col>
                    </b-row>
          <div class="content_bar">
            <div class="card-body card">
              <b-row>
                <b-col xl="6" lg="6" md="6">
                  <b-form-group
                      id="sku"
                      label="SKU"
                      label-for="sku"
                      >
                      <b-form-input
                        id="sku"
                        v-model="sku"
                        type="text"
                        required
                        placeholder="sku"
                      ></b-form-input>
                  </b-form-group>
                  </b-col>
              <b-col xl="6" lg="6" md="6">
                    <b-form-group
                        id="input-group-name"
                        label="Quantity"
                        label-for="quantity"
                        >
                        <b-form-input
                          id="quantity"
                          v-model="stock_quantity"
                          type="text"
                          required
                          placeholder="quantity"
                        ></b-form-input>
                    </b-form-group>
                    </b-col>

                     <b-col xl="6" lg="6" md="6">
                    <b-form-group
                        id="stock_status"
                        label="Stock Status"
                        label-for="stock_status"
                        >
                        <b-form-input
                          id="stock_stutus"
                          v-model="stock_status"
                          type="text"
                          required
                          placeholder="stock_stutus"
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
                          placeholder="price"
                        ></b-form-input>
                    </b-form-group>
                    </b-col>

                     <b-col xl="6" lg="6" md="6">
                    <b-form-group
                        id="tax_status"
                        label="Tax Status"
                        label-for="tax_status"
                        >
                        <b-form-input
                          id="tax_status"
                          v-model="tax_status"
                          type="text"
                          required
                          placeholder="tax_status"
                        ></b-form-input>
                    </b-form-group>
                    </b-col>

                    <b-col xl="6" lg="6" md="6">
                      <div class="clear form-group">
                        <label>Upload Image (Optional)</label>
                        <div class="custom-file mb-3">
                          <input type="file" ref="image" @change="previewImage" name="image" class="custom-file-input" id="image" accept=".jpg,.jpeg,.png">
                          <label class="custom-file-label" >Choose file...</label>
                        </div>
                        <img v-if='image_file' :src="image_file" style='width:50%;'>
                        <a v-if='image_file' class="preview" @click="deleteImage" data-method="delete" data-confirm="Are you sure?">
                              <i class="fa fa-times" style='cursor: pointer; position: absolute; margin-right: 0px;margin-left: 10px;font-size: 15px;'></i>
                        </a>
                      </div>
                  </b-col> 
                    <b-col xl="6" lg="6" md="6">
                      <b-form-group
                          id="description"
                          label="Description"
                          label-for="description"
                          >
                          <b-form-input
                            id="description"
                            v-model="description"
                            type="text"
                            required
                            placeholder="description"
                          ></b-form-input>
                      </b-form-group>
                      </b-col>
                      <b-col xl="6" lg="6" md="6">
                        <b-form-group
                            id="color"
                            label="Color"
                            label-for="color"
                            >
                            <b-form-input
                              id="color"
                              v-model="color"
                              type="text"
                              required
                              placeholder="color"
                            ></b-form-input>
                        </b-form-group>
                        </b-col>
                        <b-col xl="6" lg="6" md="6">
                          <b-form-group
                              id="size"
                              label="Size"
                              label-for="size"
                              >
                              <b-form-input
                                id="size"
                                v-model="size"
                                type="text"
                                required
                                placeholder="size"
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
        variation_id: {
        required: true
      },
      product_id:{
      required:true
      },
    },

    mounted() {
      // this.getOrder();
      // this.getOrderItems();
      this.getHsn();
      this.ProductDetail();
      this.productvariation_detail();
    },
    data() {
      return {
        show: false,
        currentPage: 1,
        image:'',
        description:'',
        rowClass:'',
        image_file:null,
        stock_status:'',
        tax_status:'',
        quantity:'',
        src:'',
        allhsn:[],
        allhsn_array:[],
        product_name:'',
        weight:'',
        Sku:'',
        categories:'',
        HSN:'',
        price:'',
        slug:'',
        size:'',
        color:'',
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
        imageData: "" ,
        stock_quantity:'',
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
        if (!this.sku) {
          this.create_error += "Enter SKU,";
        }
        if (!this.stock_quantity) {
          this.create_error += "Enter Quantity,";
        }
        if (!this.stock_status) {
          this.create_error += "Enter Stock Status,";
        }
        if (!this.price) {
          this.create_error += "Enter Price,";
        }
        if (!this.tax_status) {
          this.create_error += "Enter tax_status,";
        }  
        if (!this.description) {
          this.create_error += "Enter descriptions,";
        }
        if (!this.color) {
          this.create_error += "Enter color,";
        }
        if (!this.size) {
          this.create_error += "Enter size,";
        }
        if (this.create_error != "") {
          return false;
        }
        let formData = new FormData();
        formData.append("sku", this.sku);
        formData.append("stock_quantity", this.stock_quantity);
        formData.append("vid", this.vid);
        formData.append("stock_status", this.stock_status); 
        formData.append("price", this.price); 
        formData.append("tax_status", this.tax_status); 
        formData.append("description", this.description); 
        formData.append("product_id", this.product_id); 
        formData.append("variation_id", this.variation_id);  
        ProductProfile.update_productVariation_detail(formData)
          .then((response) => {
            this.successful = true;
            this.error = true;
            this.$router.push({name: 'productprofile'});
          })
          .catch((error) => {
              console.log(error);
              if (error.response.status == 422) {
                  this.errors_create = error.response.data.errors;
              }
          });
      },
      previewImage: function(event) {
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
                this.src = e.target.result;
                this.image_file = e.target.result;
            }
            // Start the reader job - read file as a data url (base64 format)
            reader.readAsDataURL(input.files[0]);
        }
    },
    deleteImage()
    {
      this.image = null;
      this.image_file = null;
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
  productvariation_detail()
  {
    let formData = new FormData();
          formData.append("product_id", this.product_id);
          formData.append("vid", this.vid);
          formData.append("variation_id", this.variation_id);
          ProductProfile.getProductVariation_detail(formData)
            .then((response) => {
              console.log(response.data);
              if (response.data) {
            this.sku = response.data.data[0].sku;
            this.stock_quantity = response.data.data[0].stock_quantity;
            this.stock_status = response.data.data[0].stock_status;
            this.price = response.data.data[0].price;
            this.tax_status = response.data.data[0].tax_status;
            this.src = response.data.data[0].src;
            this.description = response.data.data[0].description;
            this.color = response.data.data[0].color;
            this.size = response.data.data[0].size;
              }
              this.show = false;
            })
            .catch((error) => {
                console.log(error);
                if (error.response.status == 422) {
                    this.errors_create = error.response.data.errors;
                }
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
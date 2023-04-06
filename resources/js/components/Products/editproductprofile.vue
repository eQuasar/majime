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
                          <h3 class="own-heading"><p class="h2 mb-2"><strong>Product ID:{{product_id}}</strong></p> <router-link :to="{ name: 'editproductprofile', params: { variation_id: (this.product_id).toString() }}"></router-link></strong></h3>
                            </b-overlay>
                        </div>
                      </b-col>
                    </b-row>
                    <!-- <b-row>
                      <b-col xl="4" lg="4" md="4">
                          
                            <p><strong>Product ID</strong> {{product_id}}</p>
                            <p><strong>Product Name:</strong> {{name}}</p>              
                      </b-col>
                      <b-col xl="4" lg="4" md="4">
                              <p><strong>SKU:</strong> {{sku}} </p>
                            <p><strong>Parent Name:</strong> {{parent_name}}</p>
                      </b-col>
                      <b-col xl="4" lg="4" md="4">
                            <p><strong>Price:</strong> {{price}}</p> 
                      </b-col>
                    </b-row> -->
               
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
                  

                  <!-- 
                        <b-form-group id="input-group-hsn" label="HSN Code" label-for="input-hsn">
                          <b-form-input id="input-amount"  v-model="hsn" type="text" required placeholder="Enter HSN Code"></b-form-input>
                          </b-form-group> -->
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
          <b-table striped hover responsive :items="items"
      :sort-by.sync="sortBy"
      :sort-desc.sync="sortDesc"
      sort-icon-left :filter-included-fields="filterOn" :filter="filter" :fields="fields" :per-page="perPage" :current-page="currentPage" show-empty>
            <template #empty="scope">
                <p style="text-align:center;">No record found, choose date filter to found the result.</p>
            </template>

            <template v-slot:cell(oid)="row">
              #{{(row.item.id)}}
              </template>
      </b-table>
      </div>
     </div>
   </div>
  </b-container>
</template>

<script>
import ProductProfile from '../../api/Product.js';
import OrderProfile from '../../api/order.js';
import HSN from '../../api/hsn.js';

  export default {
    props: {
        product_id: {
        type: String,
        required: true
      },
    },

    mounted() {
      // this.getOrder();
      // this.getOrderItems();
      this.getHsn();
      this.ProductDetail();
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

        options: [],
        fields: [
          // {
          //   key: 'sr',
          //   label: 'S.No.',
        
          // },
          // {
          //   key: 'product_id',
          //   label: 'Product ID',
          //   sortable: true
          // },
          {
            key: 'product_id',
            label: 'Product id',
            sortable: true
          },
          
          {
            key: 'name',
            label: 'Name',
            sortable: true
          },
          // {
          //   key: 'parent_name',
          //   label: 'Parent Name',
          //   sortable: true
          // },
          {
            key: 'categories',
            label: 'Categories',
            sortable: true
          },
          {
            key: 'cost',
            label: 'Cost',
            sortable: true
          },
          {
            key: 'hsn_code',
            label: 'HSN Code',
            sortable: true
          },
          {
            key: 'weight',
            label: 'Weight',
            sortable: true
          },
          
        ],
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
        formData.append("product_id", this.product_id); 
        HSN.update_hsn_weight(formData)
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
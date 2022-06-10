<template>
  <b-container fluid> 
    <div class="header_title">
      <div class="header_inner">
        <b-row>
          <b-col xl="6" lg="6" md="6">
        <h3><strong>List Orders For Variation Id:{{variation_id}}</strong></h3>
      </b-col>
      <b-col xl="6" lg="6" md="6">
        <template>
          <b-button pill variant="download-btn btn btn-primary" @click="goBack">Go Back</b-button>
       </template>
     </b-col>
       </b-row>
      </div>
    </div> 
    </br>
    <div class="content_bar"> 
        <div class="card-body card">
           <div class="call-center-dashboard">
            <div class="select-list">
          <b-row>
            <b-col xl="2" lg="2" md="2">
                <select class='form-control custom-select' v-model='status' :options="allstatusdata" @change='onChangeStatus($event)'>
                    <option disabled value="null">Select status</option>
                    <option v-for='data in allstatusdata' :value='data.status'>{{data.status}}</option>
                </select>
               </b-col>
               <b-col xl="2" lg="2" md="2">
                <select class='form-control custom-select' v-model='city' :options="allcitydata" @change='onChangeCity($event)'>
                    <option disabled value="null">Select city</option>
                    <option v-for='data in allcitydata' :value='data.city'>{{data.city}}</option>
                </select>
               </b-col>
            <b-col xl="2" lg="2" md="2">
            </b-col>
            <b-col xl="6" lg="6" md="6">
                             <b-alert show variant="danger" v-if="create_error">{{create_error}}</b-alert>
                                 <b-form @submit="onSubmit" class="date_range">
                       <div class="datepiker-block">
                         <span>From:&nbsp;</span>  <b-form-datepicker  id="from" v-model="date_from" :date-format-options="{ year: 'numeric', month: '2-digit', day: '2-digit' }"  locale="en-IN"></b-form-datepicker>
                       </div>
                    <div class="datepiker-block">
                        <span>To:&nbsp;</span> <b-form-datepicker id="to" v-model="date_to" :date-format-options="{ year: 'numeric', month: '2-digit', day: '2-digit' }"  locale="en-IN"></b-form-datepicker>
                      </div>
                          <b-button type="submit" variant="primary">Submit</b-button>
                                </b-form>
            </b-col>
          </b-row>
      </div>
    </div>
  </div>
      </br>
      <!-- <div class="card-body card">
           <div class="call-center-dashboard">
               <b-row>
                    
                </b-row>
            </div>
    </div> -->
    <div class="card-body card">
      <div class="card list-appointments">
          <div class="col-sm-12">
              <b-row>
                  <b-col xl="4" lg="4" md="4">
                    <b-form-group class="mb-0">Show <b-form-select id="per-page-select" v-model="perPage" :options="pageOptions"size="sm">
                      </b-form-select> entries
                    </b-form-group>
                  </b-col>
                  <b-col xl="4" lg="4" md="4" class="search_field">
                        <b-form-input id="filter-input" v-model="filter" type="search" placeholder="Type to Search"></b-form-input>
                          <b-input-group-append><b-button :disabled="!filter" @click="filter = ''">Clear</b-button></b-input-group-append>
                    </b-col>
                  <b-col xl="2" lg="2" md="2">
                      <button type="button" class="download-btn btn btn-primary" v-on:click="selectdownload">Download</button>
                  </b-col>
                  <b-col xl="2" lg="2" md="2">
                      <button type="button" class="download-btn btn btn-primary" v-model="statusAssign" v-on:click="addstatus">Change Status</button>
                  </b-col>
        
              </b-row>
          </div>
        </div>
    </div>
      </br>
        <!-- <b-table striped hover responsive :items="items"
                  :sort-by.sync="sortBy"
                  :sort-desc.sync="sortDesc"
                   sort-icon-left :filter-included-fields="filterOn" :filter="filter" :fields="fields" :per-page="perPage" :current-page="currentPage" show-empty>
              <template #empty="scope">
                  <p style="text-align:center;">No record found, choose date filter to found the result.</p>
              </template>
              <template #head(select)="data">
                  <span class="text-info"><input type="checkbox" @click="selectedAll" v-model="allSelected"> {{ data.label }}</span>
              </template>
              <template v-slot:cell(variation_id)="row">
                #{{(row.item.variation_id)}}
              </template>
             
              <template v-slot:cell(select)="row">
                <input type="checkbox" :value="row.item.oid" v-model="selectall" >
              </template>
            </b-table> -->

            <div class="profile_info">
                <h3 class="own-heading"><router-link :to="{ name: 'productprofile', params: { variation_id: (this.variation_id).toString() }}"></router-link></strong></h3>
            </div>
          </b-col>
        </b-row>
        
      </div>
    </div>
    <div class="tenpxdiv"></div>
    <div class="content_bar">
      <div class="card-body">
        <b-row>
          <b-col xl="12" lg="12" md="12">
            <b-table striped hover responsive :items="items"
                  :sort-by.sync="sortBy"
                  sort-icon-left :filter-included-fields="filterOn" 
                  :filter="filter" :fields="fields" :per-page="perPage" 
                  :current-page="currentPage" show-empty>
                  <template v-slot:cell(sr)="row">
                    {{((currentPage-1)*perPage)+(row.index)+1}}
                  </template>
                  <template #empty="scope">
                  <p style="text-align:center;">No record found, choose date filter to found the result.</p>
              </template>
                  <template #head(select)="data">
                  <span class="text-info"><input type="checkbox" @click="selectedAll" v-model="allSelected"> {{ data.label }}</span>
              </template>
              <template v-slot:cell(variation_id)="row">
                #{{(row.item.variation_id)}}
              </template>
              <template v-slot:cell(select)="row">
                <input type="checkbox" :value="row.item.oid" v-model="selectall" >
              </template>

           </b-table>
          </b-col>
        </b-row>
      </div>
    </div>
  		
  </b-container>
</template>

<script>
import ProductProfile from '../../api/Product.js';
  export default {

    props: {
        variation_id: {
        type: String,
        required: true
      },
    },
    mounted() {
      this.getOrder();
      this.getOrderItems();
    },
    data() {
      return {
        status: null,
        city: null,
        show: false,
        currentPage: 1,
        image:'',
        rowClass:'',
        image_file:null,
        quantity:'',
        first_name:'',
        last_name:'',
        address_1:'',
        address_2:'',
        email:'',
        payment_method:'',
        state:'',
        // city:'',
        // status:'',
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

        options: [],
        fields: [
          {
          key:'select',
          label: 'Select all',
          sortable:true
        }, 

          {
            key: 'order_id',
            label: 'Order ID',
            sortable: true
          },
          {
            key: 'quantity',
            label: 'Qty',
            sortable: true
          },
          {
            key: 'total',
            label: 'Amount',
            sortable: true
          },
          {
            key: 'state',
            label: 'State',
            sortable: true
          },
          {
            key: 'city',
            label: 'City',
            sortable: true
          },
       
          {
            key: 'date_created_gmt',
            label: 'Order Date ',
            sortable: true
          },
          {
            key: 'status',
            label: 'Status',
            sortable: true
          },
          {
            key: 'action',
            label: 'Action',
            sortable: false
          }
          
        ],
        items: [],
      };
    },
    methods: {
      getOrder() {

          this.vid = localStorage.getItem("ivid");
          ProductProfile.getProductProfile(this.variation_id,this.vid)
          .then(( response ) => {
            if(response.data)
            {
              this.order_id=response.data[0].order_id;
              this.product_id = response.data[0].product_id;
              this.quantity = response.data[0].quantity;
              this.sku = response.data[0].sku;
              this.total = response.data[0].total;
            }
          })
          .catch(error => {
              console.log(error);
              
          });
      },
      goBack(){
        return this.$router.go(-1);
      },
      getOrderItems() {
          ProductProfile.getProductItems(this.variation_id,this.vid) 
          .then(( response ) => {
            if(response.data)
            {
              this.items=response.data;
            }
          })
          .catch(error => {
              console.log(error);
              
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
                this.imageData = e.target.result;
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
      getImgUrl(pet) {
        var images = '/public/uploads/otheruser/'+pet;
        return images;
    },
    },
  };
</script>
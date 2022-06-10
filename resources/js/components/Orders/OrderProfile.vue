<template>
  <b-container fluid>  
    <div class="content_bar card">
      <div class="card-body">
        <b-row>
          <b-col xl="12" lg="12" md="12">
             <template>
                <b-button pill variant="download-btn btn btn-primary" @click="goBack">Go Back</b-button>
             </template>
            <div class="profile_info">
                <h3 class="own-heading"><p class="h2 mb-2"><strong>Order ID:{{oid}}</strong></p> <strong><router-link :to="{ name: 'OrderProfile', params: { oid: (this.oid).toString() }}"></router-link></strong></h3>


            </div>
          </b-col>
        </b-row>
        <b-row>
          <b-col xl="4" lg="4" md="4">
                <h3>General</h3>
                <p><strong>Order Date:</strong> {{date_created_gmt}}</p>
                <p><strong>Status:</strong> {{status}}</p>
                <p><strong>Email:</strong> {{email}}</p>
                <p><strong>Payment Method:</strong> {{payment_method}}</p>
          </b-col>
          <b-col xl="4" lg="4" md="4">
                <h3>Billing</h3>
                <p><strong>First Name:</strong> {{first_name}}</p>
                <p><strong>Last Name:</strong> {{last_name}}</p>
                <p><strong>Address 1:</strong> {{address_1}}</p>
                <p><strong>Address 2:</strong> {{address_2}}</p>
                <p><strong>State:</strong> {{state}}</p>
                <p><strong>City:</strong> {{city}}</p>
          </b-col>
          <b-col xl="4" lg="4" md="4">
                <h3>Shipping</h3>
                <p><strong>First Name:</strong> {{first_name}}</p>
                <p><strong>Last Name:</strong> {{last_name}}</p>
                <p><strong>Address 1:</strong> {{address_1}}</p>
                <p><strong>Address 2:</strong> {{address_2}}</p>
                <p><strong>State:</strong> {{state}}</p>
                <p><strong>City:</strong> {{city}}</p>
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
                  

            </b-table>
          </b-col>
        </b-row>
      </div>
    </div>
  		
  </b-container>
</template>

<script>
import OrderProfile from '../../api/order.js';
  export default {

    props: {
      oid: {
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
        city:'',
        status:'',
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
            key: 'sr',
            label: 'S.No.',
        
          },
          {
            key: 'product_id',
            label: 'Product ID',
            sortable: true
          },
          {
            key: 'variation_id',
            label: 'Variation ID',
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
            key: 'quantity',
            label: 'Quantity',
            sortable: true
          },
          {
            key: 'price',
            label: 'Price',
            sortable: true
          },
          {
            key: 'total',
            label: 'Total',
            sortable: true
          },
          
        ],
        items: [],
      };
    },
    methods: {
      getOrder() {
          this.vid = localStorage.getItem("ivid");
          OrderProfile.getOrderProfile(this.oid,this.vid)
          .then(( response ) => {
            if(response.data)
            {
              this.quantity=response.data[0].quantity;
              this.amount = response.data[0].total;
              this.email = response.data[0].email;
              this.payment_method = response.data[0].payment_method;
              this.first_name = response.data[0].first_name;
              this.last_name = response.data[0].last_name;
              this.address_1 = response.data[0].address_1;
              this.address_2 = response.data[0].address_2;
              this.state = response.data[0].state;
              this.city=response.data[0].city;
              this.date_created_gmt=response.data[0].date_created_gmt;
              this.status=response.data[0].status;
             
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
          OrderProfile.getOrderItems(this.oid,this.vid) 
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
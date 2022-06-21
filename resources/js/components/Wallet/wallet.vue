<template>
  <b-container fluid> 
    <div class="header_title">
      <div class="header_inner">
        <h3><strong>Wallet</strong></h3><br/>
      </div>
    </div>
    
    <div class="content_bar ">
      <div class="card-body card">
          <div class="call-center-dashboard">
              <b-row>
                  <b-col xl="8" lg="8" md="8">
                      <b-alert show variant="danger" v-if="create_error">{{create_error}}</b-alert>
                      <b-form @submit="onSubmit" class="date_range">
                          <div class="datepiker-block">
                              <span>From:&nbsp;</span> <b-form-datepicker id="from" v-model="date_from" :date-format-options="{ year: 'numeric', month: '2-digit', day: '2-digit' }" locale="en-IN"></b-form-datepicker>
                          </div>
                          <div class="datepiker-block"><span>To:&nbsp;</span> <b-form-datepicker id="to" v-model="date_to" :date-format-options="{ year: 'numeric', month: '2-digit', day: '2-digit' }" locale="en-IN"></b-form-datepicker></div>
                          <b-button type="submit" variant="primary">Submit</b-button>
                      </b-form>
                  </b-col>
                  <b-col xl="4" lg="4" md="4" class="search_field">
                      <b-form-input id="filter-input" v-model="filter" type="search" placeholder="Type to Search"></b-form-input>
                      <b-input-group-append><b-button :disabled="!filter" @click="filter = ''">Clear</b-button></b-input-group-append>
                  </b-col>
              </b-row>
              <div class="blue-bar"></div>
              <div class="content_bar card list-appointments space-bottom">
                  <div class="col-sm-12">
                      <b-row>
                          <b-col xl="5" lg="5" md="5">
                              <b-form-group class="mb-0"> Show <b-form-select id="per-page-select" v-model="perPage" :options="pageOptions" size="sm"></b-form-select> entries </b-form-group>
                          </b-col>
                      </b-row>
                  </div>
              </div>
          </div>
      </div>
      <br>
         <b-table striped hover responsive :items="items"
      :sort-by.sync="sortBy"
      :sort-desc.sync="sortDesc"
      sort-icon-left :filter-included-fields="filterOn" :filter="filter" :fields="fields" :per-page="perPage" :current-page="currentPage" show-empty>
            <template #empty="scope">
                <p style="text-align:center;">No record found, choose date filter to found the result.</p>
            </template>
            <template v-slot:cell(oid)="row">
              {{(row.item.Order_id)}}
            </template>
            <template v-slot:cell(status)="row">
              <span :class="row.item.status"> {{(row.item.status)}}</span>
            </template>
              
          </b-table>
          <div class="text-center" v-if="seen">
  <b-spinner variant="primary" label="Text Centered"></b-spinner>
</div>
  <b-pagination v-model="currentPage"
       :total-rows="rows"
      :per-page="perPage"
      aria-controls="my-table"
    ></b-pagination>
    </div>
</b-container>
</template>

<script>

    import wallet from '../../api/wallet.js'; 
    import user from '../../api/user.js';
  export default {
    props: {
    },
    mounted() {
      
      this.getVidz();
    },
    data() 
    {
      return {
        //ariaDescribedby: "",
        time: "",
        date: "",
        oid:"",
        vid: 0,
        time_slots: [],
        seen: false,
        date_from: '',
        date_to: '',
        sortBy: 'date',
        sortDesc: true,
         perPage: 10,
        currentPage: 1,
        pageOptions: [5, 10, 15, 20, 50, 100],
        filter: null,
        filterOn: [],
        fields: [
          {
            key: 'date_created',
            label: 'Date',
            sortable: true
          },
          
          {
            key: 'id',
            label: 'Description',
            sortable: true
          },
          {
            key: 'discount_total',
            label: 'Service Charges',
            sortable: true
          },
          {
            key: 'orderno',
            label: 'Order No',
            sortable: true
          },
          {
            key: 'payment_method_title',
            label: 'Pay Mode',
            sortable: true
          },
          {
            key: 'status',
            label: 'Status',
            sortable: true
          },
          {
            key: 'total',
            label: 'Sale Amount',
            sortable: true
          },
          {
            key: 'cart_tax',
            label: 'Wallet Used',
            sortable: true
          },
          {
            key: 'shipping_tax',
            label: 'Logistic Cost',
            sortable: true
          },
         {
            key: 'gateway_cost',
            label: 'Payment Gateway Charges',
            sortable: true
          },
          {
           key: 'prices_include_tax',
           label: 'SMS Cost',
           sortable: true
           },
          {
            key: 'm_cost',
            label: 'Majime Charges',
            sortable: true
          },
          {
            key: 'net_amount',
            label: 'Net Amount',
            sortable: true
          },
          {
            key: 'subtotal_tax',
            label: 'Current Wallet Balance',
            sortable: true
          },
          {
            key: 'quantity',
            label: 'Order Count',
            sortable: true
          }
        
        ],
        items: [],
        errors_create:[],
        successful: false,
        create_error:'',
      };
    },
computed: {
      rows() {
        return this.items.length
      }
    },
  methods:{
   
    onSubmit(event) {
      event.preventDefault()
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
      let formData = new FormData();
      formData.append("date_from", this.date_from);
      formData.append("date_to", this.date_to);
      
      appointment
          .appointmentSearch(formData)
          .then((response) => {
              
                  this.items=response.data.data;
           
          })
          .catch((error) => {
              console.log(error);
              if (error.response.status == 422) {
                  this.errors_create = error.response.data.errors;
              }
          });
    },
  getVidz()
           {
            let formData= new FormData();
            formData.append("user_id", this.$userId);
            user.getVid(formData)
             .then(( response ) => {
                this.vid = response.data;
                localStorage.setItem("ivid", this.vid);
                this.getWalletDetail(this.vid);
                // this.allSelected=-false;
              })
              .catch(response => {
                  this.successful = false;
                  alert('something went wrong');
              })
           },

  getWalletDetail(vid) {
        let formData = new FormData();
            formData.append('vid', vid);
            formData.append('type','get');
      wallet.getWalletdetail(formData)
        .then(( response ) => {
          console.log(response);
          this.items=response.data;
          // this.countrys = response.data.data;
          console.log(this.items);
          //this.seen = false;
        })
        .catch(response => {
            this.successful = false;
            alert('something went wrong');
        })
    }
  }
};  
</script>

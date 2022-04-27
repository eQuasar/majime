<template>
  <b-container fluid> 
    <b-overlay :show="show" rounded="sm">
    <div class="header_title">
      <div class="header_inner">
        <b-row>
          <b-col xl="12" lg="12" md="12">
           <h3 class="font-weight-bold">Welcome {{user.name}}</h3>
            <h6 class="font-weight-normal mb-0">All systems are running smoothly! You have <span class="text-primary">3 unread alerts!</span></h6>
          </b-col>
          <!--<b-col xl="4" lg="4" md="4">
            <b-dropdown id="dropdown-1" text="Today (10 Jan 2021)" class="m-md-2">
              <b-dropdown-item>January - March</b-dropdown-item>
              <b-dropdown-item>March - June</b-dropdown-item>
              <b-dropdown-item>June - September</b-dropdown-item>
              <b-dropdown-item>September - December</b-dropdown-item>
            </b-dropdown>
          </b-col>-->
        </b-row>       
      </div>
    </div>
    </b-overlay>
</b-container>
</template>
<template>
  <b-container fluid> 
    <div class="header_title">
      <div class="header_inner">
        <h3><strong>Wallet Deatil</strong></h3><br/>
      </div>
    </div>
    
    <div class="content_bar card">
        <div class="card-body">
          <div class="call-center-dashboard">
          <b-col xl="10" lg="10" md="10">
            <b-alert show variant="danger" v-if="create_error">{{create_error}}</b-alert>
            <b-form @submit="onSubmit" class="date_range" @reset="onReset">
              
              <div class="datepiker-block">
                <span>From:&nbsp;</span>  <b-form-datepicker  id="from" v-model="date_from" :date-format-options="{ year: 'numeric', month: '2-digit', day: '2-digit' }"  locale="en-IN"></b-form-datepicker>
              </div>
              <div class="datepiker-block">
                <span>To:&nbsp;</span> <b-form-datepicker  id="to" v-model="date_to" :date-format-options="{ year: 'numeric', month: '2-digit', day: '2-digit' }"  locale="en-IN"></b-form-datepicker>
              </div>
              <b-button type="submit" variant="primary">Submit</b-button>
              <!--<b-button type="reset" variant="danger" style="float: right;margin-right: 10px;">Reset</b-button>-->
            </b-form>
          </b-col>
         <div class="blue-bar"></div>
        <div class="content_bar card list-appointments space-bottom">
          <div class="col-sm-12">
            <b-row>
              <b-col xl="5" lg="5" md="5">
                <b-form-group
                  class="mb-0"
                >
                 Show <b-form-select
                    id="per-page-select"
                    v-model="perPage"
                    :options="pageOptions"
                    size="sm"
                  ></b-form-select> entries
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
                    <b-button :disabled="!filter" @click="filter = ''">Clear</b-button>
                  </b-input-group-append>
              </b-col>
            </b-row>
          </div>
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
              {{(row.item.Order_id)}}
            </template>
             <template v-slot:cell(action)="row">
              <router-link :to="{ name: 'p', params: { clientid: (row.item.id).toString() }}"><b-icon icon="eye-fill" aria-hidden="true"></b-icon></router-link>&nbsp;&nbsp;<router-link :to="{ name: 'editgroomer', params: { clientid: (row.item.id).toString() }}"><b-icon icon="fa fa-eye" aria-hidden="true"></b-icon></router-link>
              <!-- <b-icon icon="trash-fill" aria-hidden="true"></b-icon> -->
            </template>  
          </b-table>
</div>
</div>
    </b-modal>
</b-container>
</template>

<script>
      import wallet from '../../api/wallet.js';
    // import timeslot from "../../api/timeslot.js";
// import appointment from '../../api/appointment.js';
  //import wallet from '../../api/wallet.js';
 export default {

    props: {
    },
    mounted() {

      // this.appointment();
      this.getWalletDetail();
    },
    data() 
    {
      return {
        ariaDescribedby: "",
        time: "",
        date: "",
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
            key: 'date_created_gmt',
            label: 'Date',
            sortable: true
          },
          {
            key: 'transaction_id',
            label: 'Transaction Id',
            sortable: true
          },
          {
            key: 'payment_method_title',
            label: 'Description',
            sortable: true
          },
          {
            key: 'subtotal',
            label: 'Amount',
            sortable: true
          },
          {
            key: 'total',
            label: 'Balance',
            sortable: true
          },
          
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
  methods: {
    
    
    getWalletDetail() {
      // this.seen = true;
      wallet.getWalletdetail()
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

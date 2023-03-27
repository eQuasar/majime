<template>
  <b-container fluid> 
    <div class="header_title">
      <div class="header_inner">
        <h3><strong>Report</strong></h3><br/>
      </div>
    </div>
    
    <div class="content_bar card">
        <div class="card-body">
          <div class="call-center-dashboard">
            <b-row>
          <b-col xl="8" lg="8" md="8">
            <b-alert show variant="danger" v-if="create_error">{{create_error}}</b-alert>
            <b-form @submit="onSubmit" class="date_range">
              
              <div class="datepiker-block">
                <span>From:&nbsp;</span>  <b-form-datepicker  id="from" v-model="date_from" :date-format-options="{ year: 'numeric', month: '2-digit', day: '2-digit' }"  locale="en-IN"></b-form-datepicker>
              </div>
              <div class="datepiker-block">
                <span>To:&nbsp;</span> <b-form-datepicker  id="to" v-model="date_to" :date-format-options="{ year: 'numeric', month: '2-digit', day: '2-digit' }"  locale="en-IN"></b-form-datepicker>
              </div>
              <b-button type="submit" variant="primary">Submit</b-button>
            </b-form>
          </b-col>
          <b-col xl="2" lg="2" md="2">
              <b-form-select v-model="status" @change="getstatus" style="height: auto !important;">
                <option :value="null">Status</option>
                <option value="1">intransit</option>
                <option value="2">confirmed</option>
                <option value="3">dtobooked</option>
                <option value="4">cancelled</option>
              </b-form-select>
            </b-col>
          </b-row>
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
              {{(row.item.oid)}}
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
    </div>
</b-container>
</template>


<script>
  import report from '../../api/report.js';
  export default {

    props: {
    },
    mounted() {
      this.reportdetail();
    },
    data() 
    {
      return {
        status:null,
        selected: null,
        options: [
          { value: null, text: 'Status' },
        
        ],
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
            key: 'oid',
            label: 'Order NO',
            sortable: true
          },
          {
            key: 'first_name',
            label: 'Customer Detail',
            sortable: true
          },
          {
            key: 'name',
            label: 'Category',
            sortable: true
          },
          {
            key: 'payment_method',
            label: 'Payment',
            sortable: true
          },
          {
            key: 'date_created_gmt',
            label: 'Dispatch Date',
            sortable: true
          },
          {
            key: 'city',
            label: 'State/City',
            sortable: true
          },
          {
            key: 'status',
            label: 'Order Status',
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
  methods:{
    getstatus()
    {
      let formData = new FormData();
      formData.append('status', this.status);
      formData.append('type', 'get');
      console.log(this.status);
      report.getStatusDetails(formData)
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
    },
   
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
  
    reportdetail() {
      // this.seen = true;
      report.getReportDetail()
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
        });

    }
  }
};  
</script>

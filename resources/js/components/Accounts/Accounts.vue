<template>
  <b-container fluid> 
    <div class="header_title">
      <div class="header_inner">
        <h3><strong>Accounts Detail</strong></h3><br/>
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
    <b-modal id="modal-1" title="Appointment Assign to:" hide-footer @hidden="clearData" size="lg">
      <b-form>
        <b-alert show variant="danger" v-if='create_error'>{{create_error}}</b-alert>
        <div :class="['form-group m-1 p-3', (successful ? 'alert-success' : '')]" v-show="successful">
          <span v-if="successful" class="label label-sucess">Published!</span>
        </div>
        <b-form-input v-model="appointment_id" id="appointment_id_val" style="display:none"></b-form-input>
        <b-form-select v-model="vehicle_assign" class="" :options="vehicle_assign_array" value-field="id" text-field="vehicle_number">
          <template v-slot:first>
            <b-form-select-option :value="0" disabled>-- Select Vehicle --</b-form-select-option>
          </template>
        </b-form-select>
        
        <b-button type="submit" @click.prevent="assign_appointment" variant="primary">Submit </b-button>
      </b-form> 
    </b-modal>
    <b-modal id="modal-2" title="Appointment Schedule to:" hide-footer @hidden="clearData" size="lg">
      <b-form>
        <b-alert show variant="danger" v-if='create_error'>{{create_error}}</b-alert>
        <div :class="['form-group m-1 p-3', (successful ? 'alert-success' : '')]" v-show="successful">
          <span v-if="successful" class="label label-sucess">Published!</span>
        </div>
        <b-form-input v-model="appointment_id" id="appointment_id_val" style="display:none"></b-form-input>
        <div class="datepiker-block">
            <label class="label_datepicker" for="example-datepicker">Choose Date & Time</label>
            <b-form-datepicker id="example-datepicker" v-model="date" class="mb-2"></b-form-datepicker>
        </div>
        <b-form-group label="" v-slot="{ ariaDescribedby }">
            <b-form-radio-group
                id="time-radios-btn"
                v-model="time"
                :aria-describedby="ariaDescribedby"
                button-variant="outline-primary"
                size="lg"
                :options="time_slots"
                value-field="id"
                text-field="time"
                name="radio-btn-outline"
                buttons
            >
            </b-form-radio-group>
        </b-form-group>
        
        <b-button type="submit" @click.prevent="schedule_appointment" variant="primary">Submit </b-button>
      </b-form> 
    </b-modal>
</b-container>
</template>

<script>
    // import appointment from '../../api/appointment.js';
    import order from '../../api/order.js';
    import Product from '../../api/Product.js';
    import accounts from '../../api/accounts.js';
  export default {

    props: {
    },
    mounted() {

      // this.appointment();
      this.AccountsDetail();
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
            key: 'id',
            label: 'Date',
            sortable: true
          },
          {
            key: 'quantity',
            label: 'Type',
            sortable: true
          },
          {
            key: 'Booing Date',
            label: 'Invoice No',
            sortable: true
          },
          {
            key: 'total',
            label: 'Taxable Amount',
            sortable: true
          },
          {
            key: 'date_created_gmt',
            label: 'IGST',
            sortable: true
          },
          {
            key: 'subtotal',
            label: 'CGST',
            sortable: true
          },
          {
            key: 'status',
            label: 'SGST',
            sortable: true
          },
          {
            key: 'status',
            label: 'Total Amount',
            sortable: true
          },
          {
            key: 'status',
            label: 'Status',
            sortable: true
          },
          
          {
            key: 'action',
            label: 'Download',
            sortable: false
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
  
   
    AccountsDetail() {
      // this.seen = true;
      accounts.getAccountsDetail()
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

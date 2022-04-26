<template>
  <b-container fluid> 
    <div class="header_title">
      <div class="header_inner">
        <h3><strong>Reports</strong></h3><br/>
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
              <router-link :to="{ name: 'orderdetail', params: { clientid: (row.item.id).toString() }}"><b-icon icon="eye-fill" aria-hidden="true"></b-icon></router-link>&nbsp;&nbsp;<router-link :to="{ name: 'ProductList', params: { clientid: (row.item.id).toString() }}"><b-icon icon="pencil-fill" aria-hidden="true"></b-icon></router-link>
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
  import report from '../../api/report.js';
  export default {

    props: {
    },
    mounted() {

      // this.appointment();
      this.reportdetail();
      //this.previewImage();
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
            label: 'Order NO',
            sortable: true
          },
          {
            key: 'quantity',
            label: 'Customer Detail',
            sortable: true
          },
          {
            key: 'Booing Date',
            label: 'Category',
            sortable: true
          },
          {
            key: 'total',
            label: 'Payment',
            sortable: true
          },
          {
            key: 'date_created_gmt',
            label: 'Dispatch Date',
            sortable: true
          },
          {
            key: 'subtotal',
            label: 'State/City',
            sortable: true
          },
          {
            key: 'status',
            label: 'Order Status',
            sortable: true
          },

          {
            key: 'action',
            label: 'Action',
            sortable: false
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
  methods: {
   
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
                  
              // }
          })
          .catch((error) => {
              console.log(error);
              if (error.response.status == 422) {
                  this.errors_create = error.response.data.errors;
              }
              // loader.hide();
          });
      // console.log(this.date_from);
      // console.log(this.date_to);
      // alert(JSON.stringify(this.form))
    },
    onReset(event) {
      event.preventDefault()
      // Reset our form values
      this.date_to = '';
      this.date_from = '';
      this.appointment();
    },
    clearData()
    {
      this.appointment_id ='';
    },
    addinfo(id){
        //alert(id);
        
        this.appointment_id = id;
        let formData = new FormData();
        formData.append("id", id);
      appointment
          .freeVehicle(formData)
          .then((response) => {
              
                  this.vehicle_assign_array=response.data.data;
                  this.$bvModal.show("modal-1");
              // }
          })
          .catch((error) => {
              // console.log(error);
              if (error.response.status == 422) {
                  this.errors_create = error.response.data.errors;
              }
              // loader.hide();
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


  }

};  
</script>

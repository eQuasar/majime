<template>
  <b-container fluid> 
    <div class="header_title">
      <div class="header_inner">
        <b-row>
          <b-col xl="12" lg="12" md="12">
            <h3 class="font-weight-bold">Welcome Aamir</h3>
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
    <div class="clear">&nbsp;</div>
    <div class="row">
      <div class="col-md-12 grid-margin transparent">
        <div class="row">
          <div class="col-md-4 col-lg-4 mb-4 stretch-card transparent">
            <div class="card card-tale">
              <div class="card-body">
                <p class="mb-4">Today’s Appointment</p>
                <p class="fs-30 mb-2">4006</p>
                <p>10.00% (30 days)</p>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-lg-4 mb-4 stretch-card transparent">
            <div class="card card-dark-blue">
              <div class="card-body">
                <p class="mb-4">Total Appointment</p>
                <p class="fs-30 mb-2">61344</p>
                <p>22.00% (30 days)</p>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-lg-4 mb-4 stretch-card transparent">
            <div class="card card-light-danger">
              <div class="card-body">
                <p class="mb-4">Accept Appointment</p>
                <p class="fs-30 mb-2">47033</p>
                <p>0.22% (30 days)</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <b-row>
      <b-col xl="8" lg="8" md="8">
        <div class="content_bar card list-appointments">
          <div class="card-body">
            <b-row>
              <b-col xl="12" lg="12" md="12">
                <b-alert show variant="danger" v-if="create_error">{{create_error}}</b-alert>
                <b-form @submit="onSubmit" class="date_range" @reset="onReset">
                  <div class="datepiker-block">
                    <span>From:&nbsp;</span>  <b-form-datepicker  id="from" v-model="date_from" :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"  locale="en-IN"></b-form-datepicker>
                  </div>
                  <div class="datepiker-block">
                    <span>To:&nbsp;</span> <b-form-datepicker  id="to" v-model="date_to" :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"  locale="en-IN"></b-form-datepicker>
                  </div>
                  <b-button type="submit" variant="primary">Submit</b-button>
                  <!--<b-button type="reset" variant="danger" style="float: right;margin-right: 10px;">Reset</b-button>-->
                </b-form>
              </b-col>
            </b-row>
            <hr/>
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
        <div class="clear">&nbsp;</div>
        <div class="content_bar card">
            <div class="card-body">
              <h4>Appointment Lists</h4>
             <b-table striped hover responsive :items="items"
          :sort-by.sync="sortBy"
          :sort-direction="sortDirection"
          sort-icon-left :filter-included-fields="filterOn" :filter="filter" :fields="fields" :per-page="perPage" :current-page="currentPage" show-empty>
                <template #empty="scope">
                    <p style="text-align:center;">No record found, choose date filter to found the result.</p>
                </template>
                <template v-slot:cell(sr)="row">
                   {{((currentPage-1)*perPage)+(row.index)+1}}
                </template>
                <template v-slot:cell(client)="row">
                  <div>
                    <span :id="'button-'+row.index">{{(row.item.client.user.name)}}</span>
                    <b-popover :target="'button-'+row.index" triggers="hover" placement="top">
                      {{(row.item.client.address)}}, {{row.item.client.state.name}} {{row.item.client.city.name}}, {{row.item.client.pincode}}
                    </b-popover>
                  </div>
                </template>
                <template v-slot:cell(pet)="row">
                  {{(row.item.pet.name)}}
                </template>
                <template v-slot:cell(vehicle)="row">
                  {{(row.item.vehicle.name)}}
                </template>
                <template v-slot:cell(phone)="row">
                  {{(row.item.client.user.phone)}}
                </template>
                <template v-slot:cell(time)="row">
                  {{(row.item.time.start_time)}} - {{row.item.time.end_time}}
                </template>
                <template v-slot:cell(date)="row">
                  {{row.item.date}}
                </template>
                <template v-slot:cell(status)="row">
                  <span v-if="row.item.status ==1" class="legend-dots" style="background: rgb(255, 193, 0);"></span>
                  <span v-if="row.item.status ==2" class="legend-dots" style="background: rgb(255, 71, 71);"></span>
                  
                </template>
                <template v-slot:cell(assign_vehicle)="row">
                <span v-if="row.item.assign_vehicle != null">  {{row.item.assign_vehicle.name}}-{{row.item.assign_vehicle.vehicle_number}}</span>
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
      </b-col>
      <b-col xl="4" lg="4" md="4">
        <div class="content_bar card">
          <div class="card-body">
            <h4>Upcoming Appointment</h4>
          </div>
        </div>
      </b-col>
    </b-row>
</b-container>
</template>

<script>
    import timeslot from "../api/timeslot.js";
import appointment from '../api/appointment.js';
  export default {

    props: {
    },
    mounted() {

            this.gettimeslot();
      this.appointment();
    },
    data() 
    {
      return {
        ariaDescribedby: "",
        time: "",
        date: "",
        time_slots: [],
        seen: false,
        vehicle_assign:0,
        vehicle_assign_array:[],
        successful:false,
        vehicle_id:0,
        appointment_id:0,
        create_error: "",
        date_from: '',
        date_to: '',
        sortBy: 'date',
        sortDirection: 'asc',
        sortDesc: true,
         perPage: 10,
        currentPage: 1,
        pageOptions: [5, 10, 15, 20, 50, 100],
        filter: null,
        filterOn: [],
        fields: [
          {
            key: 'sr',
            sortable: true
          },
          {
            key: 'client',
            label: 'Client Name',
            sortable: true
          },
          {
            key: 'pet',
            label: 'Pet Name',
            sortable: true
          },
          {
            key: 'vehicle',
            label: 'Vehicle',
            sortable: true
          },
          {
            key: 'phone',
            label: 'Phone',
            sortable: true
          },
          {
            key: 'time',
            label: 'Time',
            sortable: true
          },
          {
            key: 'date',
            label: 'Date',
            sortable: true
          },
          {
            key: 'assign_vehicle',
            label: 'Assign To',
            sortable: true
          },
          {
            key: 'status',
            label: 'Status',
            sortable: true
          },
        ],
        items: [],
      };
    },
computed: {
      rows() {
        return this.items.length
      }
    },
  methods: {
    gettimeslot() {
        let formData = new FormData();
        formData.append("type", "get_active");
        timeslot
            .addTimeSlot(formData)
            .then((response) => {
                this.time_slots = response.data.data;
            })
            .catch((error) => {
                console.log(error);
                if (error.response.status == 422) {
                    this.errors_create = error.response.data.errors;
                }
            });
    },
    assign_appointment() 
    {
      let formData = new FormData();
        formData.append("id", this.appointment_id);
        formData.append("vehicle_assign", this.vehicle_assign);
      appointment
          .assignAppointment(formData)
          .then((response) => {
                  this.$bvModal.hide("modal-1");
                  this.appointment();
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
    schedule_appointment() 
    {
      let formData = new FormData();
        formData.append("id", this.appointment_id);
        formData.append("time", this.time);
        formData.append("date", this.date);
      appointment
          .assignAppointment(formData)
          .then((response) => {
                  this.$bvModal.hide("modal-2");
                  this.appointment();
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
    scheduleinfo(id){
        //alert(id);
        
        this.appointment_id = id;
        let formData = new FormData();
        formData.append("id", id);
        this.$bvModal.show("modal-2");
    },
    appointment(address) {
      
      this.seen = true;
      appointment.getAppointments()
        .then(( response ) => {
          this.items=response.data.data;
          // this.countrys = response.data.data;
          this.seen = false;
        })
        .catch(response => {
            this.successful = false;
            alert('something went wrong');
        })
    }
  }
};  
</script>

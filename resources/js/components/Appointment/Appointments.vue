<template>
  <b-container fluid> 
    <div class="header_title">
      <div class="header_inner">
        <h3><strong>List Orders/Products</strong></h3><br/>
      </div>
    </div>
    
    <!-- <div class="content_bar card">
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
            <template v-slot:cell(action)="row">
                <span v-if="$route.name == 'appointments'">
                <router-link :to="{ name: 'viewappointment', params: { appointmentid: (row.item.id).toString() }}"><b-icon icon="eye-fill" aria-hidden="true" data-toggle="tooltip" title="View"></b-icon></router-link>&nbsp;&nbsp;
                <router-link :to="{ name: 'editappointment', params: { appointmentid: (row.item.id).toString() }}"><b-icon icon="pencil-fill" aria-hidden="true" data-toggle="tooltip" title="Edit"></b-icon></router-link>&nbsp;&nbsp;
                </span>
                <span v-if="$route.name == 'serviceappointments'">
                <router-link :to="{ name: 'serviceviewappointment', params: { appointmentid: (row.item.id).toString() }}"><b-icon icon="eye-fill" aria-hidden="true" data-toggle="tooltip" title="View"></b-icon></router-link>&nbsp;&nbsp;
                <router-link :to="{ name: 'serviceeditappointment', params: { appointmentid: (row.item.id).toString() }}"><b-icon icon="pencil-fill" aria-hidden="true" data-toggle="tooltip" title="Edit"></b-icon></router-link>&nbsp;&nbsp;
                </span>
                <b-link @click="addinfo(row.item.id)"><b-icon icon="truck" aria-hidden="true" data-toggle="tooltip" title="Assign Van"></b-icon></b-link>&nbsp;&nbsp;<b-link @click="scheduleinfo(row.item.id)"><b-icon icon="clock-fill" aria-hidden="true" data-toggle="tooltip" title="Schedule"></b-icon></b-link>&nbsp;&nbsp;<b-link @click="cancel_appointment(row.item.id, row.index)"><b-icon icon="trash-fill" aria-hidden="true" data-toggle="tooltip" title="Suspend"></b-icon></b-link>
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
      </b-form>  -->
    </b-modal>
</b-container>
</template>

<script>
    import timeslot from "../../api/timeslot.js";
import appointment from '../../api/appointment.js';
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
        min: new Date(),
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
        sortDesc: true,
        sortDirection: 'asc',
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
        reason:'',
        comment:'',
        user_id:0,

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
    schedule_appointment() 
    {
        let formData = new FormData();
        formData.append("id", this.appointment_id);
        formData.append("time", this.time);
        formData.append("date", this.date);
        appointment
          .scheduleAppointment(formData)
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
    cancel_appointment(id, index) 
    {
        let formData = new FormData();
        formData.append("id", id);
        formData.append("reason", this.reason);
        formData.append("user_id", this.$userId);
        formData.append("comment", this.comment);
        appointment
          .cancelAppointment(formData)
          .then((response) => {
                  // this.$bvModal.hide("modal-2");
                  this.appointment();
                  this.rows.splice(index, 1);
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
        let obj = (this.items).find(o => o.id === id);
        console.log(obj);
        this.date = obj.date_simple;
        this.time = obj.time.id;
        this.appointment_id = id;
        let formData = new FormData();
        formData.append("id", id);
        this.$bvModal.show("modal-2");
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

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

<script>
    import client from "../../api/clients.js";
    import timeslot from "../../api/timeslot.js";
import appointment from '../../api/appointment.js';
  export default {

    props: {
      user: {
        type: Object,
        required: false
      },
      
    },
    mounted() {
      this.gettimeslot();
      this.countClient();
      this.appointment();
      this.upcomingappointment();
    },
    data() 
    {
      return {
        max: new Date(),
        min: new Date(),
        show: false,
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
        date_from: new Date('Y/m/d'),
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
        ],
        fields_upcoming: [
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
            key: 'status',
            label: 'Status',
            sortable: true
          },
          
        ],
        ls_items:[],
        items: [],
        suggestions:[],
        items_upcoming:[],
        clientcount:0,
      };
    },
computed: {
      rows() {
        return this.items.length
      }
    },
  methods: {
    getClient(address) {
        this.show = true;
        client
            .getClients()
            .then((response) => {
                //this.suggestions = response.data.data;
                //var ls_items = JSON.parse(localStorage.getItem("suggestions"));
                //if(ls_items['length'] != response.data.data['length']){
                  localStorage.setItem("suggestions", JSON.stringify(response.data.data));
                //}else{
                  //console.log("Data Already same.");
                //}
                this.show = false;
                // this.countrys = response.data.data;
            })
            .catch((response) => {
                this.successful = false;
                alert("something went wrong");
            });
    },
    async countClient(){
      this.show = true;
      client
            .countClients()
            .then((response) => {
              this.clientcount = response.data;
              var ls_items = JSON.parse(localStorage.getItem("suggestions"));
              if(ls_items == null){
                this.getClient();
              }else{
                if(ls_items['length'] != response.data){
                  this.getClient();
                }else{
                  this.show = false;
                }
              }
            })
            .catch((response) => {
                this.successful = false;
                alert("something went wrong");
            });
    },
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
    scheduleinfo(id){
        //alert(id);
        
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
    },
    upcomingappointment(address) {
      
      this.seen = true;
      appointment.upcomingAppointments()
        .then(( response ) => {
          this.items_upcoming=response.data.data;
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

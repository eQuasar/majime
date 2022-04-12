<template>
    <b-container fluid>
        <div class="header_title">
            <div class="header_inner">
                <h3><strong>View Appointment</strong></h3>
                <br />
            </div>
        </div>
        <div class="content_bar view-appointment">
            <div class="appointment-inner" v-if="detail != null">
                <b-alert show variant="danger" v-if="create_error">{{create_error}}</b-alert>
                <div :class="['form-group m-1 p-3', (successful ? 'alert-success' : '')]" v-show="successful">
                    <span v-if="successful" class="label label-sucess">Published!</span>
                </div>
                <b-row>
                  <b-col xl="6" lg="6" md="6">
                    <div class="card">
                      <div class="card-body">
                        <h4><strong>Service Detail</strong></h4>
                        <p><strong>Service:</strong>
                        <span v-for="(serv, index) in detail.items">{{serv.service.name}}, </span> 
                        
                        </p>
                        <!-- <p><strong>Additional Service:</strong> Hair Cut, Nail Cut</p> -->
                        <p><strong>Vehicle:</strong> {{detail.vehicle.name}}</p>
                        <p><strong>Date:</strong> {{detail.date}}</p>
                        <p><strong>Time:</strong> {{detail.time.start_time}} - {{detail.time.end_time}}</p>
                        <p><strong>Service Charges:</strong> {{detail.total_cost}}</p>
                      </div>
                    </div>
                  </b-col>
                  <b-col xl="6" lg="6" md="6">
                    <div class="card">
                      <div class="card-body">
                        <h4><strong>Pet Detail</strong></h4>
                        <p><strong>Name:</strong> {{detail.pet.name}} ({{detail.pet.category.name}})</p>
                        <p><strong>Breed:</strong> {{detail.pet.breed.name}}</p>
                        <p><strong>DOB:</strong> {{detail.pet.dob}}</p>
                        <p><strong>Coat Level:</strong> {{detail.pet.coat.name}}</p>
                        <p><strong>Agressive Level:</strong> {{detail.pet.aggresive_level.name}}</p>
                      </div>
                    </div>
                  </b-col>
                  <div class="clear">&nbsp;</div>
                  <b-col xl="6" lg="6" md="6">
                    <div class="card">
                      <div class="card-body">
                        <h4><strong>Client Detail</strong></h4>
                        <p><strong>Name:</strong> {{detail.client.user.name}}</p>
                        <p><strong>Email:</strong> {{detail.client.user.email}}</p>
                        <p><strong>Phone:</strong> {{detail.client.user.phone}}</p>
                        <p><strong>DOB:</strong> {{detail.client.dob}}</p>
                        <p><strong>Address:</strong> {{detail.client.address}} {{detail.client.state.name}},{{detail.client.city.name}},{{detail.client.pincode}}</p>
                        <p v-if="detail.client.alternate_address"><strong>Alternate Address:</strong> {{detail.client.alternate_address}}</p>
                        <p v-if="detail.client.alternate_phone"><strong>Alternate Phone Number:</strong> {{detail.client.alternate_phone}}</p>
                      </div>
                    </div>
                  </b-col>
                </b-row>
            </div>
        </div>
    </b-container>
</template>
<script>
    import appointment from '../../api/appointment.js';
    
    export default {
        props: {
            appointmentid: {
              type: String,
              required: true
            },
          },
        
        mounted() {
           
            this.getappointment();
            
        },
        data() {
            return {
                detail:null,
                isHidden: false,
                ariaDescribedby: "",
                time: "",
                pet_select: "",
                date: "",
                additional_service: [],
                additional_service_array: [],
                vehicle: "",
                bath: "",
                query: "",
                selected: "",
                suggestions: [],
                pets: [],
                service_mode: [],
                service_array: [],
                time_slots: [],
                total_cost: 0,
                create_error: "",
                successful: false,

            };
        },
        computed: {
        
        },
        methods: {
            
            getImgpet(pet) {
                var images = "/public/uploads/clients/pet/" + pet;
                return images;
            },
            getappointment(address) {
      
              appointment.getAppointment(this.appointmentid)
                .then(( response ) => {
                  this.detail=response.data.data;
                  // this.countrys = response.data.data;
                })
                .catch(response => {
                    this.successful = false;
                    alert('something went wrong');
                })
            }
        },
    };
</script>
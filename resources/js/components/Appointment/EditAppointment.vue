<template>
    <b-container fluid>
        <div class="header_title">
            <div class="header_inner">
                <h3><strong>Edit Appointment</strong></h3>
                <br />
            </div>
        </div>
        <div class="content_bar card">
            <div class="card-body" v-if="detail != null">
                <b-alert show variant="danger" v-if="create_error">{{create_error}}</b-alert>
                <div :class="['form-group m-1 p-3', (successful ? 'alert-success' : '')]" v-show="successful">
                    <span v-if="successful" class="label label-sucess">Published!</span>
                </div>
                <b-row>
                	<b-col xl="6" lg="6" md="6">
                		<h4><strong>Client Detail</strong></h4>
                		<p><strong>Name:</strong> {{detail.client.user.name}}</p>
                		<p><strong>Email:</strong> {{detail.client.user.email}}</p>
                		<p><strong>Phone:</strong> {{detail.client.user.phone}}</p>
                		<p><strong>DOB:</strong> {{detail.client.dob}}</p>
                		<p><strong>Address:</strong> {{detail.client.address}} {{detail.client.state.name}},{{detail.client.city.name}},{{detail.client.pincode}}</p>
                        <p v-if="detail.client.alternate_address"><strong>Alternate Address:</strong> {{detail.client.alternate_address}}</p>
                        <p v-if="detail.client.alternate_phone"><strong>Alternate Phone Number:</strong> {{detail.client.alternate_phone}}</p>
                	</b-col>
                	<b-col xl="6" lg="6" md="6">
                		<h4><strong>Pet Detail</strong></h4>
                		<p><strong>Name:</strong> {{detail.pet.name}} ({{detail.pet.category.name}})</p>
                		<p><strong>Breed:</strong> {{detail.pet.breed.name}}</p>
                		<p><strong>DOB:</strong> {{detail.pet.dob}}</p>
                		<p><strong>Coat Level:</strong> {{detail.pet.coat.name}}</p>
                		<p><strong>Agressive Level:</strong> {{detail.pet.aggresive_level.name}}</p>
                	</b-col>
                </b-row>
                <hr/>
                <h4><strong>Edit Service Detail</strong></h4><br/>
                <b-form>
                    <div class="service_tab_main">
                        <b-form-group label="Select Service" v-slot="{ ariaDescribedby }">
                            <b-form-radio-group
                                id="bath-radios-btn"
                                v-model="bath"
                                :aria-describedby="ariaDescribedby"
                                button-variant="outline-primary"
                                size="lg"
                                :options="service_array"
                                value-field="id"
                                text-field="name"
                                name="radio-btn-outline"
                                buttons
                                @change="vehicle_select"
                            >
                            </b-form-radio-group>
                        </b-form-group>

                        <b-form-group label="Additional Service" v-slot="{ ariaDescribedby }">
                            <b-form-checkbox-group
                                id="checkbox-group-2"
                                v-model="additional_service"
                                :aria-describedby="ariaDescribedby"
                                :options="additional_service_array"
                                value-field="id"
                                text-field="name"
                                name="flavour-2"
                                button-variant="outline-primary"
                                buttons
                                @change="vehicle_select"
                            >
                            </b-form-checkbox-group>
                        </b-form-group>
                        <b-form-group label="Select Vehicle" v-slot="{ ariaDescribedby }">
                            <b-form-radio-group
                                id="vehicle-radios-btn"
                                v-model="vehicle"
                                :aria-describedby="ariaDescribedby"
                                button-variant="outline-primary"
                                size="lg"
                                name="radio-btn-outline"
                                :options="service_mode"
                                value-field="id"
                                text-field="name"
                                buttons
                                @change="vehicle_select"
                            >
                            </b-form-radio-group>
                        </b-form-group>
                        <div v-if="total_cost" class="total_cost">Total Cost: {{total_cost}}</div>
                        <div class="datepiker-block">
                            <label class="label_datepicker" for="example-datepicker">Choose Date & Time</label>
                            <b-form-datepicker id="example-datepicker" :min="min" v-model="date" class="mb-2"></b-form-datepicker>
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
                    </div>
                    <b-button type="submit" @click.prevent="update_client" variant="primary">Submit</b-button>
                </b-form>
            </div>
        </div>
    </b-container>
</template>
<script>
    import client from "../../api/clients.js";
    import servicemode from "../../api/servicemode.js";
    import service from "../../api/service.js";
    import timeslot from "../../api/timeslot.js";
    import servicecost from "../../api/servicecost.js";
    import appointment from "../../api/appointment.js";
    export default {
        props: {
            appointmentid: {
              type: String,
              required: true
            },
          },
        mounted() {
            this.getclient();
            this.getappointment();
            this.getservicemode();
            this.getservice();
            this.gettimeslot();
        },
        data() {
            return {
                additional_service_id:[],
                detail:null,
                isHidden: false,
                ariaDescribedby: "",
                time: "",
                max: new Date(),
                min: new Date(),
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
                appoint_service:null,
            };
        },
        computed: {
        filteredOptions() {
            return [
                {
                    data: this.suggestions.filter((option) => {
                        // console.log(option.user.phone);
                        // suggestion.item.user.name;
                        if(option.user.name.toLowerCase().indexOf(this.query.toLowerCase()) > -1 || option.user.phone.toLowerCase().indexOf(this.query.toLowerCase()) > -1)
                        {
                            
                            return option.user.name+'-'+option.user.phone;
                        }
                        
                    }),
                },
            ];
        },
        },
        methods: {
            getappointment(address) {
      
              appointment.getAppointment(this.appointmentid)
                .then(( response ) => {
                  this.detail=response.data.data;
                  var ite = this.detail.items;
                  for (var i = 0; i < ite.length; i++) {
                    
                      if(ite[i].service.type == 1)
                      {
                        this.bath=ite[i].service.id;
                        this.appoint_service = ite[i].id;
                        
                      }
                      if(ite[i].service.type == 2)
                      {
                        this.additional_service.push(ite[i].service.id);
                        this.additional_service_id.push(ite[i].id);
                      }
                  }
                  this.vehicle=this.detail.vehicle_id;
                  this.date=this.detail.date_simple;
                  this.time=this.detail.time.id;
                  this.total_cost=this.detail.total_cost;
                  // this.countrys = response.data.data;
                })
                .catch(response => {
                    this.successful = false;
                    alert('something went wrong');
                })
            },
            vehicle_select() {
                var aa = "";
                if (!this.bath) {
                    aa += "Add service,";
                }
                if (!this.vehicle) {
                    aa += "Add vehicle,";
                }
                // console.log(aa);
                if (aa != "") {
                    return false;
                }
                let formData = new FormData();
                formData.append("service_id", this.bath);
                formData.append("additional_service", this.additional_service);
                formData.append("vehicle", this.vehicle);
                servicecost
                    .getCost(formData)
                    .then((response) => {
                        this.total_cost = response.data.data;
                    })
                    .catch((error) => {
                        console.log(error);
                        if (error.response.status == 422) {
                            this.errors_create = error.response.data.errors;
                        }
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
            update_client() {
                // this.$emit('onComplete');
                this.create_error = "";
                
                if (!this.bath) {
                    this.create_error += "Select Service,";
                }
                if (!this.vehicle) {
                    this.create_error += "Select Vehicle,";
                }
                if (!this.date) {
                    this.create_error += "Select Date,";
                }
                if (!this.time) {
                    this.create_error += "Select Time,";
                }

                if (this.create_error != "") {
                    return false;
                }
                // let loader = this.$loading.show({
                //   "is-full-page": true,
                // });
                let formData = new FormData();
                formData.append('_method', 'PUT');
                
                formData.append("service_id", this.bath);
                formData.append("additional_service", this.additional_service);
                formData.append("vehicle", this.vehicle);
                formData.append("date", this.date);
                formData.append("time", this.time);
                formData.append("total_cost", this.total_cost);
                formData.append("appoint_service", this.appoint_service);
                appointment
                    .editAppointment(this.appointmentid,formData)
                    .then(({ data }) => {
                        if (data.error == false) {
                            this.successful = true;
                            // this.$router.push({ name: "appointments" });
                            // this.query = "";
                            // this.selected = "";
                            // this.pet_select = "";
                            // this.bath = "";
                            // this.error = true;
                            //this.items.push(data.data);
                            // this.cost='';
                            // this.service_id='';
                            // this.slots='';
                            // this.service_mode='';
                            // this.getservicecost();
                            // this.show= false;
                            // loader.hide();
                        }
                    })
                    .catch((error) => {
                        console.log(error);
                        if (error.response.status == 422) {
                            this.errors_create = error.response.data.errors;
                        }
                        this.successful = false;
                        this.error = true;
                        // loader.hide();
                    });
            },
            getImgpet(pet) {
                var images = "/public/uploads/clients/pet/" + pet;
                return images;
            },
            getservice() {
                service
                    .getServices()
                    .then((response) => {
                        // this.service_array=response.data.data;
                        var dat = response.data.data;
                        for (var i = 0; i < dat.length; i++) {
                            if (dat[i].type == 1) {
                                this.service_array.push(dat[i]);
                            }
                            if (dat[i].type == 2) {
                                this.additional_service_array.push(dat[i]);
                            }
                        }
                    })
                    .catch((error) => {
                        console.log(error);
                        if (error.response.status == 422) {
                            this.errors_create = error.response.data.errors;
                        }
                    });
            },
            async getclient(address) {
                client
                    .getClients()
                    .then((response) => {
                        this.suggestions = response.data.data;
                        // this.countrys = response.data.data;
                    })
                    .catch((response) => {
                        this.successful = false;
                        alert("something went wrong");
                    });
            },
            getservicemode() {
                servicemode
                    .getServiceModes()
                    .then((response) => {
                        this.service_mode = response.data.data;
                    })
                    .catch((error) => {
                        console.log(error);
                        if (error.response.status == 422) {
                            this.errors_create = error.response.data.errors;
                        }
                    });
            }
        },
    };
</script>
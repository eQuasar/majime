<template>
    <b-container fluid>
        <div class="header_title">
            <div class="header_inner">
                <h3><strong>Add Appointment</strong></h3>
                <br />
            </div>
        </div> 
        <div class="content_bar card">
            <div class="card-body">
                <b-alert show variant="danger" v-if="create_error">{{create_error}}</b-alert>
                <div :class="['form-group m-1 p-3', (successful ? 'alert-success' : '')]" v-show="successful">
                    <span v-if="successful" class="label label-sucess">Published!</span>
                </div>
                <div class="card main_booking">
                    <div class="card-header">
                        <ul class="nav nav-pills nav-wizard nav-fill">
                            <!-- @click="selectTab(index)"  -->
                            <li class="nav-item" :class="tab.isActive ? 'active' : 'in-active'" v-for="(tab, index) in tabs" v-bind:key="`tab-${index}`">
                                <a class="nav-link" href="#">
                                    <span class="tabStatus">{{index+1}} </span>
                                    <span class="tabLabel">{{tab.title}}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="col-lg-12 mx-auto">
                            <form>
                                <tab-content title="Find User & Choose Pet" :selected="true">
                                    <div class="add_client_link" v-if="!this.$route.query.clientid">
                                        <span v-if="$route.name == 'serviceaddappointment'">
                                            <router-link :to="{ name: 'servicecreateclient' }" class="btn btn-primary">Add Client</router-link>
                                        </span>
                                        <span v-if="$route.name == 'addappointment'">
                                            <router-link :to="{ name: 'createclient' }">Add Client</b-icon></router-link>
                                        </span>
                                    </div>
                                    <div v-if="this.$route.query.clientid">
                                        <div v-if="isHidden" id="client_info_div">
                                            <div class="edit_client_link">
                                                <span v-if="$route.name == 'serviceaddappointment'">
                                                    <router-link :to="{ name: 'serviceeditclient', params: { clientid: (this.clientid).toString() } }"><b-icon icon="pencil-fill" aria-hidden="true"></b-icon></router-link>
                                                </span>
                                                <span v-if="$route.name == 'addappointment'">
                                                    <router-link :to="{ name: 'editclient', params: { clientid: (this.clientid).toString() } }"><b-icon icon="pencil-fill" aria-hidden="true"></b-icon></router-link>
                                                </span>
                                            </div>
                                            <p><strong>Name:</strong> {{user_name}}</p>
                                            <p><strong>Phone:</strong> {{mobile_phone}}</p>
                                            <p><strong>Email:</strong> {{email_address}}</p>
                                            <p><strong>DOB:</strong> {{dob_main}}</p>
                                            <p v-if="address_main"><strong>Address:</strong> {{address_main}}</p>
                                            <p v-if="alternate_address_main"><strong>Alternate Address:</strong> {{alternate_address_main}}</p>
                                            <p v-if="alternate_phone_main"><strong>Alternate Phone Number:</strong> {{alternate_phone_main}}</p>
                                        </div>
                                    </div>
                                    <div class="autosuggest-container" style="position: relative;clear: both;" v-else>
                                        <vue-autosuggest
                                            v-model="query"
                                            :suggestions="filteredOptions"
                                            @focus="focusMe"
                                            @click="clickHandler"
                                            @input="onInputChange"
                                            @selected="onSelected"
                                            :get-suggestion-value="getSuggestionValue"
                                            :input-props="{id:'autosuggest__input', placeholder:'Search by Client Name, Phone Number...'}"
                                        >
                                            <div slot-scope="{suggestion}" style="display: flex; align-items: center;">
                                                <div style="{ display: 'flex', color: 'navyblue'}">{{suggestion.item.user.phone}} - {{suggestion.item.user.name}}</div>
                                            </div>
                                        </vue-autosuggest>
                                        <div class="text-center" v-if="seen" style="position: absolute;right: 0;top: -40px;right: 10px;">
                                          <b-spinner label="Spinning"></b-spinner>
                                        </div>
                                        <div v-if="isHidden" id="client_info_div">
                                            <div class="edit_client_link">
                                                <span v-if="$route.name == 'serviceaddappointment'">
                                                    <router-link :to="{ name: 'serviceeditclient', params: { clientid: (selected.id).toString() } }"><b-icon icon="pencil-fill" aria-hidden="true"></b-icon></router-link>
                                                </span>
                                                <span v-if="$route.name == 'addappointment'">
                                                    <router-link :to="{ name: 'editclient', params: { clientid: (selected.id).toString() } }"><b-icon icon="pencil-fill" aria-hidden="true"></b-icon></router-link>
                                                </span>
                                            </div>
                                            <p><strong>Name:</strong> {{selected.user.name}}</p>
                                            <p><strong>Phone:</strong> {{selected.user.phone}}</p>
                                            <p><strong>Email:</strong> {{selected.user.email}}</p>
                                            <p><strong>DOB:</strong> {{selected.dob}}</p>
                                            <p v-if="selected.address"><strong>Address:</strong> {{selected.address}} {{selected.state.name}},{{selected.city.name}},{{selected.pincode}}</p>
                                            <p v-if="selected.alternate_address"><strong>Alternate Address:</strong> {{selected.alternate_address}}</p>
                                            <p v-if="selected.alternate_phone"><strong>Alternate Phone Number:</strong> {{selected.alternate_phone}}</p>
                                        </div>
                                    </div>
                                    <div class="choose_pet" v-show="isHidden">
                                        <b-form-group label="Select Pet" v-slot="{ ariaDescribedby }">
                                            <b-form-checkbox-group v-model="pet_select" id="pet-radios-btn" :aria-describedby="ariaDescribedby" button-variant="outline-primary" size="lg" name="checkbox-group-btn-outline" buttons>
                                                <div class="choose-pet-radios-btn myedit_pet" v-for="(pet, index) in pets" v-bind:key="index">
                                                    <b-icon @click="show_data(index)" icon="pencil-fill" aria-hidden="true" style="right:5px; cursor:pointer;"></b-icon>
                                                    <b-form-checkbox :value="pet.id">
                                                        <div class="row">
                                                            <div class="col-md-7 pet_info_main">
                                                                <div class="row">
                                                                    <div class="col-md-3 col-lg-3 col-xl-3" style="padding-right: 0px;">
                                                                        <img :src="getImgpet(pet.image)" v-if="pet.image" />
                                                                        <img src="/public/uploads/clients/pet/dummy.jpg" v-else>
                                                                    </div>
                                                                    <div class="col-md-9 col-lg-9 col-xl-9">
                                                                        <h4>{{pet.name}}</h4>
                                                                        <p>{{pet.breed.name}}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <!--<p><strong>Last service - 10 Nov. 2021</strong></p>
                                                                <p>Regular Bath, Hair Cut</p>-->
                                                            </div>
                                                        </div>
                                                    </b-form-checkbox>
                                                </div>
                                            </b-form-checkbox-group>
                                        </b-form-group>
                                        <b-link @click="addpetinfo($route.query.user_id)" class="btn btn-primary 22" v-if="this.$route.query.clientid">Add Pet</b-link>
                                        <b-link @click="addpetinfo(selected.user.id)" class="btn btn-primary 11" v-else>Add Pet</b-link>
                                    </div>
                                </tab-content>
                                <tab-content title="Choose Service">
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
                                        <div class="hide"><div v-if="total_cost" class="total_cost">Total Cost: {{total_cost}}</div></div>
                                        <div class="datepiker-block">
                                            <label class="label_datepicker" for="example-datepicker">Choose Date & Time</label>
                                            <b-form-datepicker id="example-datepicker" :min="min" v-model="date" @context="gettimeslot()" class="mb-2"></b-form-datepicker>
                                        </div>
                                    </div>
                                    <div id="form_info">
                                        <div class="mychoose_pet" v-for="(mypet, index) in mypets" v-bind:key="index">
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <div class="row">
                                                            <div class="col-md-12 pet_info_main">
                                                                <div class="row">
                                                                    <div class="col-md-3 col-lg-3 col-xl-3" style="padding-right: 0px;">
                                                                        <img :src="getImgpet(mypet.image)" v-if="mypet.image" />
                                                                        <img src="/public/uploads/clients/pet/dummy.jpg" v-else>
                                                                    </div>
                                                                    <div class="col-md-9 col-lg-9 col-xl-9">
                                                                        <h4>{{mypet.name}}</h4>
                                                                        <p>{{mypet.breed.name}}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <b-form-group label="" v-slot="{ ariaDescribedby }">
                                                            <b-form-checkbox-group
                                                                v-model="myserv[index]"
                                                                :aria-describedby="ariaDescribedby"
                                                                button-variant="outline-primary"
                                                                name="radio-btn-outline"
                                                                buttons
                                                            >
                                                                <div class="choose-pet-radios-btn" v-for="(myservice, index2) in myservices" v-bind:key="index2">
                                                                    <b-form-checkbox :value="myservice.id">
                                                                        <b-row>
                                                                            <b-col xl="12" lg="12" md="12" sm="12">
                                                                                <h4>{{myservice.name}}</h4>
                                                                            </b-col>
                                                                        </b-row> 
                                                                    </b-form-checkbox>
                                                                </div>
                                                            </b-form-checkbox-group>
                                                        </b-form-group>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <b-form-select v-model="time[index]" :options="time_slots"
                                                            value-field="id"
                                                            text-field="time">
                                                            <template #first>
                                                                <b-form-select-option :value="null" disabled>-- Choose Time --</b-form-select-option>
                                                              </template>
                                                        </b-form-select>
                                                        <b-button type="submit" @click.prevent="myDataTab(mypet.id, index)" variant="primary">Submit </b-button>
                                                    </div>
                                                </div>
                                        </div>
                                    </div>
                                </tab-content>
                            </form>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <div class="btn-group" role="group">
                            <button @click="previousTab" :disabled="currentTab === 0" class="btn btn-warning">Previous</button>
                            <button @click="nextTab" v-if="currentTab < totalTabs - 2" class="btn btn-primary">Next</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <b-modal id="modal-1" title="Add Pet" hide-footer @hidden="clearpetData" size="lg">
            <b-form>
                <b-alert show variant="danger" v-if='create_error'>{{create_error}}</b-alert>
                <div :class="['form-group m-1 p-3', (successful ? 'alert-success' : '')]" v-show="successful">
                  <span v-if="successful" class="label label-sucess">Published!</span>
                </div>
                <b-form-input v-model="user_id" id="user_id_val" style="display:none"></b-form-input>
                <b-overlay :show="show" rounded="sm">
                <b-row>
                  <b-col xl="3" lg="3" md="3" class="pet_image_bar">
                      <div class="custom-file mb-3">
                        <input type="file" ref="image" @change="previewImage" name="image" class="custom-file-input" id="image" accept=".jpg,.jpeg,.png">
                        <label class="custom-file-label" >Choose file...</label>
                      </div>
                      <img v-if='image_file' :src="image_file" style='width:50%;'>
                      <a v-if='image_file' class="preview" @click="deleteImage" data-method="delete" data-confirm="Are you sure?">
                          <i class="fa fa-times" style='cursor: pointer; position: absolute; margin-right: 0px;margin-left: 10px;font-size: 15px;'></i>
                      </a>
                  </b-col>
                  <b-col xl="9" lg="9" md="9">
                    <b-row>
                        <b-col xl="12" lg="12" md="12">
                            <b-form-group
                                id="input-group-name"
                                label="Pet Name"
                                label-for="input-name"
                                >
                                <b-form-input
                                  id="input-name"
                                  v-model="pet_name"
                                  type="text"
                                  required
                                  placeholder="Enter Pet Name"
                                ></b-form-input>
                            </b-form-group>
                        </b-col>
                    </b-row>
                    <b-row>
                        <b-col xl="6" lg="6" md="6">
                          <label>Pet Category</label>
                          <b-form-select v-model="pet_cat" class="mb-4" :options="pet_cat_array" value-field="id" text-field="name" @change="cat_change">
                            <template v-slot:first>
                              <b-form-select-option :value="0" disabled>-- Select Category --</b-form-select-option>
                              
                            </template>
                          </b-form-select>
                        </b-col>
                        <b-col xl="6" lg="6" md="6">
                            <label>Pet Breed</label>
                            <b-form-select v-model="pet_breed" class="mb-4" :options="pet_breed_array_filter" value-field="id" text-field="name">
                             <template v-slot:first>
                              <b-form-select-option :value="0" disabled>-- Select Breed --</b-form-select-option>
                            </template>
                            </b-form-select>
                        </b-col>
                    </b-row>
                    <b-row style="display:none">
                        <b-col xl="6" lg="6" md="6">
                            <label>Coat Level</label>
                            <b-form-select v-model="coat_level" class="mb-4" :options="coat_options" value-field="id" text-field="name">
                             <template v-slot:first>
                              <b-form-select-option :value="0" disabled>-- Select Coat Level --</b-form-select-option>
                            </template>
                            </b-form-select>
                        </b-col>
                        <b-col xl="6" lg="6" md="6">
                            <label>Aggression</label>
                            <b-form-select v-model="aggressive" class="mb-4" :options="aggresion_options" value-field="id" text-field="name">
                              <template v-slot:first>
                              <b-form-select-option :value="0" disabled>-- Select Aggresive Level --</b-form-select-option>
                            </template>
                             
                            </b-form-select>
                        </b-col>
                    </b-row>
                    <b-row>
                        <b-col xl="12" lg="12" md="12">
                        <b-form-group
                            id="input-group-dob"
                            label="Date of Birth"
                            label-for="input-dob"
                            >
                            <div class="datepiker-block">
                            <b-form-datepicker id="pet_dob" v-model="pet_dob" class="mb-2" :date-format-options="{ year: 'numeric', month: '2-digit', day: '2-digit' }" :max="max" locale="en-IN"></b-form-datepicker>
                            </div>
                        </b-form-group> 
                        </b-col>
                    </b-row>
                  </b-col>
                </b-row>
                </b-overlay>
                <b-button type="submit" @click.prevent="create_pet" variant="primary">Submit </b-button>
            </b-form>
        </b-modal>
    </b-container>
</template>
<script>
    import { VueAutosuggest } from "vue-autosuggest";
    import client from "../../api/clients.js";
    import servicemode from "../../api/servicemode.js";
    import service from "../../api/service.js";
    import timeslot from "../../api/timeslot.js";
    import servicecost from "../../api/servicecost.js";
    import appointment from "../../api/appointment.js";
import pet from '../../api/pets.js';
import breed from '../../api/breed.js';
import petcategory from '../../api/petcategory.js';
import petcoat from '../../api/petcoat.js';
import petaggresive from '../../api/petaggresive.js';
    export default {
        components: {
            VueAutosuggest,
        },
        mounted() {
            this.tabs = this.$children;
            this.totalTabs = this.tabs.length;
            this.currentTab = this.tabs.findIndex((tab) => tab.isActive === true);
            if (this.currentTab === -1 && this.totalTabs > 0) {
                //Select first tab if none is marked selected
                this.tabs[0].isActive = true;
                this.currentTab = 0;
            }
            var suggestions = JSON.parse(localStorage.getItem("suggestions"));
            if(suggestions != null){
               this.suggestions = suggestions;
                if (suggestions.length === 0) {
                    this.getClient();
                }
            }else{
                this.getClient();
            }
            //console.log(suggestions);
            this.getservicemode();
            this.getservice();
            // this.gettimeslot();
            this.getpetcoat();
            this.getpetaggresion(); 
            // this.getpets(); 
            this.getbreed();
            this.getpetcategory();
            if(this.$route.query.clientid){
                //alert("sss");
                this.clientid = this.$route.query.clientid;
                this.user_id = this.$route.query.user_id;
                this.getclientpet();
                this.getclientmain();
                this.isHidden = true;
            }
        },
        data() {
            return {
                myserv:[],
                user_name:"",
                mobile_phone:"",
                email_address:"",
                dob_main:"",
                address_main:"",
                alternate_address_main:"",
                alternate_phone_main:"",
                clientid:"",
                seen: false,
                current_index:0,
                user_id:0,
                max: new Date(),
                min: new Date(),
                //min: new Date(new Date().getTime() + 24 * 60 * 60 * 1000),
                show: false,
                image:'',
                image_file:null,
                aggressive:2,
                coat_level:2,
                pet_name:'',
                pet_breed:0,
                pet_breed_array:[],
                pet_dob: '',
                pet_cat:0,
                pet_cat_array:[],
                pet_breed_array_filter:[],
                coat_options:[],
                aggresion_options:[],
                seen: false,
                isHidden: false,
                tabs: [],
                currentTab: 0,
                totalTabs: 0,
                ariaDescribedby: "",
                time: [],
                pet_select: [],
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
                edit_pet_id:"",
                successful: false,
                particularclient:[],
                pet_select_info:[],
                main_service_info:[],
                additional_service_info:[],
                mypets:[],
                myservices:[],
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
        getclientmain(address) {
          client.getClient(this.clientid)
          .then(( response ) => {
            if(response.data.data)
            {
              this.user_id = response.data.data.user.id;
              this.user_name = response.data.data.user.name;
              this.mobile_phone = response.data.data.user.phone;
              this.email_address = response.data.data.user.email;
              this.dob_main = response.data.data.dob;
              this.address_main = response.data.data.address;
              this.alternate_address_main = response.data.data.alternate_address;
              this.alternate_phone_main = response.data.data.alternate_phone;
              
            // }
            }
          })
          .catch(error => {
              console.log(error);
              
          });
      },
      getclientpet(address) {
        let formData = new FormData();
        if(this.$route.query.clientid){
            formData.append("client_id", this.$route.query.clientid);
        }else{
            formData.append("client_id", this.client_id);
        }
        pet.clientPet(formData)
          .then(( response ) => {
            this.pets=response.data.data;
            // this.countrys = response.data.data;
          })
          .catch(response => {
              this.successful = false;
              alert('something went wrong');
          })
      },
      getpetappointment(petid){
        let formData = new FormData();
        formData.append("pet_id", this.petid);
      },
    show_data(index)
      {
        // alert(index);
        // console.log(this.pets[index]);
        this.pet_name = this.pets[index].name;
        this.pet_cat = this.pets[index].pet_cat_id;
        this.cat_change();
        this.pet_breed = this.pets[index].breed_id;
        this.pet_dob = this.pets[index].dob;
        //this.coat_level = this.pets[index].coat_level;
        //this.aggressive = this.pets[index].aggresive;
        this.coat_level = 2;
        this.aggressive = 2;
        this.edit_pet_id = this.pets[index].id;
        this.user_id = this.$route.query.user_id;
                this.$bvModal.show("modal-1");
      },
    deleteImage()
    {
      this.image = null;
      this.image_file = null;
    },
      clearpetData()
      {
        this.current_index = null;
      },
              getpetaggresion()
              {
                
                petaggresive.getPetAggresives()
                .then(( response ) => {
                    this.aggresion_options=response.data.data;
                })
                .catch(error => {
                  console.log(error);
                  if (error.response.status == 422){
                   this.errors_create = error.response.data.errors;
                  } 
                })
              },
              getpetcoat()
              {
                
                petcoat.getPetCoats()
                .then(( response ) => {
                    this.coat_options=response.data.data;
                })
                .catch(error => {
                  console.log(error);
                  if (error.response.status == 422){
                   this.errors_create = error.response.data.errors;
                  } 
                })
              },
              cat_change()
              {
                this.pet_breed_array_filter=[];
                for (var i = 0; i < (this.pet_breed_array).length; i++) {
                  if(this.pet_cat == this.pet_breed_array[i].pet_cat_id)
                  {
                    this.pet_breed_array_filter.push(this.pet_breed_array[i]);
                  }
                }
                console.log(this.pet_breed_array_filter);
              },
              getpetcategory()
              {
                
                petcategory.getPetCategories()
                .then(( response ) => {
                    this.pet_cat_array=response.data.data;
                })
                .catch(error => {
                  console.log(error);
                  if (error.response.status == 422){
                   this.errors_create = error.response.data.errors;
                  } 
                })
              },
      getbreed()
      {
        
        breed.getBreeds()
        .then(( response ) => {
            this.pet_breed_array=response.data.data;
        })
        .catch(error => {
          console.log(error);
          if (error.response.status == 422){
           this.errors_create = error.response.data.errors;
          } 
        })
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
    /*getpets()
     {
       let formData = new FormData();
       formData.append('type', 'get_pet');
       formData.append('client_id', this.clientid);
       pet.addPet(formData)
         .then(( response ) => {
              
             this.items=response.data.data;
           console.log(this.items);
                
         })
         .catch(error => {
           console.log(error);
           if (error.response.status == 422){
            this.errors_create = error.response.data.errors;
           } 
            this.successful = false;
            this.error = true;
         })
     },*/
    create_pet() 
    {
      this.create_error='';
      if(!this.pet_name)
      {
        this.create_error+='Add Name,';
      }
      if(!this.pet_breed)
      {
        this.create_error+='Add Breed,';
      }
      if(this.coat_level == 0)
      {
        this.create_error+='Add Coat Level,';
      }
      if(this.pet_cat == 0)
      {
        this.create_error+='Add Category,';
      }
      if(!this.pet_dob)
      {
        this.create_error+='Add date of birth,';
      }
      /*if(!this.$refs.image.files[0])
      {
        this.create_error+='Add Image,';
      }*/
      if(this.aggressive == 0)
      {
        this.create_error+='Add Aggression,';
      }
      
      if(this.create_error != '')
      {
        // this.create_item_error+= ' Required';
        return false;
      }
      // let loader = this.$loading.show({
      //   "is-full-page": true,
      // });
      let formData = new FormData();
      formData.append('name', this.pet_name);
      formData.append('pet_cat_id', this.pet_cat);
      formData.append('pet_breed', this.pet_breed);
      formData.append('coat_level', this.coat_level);
      formData.append('aggressive', this.aggressive);
      formData.append('dob', this.pet_dob);
      if(this.$route.query.clientid){
        formData.append('client_id', this.$route.query.clientid);
      }else{
        formData.append('client_id', this.selected.id);
      }
      formData.append("image", this.$refs.image.files[0]);

      //alert("wwww"+this.edit_pet_id);

      if(this.edit_pet_id)
      {
        this.show= true;
        formData.append('_method', 'PUT');
        pet.editPet(this.edit_pet_id,formData)
        .then(({ data }) => {
          this.show= true;
            this.successful = true;
            this.error = true;
            this.pet_name='';
            this.pet_breed='';
            this.coat_level=2;
            this.aggressive=2;
            this.pet_dob='';
            this.deleteImage();
            this.create_error='';
            this.edit_pet_id='';
            //this.getclientpet();
            //this.pets.push(response.data.data);
            if(this.$route.query.clientid){
                this.client_id = this.$route.query.clientid;
            }else{
                this.client_id = this.selected.id;
            }
            this.getclientpet();
            this.isHidden = true;
            this.$bvModal.hide("modal-1");
            this.show= false;
            this.successful = false;
            this.error = false;
        })
        .catch(error => {

    this.show= false;
          console.log(error);
          if (error.response.status == 422){
           this.errors_create = error.response.data.errors;
          } 
           this.successful = false;
           this.error = true;
            // loader.hide();
        })
      }else{
      
        pet.addPet(formData)
        .then((response) => {
          // if(response.data.error == false)
          // {

            this.show= true;
            this.successful = true;
            this.error = true;
            this.pet_name='';
            this.pet_breed='';
            this.coat_level=2;
            this.aggressive=2;
            this.pet_dob='';
            this.deleteImage();
            this.pets.push(response.data.data);
            this.$bvModal.hide("modal-1");
            this.show= false;
            this.successful = false;
            this.error = false;
        })
        .catch(error => {

    this.show= false;
          console.log(error);
          if (error.response.status == 422){
           this.errors_create = error.response.data.errors;
          } 
           this.successful = false;
           this.error = true;
            // loader.hide();
        })
      }
    },
    getImgpet(pet) {
        if(pet != ""){
          var images = '/public/uploads/clients/pet/'+pet;
        }else{
          var images = '/public/uploads/clients/pet/dummy.jpg';
        }
        return images;
    },
            addpetinfo(id){
                //alert(id);
                this.$bvModal.show("modal-1");
                this.edit_pet_id = "";
                this.pet_name = "";
                this.pet_cat = "";
                this.pet_breed = "";
                this.pet_dob = "";
                this.coat_level = 2;
                this.aggressive = 2;
                this.user_id = id;
            },
            vehicle_select() {
                var aa = "";
                if (!this.bath) {
                    aa += "Add service,";
                }
                if (!this.vehicle) {
                    aa += "Add vehicle,";
                }
                console.log(aa);
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
                localStorage.setItem("pet_select_info", JSON.stringify(this.pet_select));
                localStorage.setItem("main_service_info", JSON.stringify(this.bath));
                localStorage.setItem("additional_service_info", JSON.stringify(this.additional_service));

                var pet_select_info = JSON.parse(localStorage.getItem("pet_select_info"));
                this.pet_select_info = pet_select_info;
                var main_service_info = JSON.parse(localStorage.getItem("main_service_info"));
                this.main_service_info = main_service_info;
                var additional_service_info = JSON.parse(localStorage.getItem("additional_service_info"));
                this.additional_service_info = additional_service_info;

                console.log(pet_select_info);
                console.log(main_service_info);
                console.log(additional_service_info);

                this.getmypets();
                this.getmyservices();


                if(this.date){
                    let formData = new FormData();
                    formData.append("type", "get_active");
                    formData.append("date", this.date);
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
                    }    
            },
            previousTab() {
                this._switchTab(this.currentTab - 1);
            },

            nextTab() {
                if(!this.$route.query.clientid){
                    if (this.query == "") {
                        return;
                    }
                }
                if (!this.pet_select) {
                    return;
                }

                this._switchTab(this.currentTab + 1);
            },

            /*selectTab(index){
            if(index < this.currentTab){
              this._switchTab(index);
            }

            this._switchTab(index);
            
        },*/

                 getmypets()
                 {
                   let formData = new FormData();
                   formData.append('type', 'get_pet');
                   formData.append('pet_ids', this.pet_select_info);
                   pet.getMyPets(formData)
                     .then(( response ) => {
                          
                         this.mypets=response.data.data;
                       console.log(this.mypets);
                            
                     })
                     .catch(error => {
                       console.log(error);
                       if (error.response.status == 422){
                        this.errors_create = error.response.data.errors;
                       } 
                        this.successful = false;
                        this.error = true;
                     })
                 },

                 getmyservices()
                 {
                   let formData = new FormData();
                   formData.append('type', 'get_services');
                   formData.append('main_service_id', this.main_service_info);
                   formData.append('additional_service_ids', this.additional_service_info);
                   service.getMyServices(formData)
                     .then(( response ) => {
                          
                         this.myservices=response.data.data;
                       console.log(this.myservices);
                            
                     })
                     .catch(error => {
                       console.log(error);
                       if (error.response.status == 422){
                        this.errors_create = error.response.data.errors;
                       } 
                        this.successful = false;
                        this.error = true;
                     })
                 },

            myDataTab(petid, index) {
                // this.$emit('onComplete');
                this.create_error = "";
                /*if(!this.$route.query.clientid){
                    if (!this.query) {
                        this.create_error += "Add client,";
                    }
                    if (!this.selected) {
                        this.create_error += "Add client,";
                    }
                }*/
                if (!this.pet_select) {
                    this.create_error += "Select Pet,";
                }
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

                if(!this.$route.query.clientid){
                    formData.append("mobile", this.query);
                    formData.append("client_id", this.selected.id);
                }else{
                    formData.append("client_id", this.$route.query.clientid);
                }

                formData.append("pet_id", petid);
                formData.append("service_id", this.bath);
                formData.append("myserv", this.myserv[index]);
                formData.append("vehicle", this.vehicle);
                formData.append("date", this.date);
                formData.append("time", this.time[index]);
                formData.append("total_cost", this.total_cost);

                if(this.myserv[index].length > 0){
                appointment
                    .addAppointment(formData)
                    .then(({ data }) => {
                        if (data.error == false) {
                            this.successful = true;
                            //this.$router.push({ name: "appointments" });
                            this.mypets.splice(index, 1);
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
                }
            },

            _switchTab(index) {
                //Disable all tabs
                this.tabs.forEach((tab) => {
                    tab.isActive = false;
                });
                this.currentTab = index;
                this.tabs[index].isActive = true;
            },
            getImgpet(pet) {
                if(pet != ""){
                  var images = '/public/uploads/clients/pet/'+pet;
                }else{
                  var images = '/public/uploads/clients/pet/dummy.jpg';
                }
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
                                var str='';
                                if(dat[i].cost.length > 0)
                                {
                                    
                                    for (var k = 0; k < dat[i].cost.length; k++) {
                                        if(dat[i].cost[k].service_mode == 1)
                                        {
                                            str+='Bike '+dat[i].cost[k].cost;
                                        }
                                        if(dat[i].cost[k].service_mode == 2)
                                        {
                                            str+='  Van '+dat[i].cost[k].cost;
                                        }
                                        
                                    }
                                }

                                this.service_array.push({'id':dat[i].id,'name':dat[i].name+' ('+str+')'});  
                                // console.log(this.service_array);
                            }
                            if (dat[i].type == 2) {
                                var str1='';
                                if(dat[i].cost.length > 0)
                                {
                                    
                                    for (var k = 0; k < dat[i].cost.length; k++) {
                                        if(dat[i].cost[k].service_mode == 1)
                                        {
                                            str1+='Bike '+dat[i].cost[k].cost;
                                        }
                                        if(dat[i].cost[k].service_mode == 2)
                                        {
                                            str1+='  Van '+dat[i].cost[k].cost;
                                        }
                                        
                                    }
                                }
                                this.additional_service_array.push({'id':dat[i].id,'name':dat[i].name+' ('+str1+')'});
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
            async getClient(address) {
                this.seen = true;
                client
                    .getClients()
                    .then((response) => {
                        this.suggestions = response.data.data;
                        this.seen = false;
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
            },
            clickHandler(item) {
                // event fired when clicking on the input
                this.isHidden = false;
            },
            create() {},
            onSelected(item) {
                this.query = item.item.user.phone;
                this.selected = item.item;
                this.pets = item.item.pets;
                // console.log("ssss");
                this.isHidden = true;
            },
            onInputChange(text) {
                // event fired when the input changes
                this.isHidden = false;
            },
            /**
             * This is what the <input/> value is set to when you are selecting a suggestion.
             */
            getSuggestionValue(suggestion) {
                // console.log(suggestion.item.user.phone);
                return suggestion.item.user.phone +' - '+ suggestion.item.user.name;
            },
            focusMe(e) {
                console.log(e) // FocusEvent
            },
        },
    };
</script>
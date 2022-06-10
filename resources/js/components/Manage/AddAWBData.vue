<template>
  <b-container fluid> 
   
      <div class="card-body">
        <h3><strong>Add AWB Data</strong></h3>
        <br/>
        <b-form>
            <b-alert show variant="danger" v-if='create_error'>{{create_error}}</b-alert>
            <div :class="['form-group m-1 p-3', (successful ? 'alert-success' : '')]" v-show="successful">
              <span v-if="successful" class="label label-sucess">Published!</span>
            </div>
            <b-overlay :show="show" rounded="sm">
              <b-row style="margin-bottom: 10px;">
                <b-col xl="6" lg="6" md="6">
                  <b-form-group
                      id="input-group-city"
                      label="City"
                      label-for="input-city"
                      >
                  <b-form-input id="input-city" :value="this.city" v-model="city" type="text" required placeholder="Enter City"></b-form-input>
                </b-form-group>
                </b-col>
                <b-col xl="6" lg="6" md="6">
                  <b-form-group
                      id="input-group-name"
                      label="Name"
                      label-for="input-name"
                      >
                    <b-form-input id="input-name" :value="this.name" v-model="name" type="text" required placeholder="Enter Name"></b-form-input>
                  </b-form-group>
                </b-col>
              </b-row>
              <b-row style="margin-bottom: 10px;">
                <b-col xl="6" lg="6" md="6">
                 
                  <b-form-group
                      id="input-group-country"
                      label="Country"
                      label-for="input-country"
                      >
                    <b-form-input
                        id="input-country"
                        v-model="country"
                        type="text" :value="this.country"
                        required
                        placeholder="Enter Country"
                      ></b-form-input>
                    </b-form-group>
                  
                </b-col>
                <b-col xl="6" lg="6" md="6">
                  
                  <b-form-group
                      id="input-group-pin"
                      label="Pincode"
                      label-for="input-pin"
                      >
                  <b-form-input
                        id="input-pin"
                        v-model="pin" @keypress="isNumber($event)" 
                        type="text" :value="this.pin"maxlength="6"
                        required
                        placeholder="Enter Pincode"
                      ></b-form-input>
                    </b-form-group>
                 
                </b-col>
              </b-row>
              <b-row style="margin-bottom: 10px;">
                <b-col xl="6" lg="6" md="6">
                  <b-form-group
                      id="input-group-address"
                      label="Address"
                      label-for="input-address"
                      >
                      <b-form-input
                        id="input-add"
                        v-model="add"
                        type="text" :value="this.add"
                        required
                        placeholder="Enter Address"
                      ></b-form-input>
                    </b-form-group>
                 
                </b-col>
                <b-col xl="6" lg="6" md="6">
                 
                  <b-form-group
                      id="input-group-phone"
                      label="Phone"
                      label-for="input-phone"
                      >
                  <b-form-input
                        id="input-phone"
                        v-model="phone" :value="this.phone"
                        type="text" maxlength="10" @keypress="isNumber($event)" 
                        required
                        placeholder="Enter Phone"
                      ></b-form-input>
                 </b-form-group>
                </b-col>
              </b-row>
              <b-row style="margin-bottom: 10px;">
                <b-col xl="12" lg="12" md="12">
                 
                  <b-form-group
                      id="input-group-token"
                      label="Token"
                      label-for="input-token"
                      >
                  <b-form-input
                        id="input-token"
                        v-model="token"
                        type="text" :value="this.token"
                        required
                        placeholder="Enter Token"
                      ></b-form-input>
                  </b-form-group>
                  <b-form-group
                      id="input-group-prefix"
                      label="Order Prefix"
                      label-for="input-order_prefix"
                      >
                  <b-form-input
                        id="input-prefix"
                        v-model="order_prefix"
                        type="text" :value="this.order_prefix"
                        required
                        placeholder="Enter Order Prefix"
                      ></b-form-input>
                  </b-form-group>
                </b-col>
              </b-row>
              <div v-if="this.awb_id != 0">
                <b-form-input
                        id="input-token"
                        v-model="awb_id"
                        type="text" :value="this.awb_id"
                        required
                        style="display: none"
                      ></b-form-input>
                <b-button type="submit" @click.prevent="update" variant="primary">Update </b-button></div>
              <div v-else><b-button type="submit" @click.prevent="create" variant="primary">Submit </b-button></div>
            </b-overlay>
          </b-form>
      </div>
    </div>
  </b-container>
</template>

<script>
  import awb from '../../api/awbdata.js';
  // import apidata from '../../api/json.js';
  // import vendor from '../../api/vendors.js';
  export default {
    props: {
    },
    mounted() {
      this.getAWBLocation();
    },
    data() 
    {
      return {
        show: false,
        order_prefix: "",
        ariaDescribedby: "",
        name: "",
        vid:0,
        city: "",
        pin:"",
        country:"",
        phone:"",
        add:"",
        token:"",
        role_id:1,
        seen: false,
        sortBy: 'date',
        sortDesc: true,
        perPage: 10,
        awb_id:0,
        currentPage: 1,
        pageOptions: [5, 10, 15, 20, 50, 100],
        filter: null,
        filterOn: [],
        fields: [
          {
            key: 'city',
            label: 'City',
            sortable: true
          },
          {
            key: 'name',
            label: 'Name',
            sortable: true
          },
           {
            key: 'country',
            label: 'country',
            sortable: true
          },
          {
            key: 'phone',
            label: 'phone',
            sortable: true
          }, 
          {
            key: 'add',
            label: 'add',
            sortable: true
          },
          {
            key: 'pin',
            label: 'pin',
            sortable: true
          },
          {
            key: 'action',
            label: 'Action',
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
      isNumber: function(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if ((charCode > 31 && (charCode < 48 || charCode > 57)) && charCode !== 46) {
          evt.preventDefault();;
        } else {
          return true;
        }
      },
      create() {
        this.create_error = "";
        if (!this.city) {
          this.create_error += "Add city,";
        }
        if (!this.name) {
          this.create_error += "Add Name,";
        }
        if (!this.pin) {
          this.create_error += "Add Pincode,";
        }
        if (!this.country) {
          this.create_error += "Add Country,";
        }
        if (!this.phone) {
          this.create_error += "Add Phone,";
        }
        if (!this.add) {
          this.create_error += "Add Address,";
        }
        if (!this.token) {
          this.create_error += "Add Token,";
        }
        if (!this.order_prefix) {
          this.create_error += "Order Prefix,";
        }
        if (this.create_error != "") {
          return false;
        }
        this.vid = JSON.parse(localStorage.getItem("ivid"));
        let formData = new FormData();
        formData.append("user_id", this.$userId);
        formData.append("vid", this.vid);
        formData.append("city", this.city);
        formData.append("name", this.name);
        formData.append("pin", this.pin);
        formData.append("country", this.country);
        formData.append("phone", this.phone);
        formData.append("add", this.add);
        formData.append("order_prefix", this.order_prefix);
        formData.append("token", this.token);
        
        awb.addWayData(formData)
          .then((response) => {
            this.successful = true;
            this.error = true;
            this.getAWBLocation();
            //this.items=response.data;
            // this.items.push(response.data);
            // this.city='';
            // this.name='';
            // this.pin='';
            // this.country='';
            // this.phone='';
            // this.add='';
            // this.token='';
          })
          .catch((error) => {
              console.log(error);
              if (error.response.status == 422) {
                  this.errors_create = error.response.data.errors;
              }
          });
      },
      update() {
        this.create_error = "";
        if (!this.city) {
          this.create_error += "Add city,";
        }
        if (!this.name) {
          this.create_error += "Add Name,";
        }
        if (!this.pin) {
          this.create_error += "Add Pincode,";
        }
        if (!this.country) {
          this.create_error += "Add Country,";
        }
        if (!this.phone) {
          this.create_error += "Add Phone,";
        }
        if (!this.add) {
          this.create_error += "Add Address,";
        }
        if (!this.token) {
          this.create_error += "Add Token,";
        }
        if (!this.order_prefix) {
          this.create_error += "Order Prefix,";
        }
        if (this.create_error != "") {
          return false;
        }
        this.vid = JSON.parse(localStorage.getItem("ivid"));
        let formData = new FormData();
        formData.append("user_id", this.$userId);
        formData.append("vid", this.vid);
        formData.append("city", this.city);
        formData.append("name", this.name);
        formData.append("pin", this.pin);
        formData.append("country", this.country);
        formData.append("phone", this.phone);
        formData.append("add", this.add);
        formData.append("token", this.token);
        formData.append("order_prefix", this.order_prefix);
        formData.append("id", this.awb_id);
        
        awb.updateWayData(formData)
          .then((response) => {
            this.successful = true;
            this.error = true;
            this.getAWBLocation();
            //this.items=response.data;
            // this.items.push(response.data);
            // this.city='';
            // this.name='';
            // this.pin='';
            // this.country='';
            // this.phone='';
            // this.add='';
            // this.token='';
          })
          .catch((error) => {
              console.log(error);
              if (error.response.status == 422) {
                  this.errors_create = error.response.data.errors;
              }
          });
      },
      // import_data(id, url) {
      //   // console.log(id);
      //   // console.log(url);
      //   let formData = new FormData();
      //   formData.append("vid", id);
      //   formData.append("url", url);
        
      //   apidata
      //     .getJson(formData)
      //     .then((response) => {
      //         alert("Data imported successfully.");
      //     })
      //     .catch((error) => {
      //         console.log(error);
      //         if (error.response.status == 422) {
      //             this.errors_create = error.response.data.errors;
      //         }
      //     });
      // },
      getAWBLocation() {
        this.vid = JSON.parse(localStorage.getItem("ivid"));
        let formData = new FormData();
        formData.append("user_id", this.$userId);
        formData.append("vid", this.vid);
        awb.getAWBLocation(formData)
        .then(( response ) => {
          if(response.data.msg){
            alert(response.data.msg);
          }else{
            this.vid=response.data[0].vid;
            this.user_id=response.data[0].user_id;
            this.city=response.data[0].city;
            this.name=response.data[0].name;
            this.pin=response.data[0].pin;
            this.country=response.data[0].country;
            this.phone=response.data[0].phone;
            this.add=response.data[0].add;
            this.token=response.data[0].token;
            this.order_prefix=response.data[0].order_prefix;
            this.awb_id=response.data[0].id;
          }
        })
        .catch(response => {
            this.successful = false;
            alert('something went wrong');
        })
      }
    }
  };  
</script>

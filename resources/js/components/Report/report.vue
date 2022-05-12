<template>
  <b-container fluid> 
   
      <div class="card-body">
        <h3><strong>Import Data</strong></h3>
        <br/>
        <b-form>
            <b-alert show variant="danger" v-if='create_error'>{{create_error}}</b-alert>
            <div :class="['form-group m-1 p-3', (successful ? 'alert-success' : '')]" v-show="successful">
              <span v-if="successful" class="label label-sucess">Published!</span>
            </div>
            <b-overlay :show="show" rounded="sm">
              <b-row style="margin-bottom: 10px;">
                <b-col xl="6" lg="6" md="6">
                  
                  <b-form-input id="input-city" v-model="city" type="text" required placeholder="Enter City"></b-form-input>
                                  </b-col>
                <b-col xl="6" lg="6" md="6">
                  <b-form-input id="input-name"  v-model="name" type="text" required placeholder="Enter Name"></b-form-input>
                </b-col>
              </b-row>
              <b-row style="margin-bottom: 10px;">
                <b-col xl="6" lg="6" md="6">
                  
                  <b-form-input
                        id="input-pin"
                        v-model="pin"
                        type="text"
                        required
                        placeholder="Enter Pincode"
                      ></b-form-input>
                 
                </b-col>
              </b-row>
              <b-row style="margin-bottom: 10px;">
                <b-col xl="6" lg="6" md="6">
                 
                  <b-form-input
                        id="input-country"
                        v-model="country"
                        type="text"
                        required
                        placeholder="Enter Country"
                      ></b-form-input>
                  
                </b-col>
                <b-col xl="6" lg="6" md="6">
                 
                  <b-form-input
                        id="input-phone"
                        v-model="phone"
                        type="text"
                        required
                        placeholder="Enter Phone"
                      ></b-form-input>
                 
                </b-col>
              </b-row>
              <b-row style="margin-bottom: 10px;">
                <b-col xl="6" lg="6" md="6">
                
                  <b-form-input
                        id="input-add"
                        v-model="add"
                        type="text"
                        required
                        placeholder="Enter Addresss"
                      ></b-form-input>
                 
                </b-col>
                <b-col xl="6" lg="6" md="6">
                 
                  <b-form-input
                        id="input-token"
                        v-model="token"
                        type="text"
                        required
                        placeholder="Enter Token"
                      ></b-form-input>
                  
                </b-col>
              </b-row>
              <input type="text" v-model="role_id" value="1" style="display:none;"/>
              <b-button type="submit" @click.prevent="create" variant="primary">Submit </b-button>
            </b-overlay>
          </b-form>
      </div>
    </div>
  </b-container>
</template>

<script>
  import report from '../../api/report.js';
  import vendor from '../../api/vendors.js';
  export default {
    props: {
    },
    mounted() {
      this.getLinks();
    },
    data() 
    {
      return {
        show: false,
        ariaDescribedby: "",
        name: "",
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
        if (this.create_error != "") {
          return false;
        }
        let formData = new FormData();
        formData.append("city", this.city);
        formData.append("name", this.name);
        formData.append("pin", this.pin);
        formData.append("country", this.country);
        formData.append("phone", this.phone);
        formData.append("add", this.add);
        formData.append("token", this.token);
        
        report
          .addwaydata(formData)
          .then((response) => {
            this.successful = true;
            this.error = true;
            //this.items=response.data;
            this.items.push(response.data);
            this.city='';
            this.name='';
            this.pin='';
            this.country='';
            this.phone='';
            this.add='';
            this.token='';
          })
          .catch((error) => {
              console.log(error);
              if (error.response.status == 422) {
                  this.errors_create = error.response.data.errors;
              }
          });
      },
      import_data(id, url) {
        console.log(id);
        console.log(url);
        let formData = new FormData();
        formData.append("vid", id);
        formData.append("url", url);
        
        apidata
          .getJson(formData)
          .then((response) => {
              alert("Data imported successfully.");
          })
          .catch((error) => {
              console.log(error);
              if (error.response.status == 422) {
                  this.errors_create = error.response.data.errors;
              }
          });
      },
      getLinks() {
        apidata.getAllLinks()
        .then(( response ) => {
          this.items=response.data;
        })
        .catch(response => {
            this.successful = false;
            alert('something went wrong');
        })
      }
    }
  };  
</script>

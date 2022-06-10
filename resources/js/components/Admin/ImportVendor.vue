<template>
  <b-container fluid> 
    <b-overlay :show="show" rounded="sm">
      <div class="content_bar card">
        <div class="card-body">
          <b-table striped hover responsive :items="items"
          :sort-by.sync="sortBy"
          :sort-desc.sync="sortDesc"
          sort-icon-left :filter-included-fields="filterOn" :filter="filter" :fields="fields" :per-page="perPage" :current-page="currentPage" show-empty>
              <template #empty="scope">
                  <p style="text-align:center;">No record found, choose date filter to found the result.</p>
              </template>
              <template v-slot:cell(action)="row">
                <b-link @click="import_data(row.item.id, row.item.url)"><button class="btn btn-primary" style="float:none;">Import Data</button></b-link>
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
      <div class="card-body">
        <h3><strong>Import Data</strong></h3>
        <br/>
        <b-form>
            <b-alert show variant="danger" v-if='create_error'>{{create_error}}</b-alert>
            <div :class="['form-group m-1 p-3', (successful ? 'alert-success' : '')]" v-show="successful">
              <span v-if="successful" class="label label-sucess">Published!</span>
            </div>
            <b-row style="margin-bottom: 10px;">
              <b-col xl="6" lg="6" md="6">
                <b-form-group
                    id="input-group-user-name"
                    label="User Name"
                    label-for="input-user-name"
                    >
                <b-form-input
                      id="input-user-name"
                      v-model="user_name"
                      type="text"
                      required
                      placeholder="Enter User Name"
                    ></b-form-input>
                </b-form-group>
              </b-col>
              <b-col xl="6" lg="6" md="6">
                <b-form-group
                    id="input-group-email"
                    label="User Email"
                    label-for="input-email"
                    >
                <b-form-input
                      id="input-email"
                      v-model="email"
                      type="email"
                      required
                      placeholder="Enter email"
                    ></b-form-input>
                </b-form-group>
              </b-col>
            </b-row>
            <b-row style="margin-bottom: 10px;">
              <b-col xl="6" lg="6" md="6">
                <b-form-group
                    id="input-group-user_phone"
                    label="User Phone"
                    label-for="input-user_phone"
                    >
                <b-form-input
                      id="input-user-phone"
                      v-model="user_phone"
                      type="text" @keypress="isNumber($event)" maxlength="10"
                      required
                      placeholder="Enter Phone"
                    ></b-form-input>
                </b-form-group>
              </b-col>
              <b-col xl="6" lg="6" md="6">
                <b-form-group
                    id="input-group-name"
                    label="Vendor Name"
                    label-for="input-name"
                    >
                <b-form-input
                      id="input-name"
                      v-model="name"
                      type="text"
                      required
                      placeholder="Enter Vendor Name"
                    ></b-form-input>
                </b-form-group>
              </b-col>
            </b-row>
            <b-row style="margin-bottom: 10px;">
              <b-col xl="6" lg="6" md="6">
                <b-form-group
                    id="input-group-token"
                    label="Token"
                    label-for="input-token"
                    >
                <b-form-input
                      id="input-token"
                      v-model="token"
                      type="text"
                      required
                      placeholder="Enter Token"
                    ></b-form-input>
                </b-form-group>
              </b-col>
              <b-col xl="6" lg="6" md="6">
                <b-form-group
                    id="input-group-url"
                    label="Website URL"
                    label-for="input-url"
                    >
                <b-form-input
                      id="input-url"
                      v-model="url"
                      type="text"
                      required
                      placeholder="Enter URL"
                    ></b-form-input>
                </b-form-group>
              </b-col>
            </b-row>
            <b-row style="margin-bottom: 10px; display:none;">
              <b-col xl="6" lg="6" md="6">
                <b-form-group
                    id="input-group-consumer_key"
                    label="Consumer Key"
                    label-for="input-consumer_key"
                    >
                <b-form-input
                      id="input-consumer_key"
                      v-model="consumer_key"
                      type="text"
                      required
                      placeholder="Enter Consumer Key"
                    ></b-form-input>
                </b-form-group>
              </b-col>
              <b-col xl="6" lg="6" md="6">
                <b-form-group
                    id="input-group-secret_key"
                    label="Secret Key"
                    label-for="input-secret_key"
                    >
                <b-form-input
                      id="input-secret_key"
                      v-model="secret_key"
                      type="text"
                      required
                      placeholder="Enter Secret Key"
                    ></b-form-input>
                </b-form-group>
              </b-col>
            </b-row>
            <input type="text" v-model="role_id" value="1" style="display:none;"/>
            <b-button type="submit" @click.prevent="create" variant="primary">Submit </b-button>
          </b-form>
        </div>
      </div>
    </b-overlay>
  </b-container>
</template>

<script>
  import apidata from '../../api/json.js';
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
        url: "",
        email:"",
        token:"",
        role_id:1,
        consumer_key:"",
        secret_key:"",
        user_name:"",
        user_phone:"",
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
            key: 'id',
            label: 'Vendor Id',
            sortable: true
          },
          {
            key: 'name',
            label: 'Name',
            sortable: true
          },
          {
            key: 'url',
            label: 'URL',
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
        if (!this.name) {
          this.create_error += "Add Name,";
        }
        if (!this.url) {
          this.create_error += "Add URL,";
        }
        if (!this.user_name) {
          this.create_error += "Add User Name,";
        }
        if (!this.user_phone) {
          this.create_error += "Add Phone Number,";
        }
        if (!this.token) {
          this.create_error += "Add Secret Key,";
        }
        if (!this.email) {
          this.create_error += "Add Email,";
        }
        if (this.create_error != "") {
          return false;
        }
        let formData = new FormData();
        formData.append("name", this.name);
        formData.append("url", this.url);
        formData.append("role_id", this.role_id);
        formData.append("user_name", this.user_name);
        formData.append("user_phone", this.user_phone);
        formData.append("email", this.email);
        formData.append("token", this.token);
        formData.append("secret_key", this.secret_key);
        formData.append("consumer_key", this.consumer_key);
        
        vendor
          .addVendor(formData)
          .then((response) => {
            this.successful = true;
            this.error = true;
            console.log(response.data.data);
            //this.items=response.data;
            this.items.push(response.data.data);
            this.name='';
            this.url='';
            this.user_name='';
            this.user_phone='';
            this.email='';
            this.consumer_key='';
            this.secret_key='';
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
        this.show = true;
        let formData = new FormData();
        formData.append("vid", id);
        formData.append("url", url);
        
        apidata
          .getJson(formData)
          .then((response) => {
              alert("Data imported successfully.");
              this.show = false;
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

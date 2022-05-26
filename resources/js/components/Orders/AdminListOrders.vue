<template>
  <b-container fluid> 
    <div v-if="this.$route.query.vid">
      <b-form-group
          id="input-group-Vendor"
          label="Choose Vendor"
          label-for="input-Vendor"
          >
          <b-form-select v-model="vendor" class="" :options="allvendors" value-field="id" text-field="name" @change="vendor_wise_detail">
            <template v-slot:first>
              <b-form-select-option :value="0" disabled>-- Select Vendor --</b-form-select-option>
              
            </template>
          </b-form-select>
      </b-form-group>
      <br/>
      <div class="header_title">
        <div class="header_inner">
          <b-row>
            <b-col xl="2" lg="2" md="2">
              <h3><strong>List Orders</strong></h3><br/>
            </b-col>
            <b-col xl="2" lg="2" md="2">
            	<select name="LeaveType" @change="onChange" class="form-control" v-model="key">
                 <option value="1">Ludhiana</option>
                 <option value="2">Jalandhar</option>
              </select>
            </b-col>
          </b-row>
        </div>
      </div>
      <div class="content_bar">
        <div class="card-body card">
          <div class="call-center-dashboard">
            <b-row>
              <b-col xl="9" lg="9" md="9">
                <b-alert show variant="danger" v-if="create_error">{{create_error}}</b-alert>
                <b-form @submit="onSubmit" class="date_range">
                  <div class="datepiker-block">
                    <span>From:&nbsp;</span>  <b-form-datepicker  id="from" v-model="date_from" :date-format-options="{ year: 'numeric', month: '2-digit', day: '2-digit' }"  locale="en-IN"></b-form-datepicker>
                  </div>
                  <div class="datepiker-block">
                    <span>To:&nbsp;</span> <b-form-datepicker id="to" v-model="date_to" :date-format-options="{ year: 'numeric', month: '2-digit', day: '2-digit' }"  locale="en-IN"></b-form-datepicker>
                  </div>
                  <b-button type="submit" variant="primary">Submit</b-button>
                </b-form>
              </b-col>
              <b-col xl="3" lg="3" md="3">
                <button type="button" class="download-btn btn btn-primary" v-on:click="download">Download</button>
              </b-col>
            </b-row>
          </div>
        </div>
        <div class="blue-bar"></div>
        <div class="card list-appointments space-bottom">
          <div class="col-sm-12">
            <b-row>
              <b-col xl="4" lg="4" md="4">
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
              <b-col xl="8" lg="8" md="8" class="search_field">
                <b-form-input
                    id="filter-input"
                    v-model="filter"
                    type="search"
                    placeholder="Type to Search">
                </b-form-input>
                  <b-input-group-append>
                    <b-button :disabled="!filter" @click="filter = ''">Clear</b-button>
                  </b-input-group-append>
              </b-col>
            </b-row>
          </div>
        </div>
        <br>
        <b-table striped hover responsive :items="items"
          :sort-by.sync="sortBy"
          :sort-desc.sync="sortDesc"
          sort-icon-left :filter-included-fields="filterOn" :filter="filter" :fields="fields" :per-page="perPage" :current-page="currentPage" show-empty>
          <template #empty="scope">
            <p style="text-align:center;">No record found.</p>
          </template>

          <template v-slot:cell(oid)="row">
            #{{(row.item.oid)}}
          	</template>
          <template v-slot:cell(action)="row">
            <router-link :to="{ name: 'OrderProfile', params: { oid: (row.item.oid).toString() }}"><b-icon icon="eye-fill" aria-hidden="true"></b-icon></router-link>&nbsp;&nbsp; <b-link @click="addstatus(row.item.oid)"><b-icon icon="exclamation-circle-fill" variant="primary" aria-hidden="true" data-toggle="tooltip" title="Change Status"></b-icon></b-link>
          </template>
        </b-table>
        <div class="text-center" v-if="seen">
          <b-spinner variant="primary" label="Text Centered"></b-spinner>
        </div>
        <b-pagination v-model="currentPage"
                   :total-rows="rows"
                  :per-page="perPage"
                  aria-controls="my-table">
        </b-pagination>
      </div>
    </div>
    <div v-else>
      <b-form-group
          id="input-group-Vendor"
          label="Choose Vendor"
          label-for="input-Vendor"
          >
          <b-form-select v-model="vendor" class="" :options="allvendors" value-field="id" text-field="name" @change="vendor_wise_detail">
            <template v-slot:first>
              <b-form-select-option :value="0" disabled>-- Select Vendor --</b-form-select-option>
              
            </template>
          </b-form-select>
      </b-form-group>
    </div>
    <div v-if="this.$route.query.vid">
      <b-modal id="modal-1" title="Change Status:" hide-footer  size="lg">
        <b-form>
          <b-alert show variant="danger" v-if='create_error'>{{create_error}}</b-alert>
          <div :class="['form-group m-1 p-3', (successful ? 'alert-success' : '')]" v-show="successful">
            <span v-if="successful" class="label label-sucess">Published!</span>
          </div>
          <b-form-input v-model="oid" id="oid_val" style="display:none"></b-form-input>
          
          <b-form-select v-model="status_assign">
            <template v-slot:first>
              <b-form-select-option value="null" disabled selected>-- Select Status --</b-form-select-option>
            </template>
              <b-form-select-option value="dtobooked">-- Change status to DTO Booked --</b-form-select-option>
              <b-form-select-option value="intransit">-- Change status to Intransit --</b-form-select-option>
              <b-form-select-option value="confirmed">-- Change status to Confirmed --</b-form-select-option>
              <b-form-select-option value="processing">-- Change status to processing --</b-form-select-option>
              <b-form-select-option value="completed">-- Change status to completed --</b-form-select-option>
              <b-form-select-option value="cancelled">-- Change status to cancelled --</b-form-select-option>
          </b-form-select>
          <b-button type="submit" @click.prevent="assign_status" variant="primary">Submit</b-button>
        </b-form> 
      </b-modal>
    </div>
  </b-container>
</template>
<script>
    import order from '../../api/order.js';
    import user from '../../api/user.js';
    import vendors from '../../api/vendors.js';
    import * as XLSX from 'xlsx/xlsx.mjs';

  export default {

    props: {

    },
    mounted() {
      if(this.$route.query.vid){
        this.getVendor();
        this.getVidz();
      }else{
        this.getVendor();
      }
    },
    data() 
    {
      return {
        vendor:null,
        allvendors: [],
        status_assign:"",
        key: "",
        oid:0,
        selected: null,
        options: [
          { value: null, text: 'Vendor Wise Detail' },
        ],
        ariaDescribedby: "",
        time: "",
        date: "",
        vid: 0,
        time_slots: [],
        status_assign_array:[],
        seen: false,
        date_from: '',
        city: '',
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
            key: 'oid',
            label: 'Order ID',
            sortable: true
          },
          {
            key: 'quantity',
            label: 'Qty',
            sortable: true
          },
          {
            key: 'total',
            label: 'Amount',
            sortable: true
          },
          {
            key: 'state',
            label: 'State',
            sortable: true
          },
          {
            key: 'city',
            label: 'City',
            sortable: true
          },
       
          {
            key: 'date_created_gmt',
            label: 'Order Date ',
            sortable: true
          },
          {
            key: 'waybill_no',
            label: 'AWB',
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
      };
    },
computed: {
      rows() {
        return this.items.length
      }
    },
  methods: {
    vendor_wise_detail(){
      console.log(this.vendor);
      window.location.assign("https://majime.nmf.im/admin/listOrders?vid="+this.vendor);
        // this.$router.push({name: 'adminlistOrders', params: {vid: this.vendor}})
    },
  	 onChange(event) {
            // console.log(event.target.value)
           // alert('something went wrong');
 
            console.log(this.city);

      let formData = new FormData();
      formData.append("city", this.city);
      
      order.citySearch(formData)
          .then((response) => {
              
                  this.items=response.data;
                  console.log(this.items);
                  
              // }
          })
          .catch((error) => {
              console.log(error);
              if (error.response.status == 422) {
                  this.errors_create = error.response.data.errors;
              }
              // loader.hide();
          });
    
        },
    getOrderDetails(vid)
    {
      let formData = new FormData();
      formData.append('vid', vid);
      formData.append('type', 'get');
      order.getOrderDetails(formData)
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
        })
    },
    onSubmit(event) {
      event.preventDefault()
      console.log(this.date_from);
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
      
      order.orderSearch(formData)
          .then((response) => {
              
                  this.items=response.data;
                  console.log(this.items);
                  
              // }
          })
          .catch((error) => {
              console.log(error);
              if (error.response.status == 422) {
                  this.errors_create = error.response.data.errors;
              }
              // loader.hide();
          });
    
    },
getVendor(){
  vendors.getVendors()
  .then((response) => {
      this.allvendors=response.data;
    })
    .catch((error) => {
        // console.log(error);
        if (error.response.status == 422) {
            this.errors_create = error.response.data.errors;
        }
        // loader.hide();
    });
},
  getVidz()
     {
      localStorage.setItem("ivid", this.$route.query.vid);
      this.vid = this.$route.query.vid;
      this.getOrderDetails(this.$route.query.vid);
     },
    addstatus(oid){
      //alert(oid);
                 this.oid = oid;
                 let formData= new FormData();
                  formData.append("oid", this.oid);
                 this.$bvModal.show("modal-1");
                 console.log(this.oid);
               },
   assign_status() 
    {
        this.vid = JSON.parse(localStorage.getItem("ivid"));
         let formData = new FormData();
        formData.append("oid",this.oid);
        formData.append("status_assign",this.status_assign);
        formData.append("vid",this.vid);
      order.changeStatus(formData)
          .then((response) => {
              this.$bvModal.hide("modal-1");
                  alert('Status Update Successfully');
               window.location.reload();
          })
          .catch((error) => {
              // console.log(error);
              if (error.response.status == 422) {
                  this.errors_create = error.response.data.errors;
              }
              // loader.hide();
          });
    },
            download : function() {
                const data = XLSX.utils.json_to_sheet(this.items)
                const wb = XLSX.utils.book_new()
                XLSX.utils.book_append_sheet(wb, data, 'data')
                XLSX.writeFile(wb,'download_list.xlsx')
            },

  },

clearData()
    {
      this.oid ='';
    },

	// onCity(){
 
 //      console.log(this.city);
 //      let formData = new FormData();
 //      formData.append("date_from", this.city);
      
 //      order.citySearch(formData)
 //          .then((response) => {
              
 //                  this.items=response.data;
 //                  console.log(this.items);
                  
 //              // }
 //          })
 //          .catch((error) => {
 //              console.log(error);
 //              if (error.response.status == 422) {
 //                  this.errors_create = error.response.data.errors;
 //              }
 //              // loader.hide();
 //          });
    
 //    },
};  
</script>

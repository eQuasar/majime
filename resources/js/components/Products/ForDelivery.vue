<template>
  <b-container fluid> 
     <b-overlay
      :show="show"
      rounded="sm"
      spinner-type="grow"
      spinner-variant="primary"
      spinner-small
    >
    <div class="header_title">
      <div class="header_inner">
        <h3><strong>Dispatched List</strong></h3>
      </div>
    </div> 
         <b-row>
            <b-col xl="12" lg="12" md="12">
                <div class="list-appointments content_bar">
                        <div class="card-body card">
                            <b-row>
                              <b-col xl="6" lg="6" md="6" class="search_field">
                                    <b-form-input id="filter-input" v-model="filter" type="search" placeholder="Type to Search"></b-form-input>
                                    <b-input-group-append><b-button :disabled="!filter" @click="filter = ''">Clear</b-button></b-input-group-append>
                                </b-col>
                                <b-col xl="6" lg="6" md="6">
                                    <button type="button" class="download-btn btn btn-primary" v-on:click="fordeliverydownload" style=" margin-left: 15px;">Download</button>
                                    <button type="button" class="download-btn btn btn-primary" v-on:click="dispatchstatus">Dispatch</button>                                    
                                </b-col>                                
                            </b-row>
                          </div>
                        </div>
                     

                          </div>
                        </div>
                         </b-col>
                    </b-row>
   <!--  </br> -->
   <!--  <div class="content_bar"> 
      <div class="select-list">
    <b-row>
        <b-col xl="2" lg="2" md="2">
            <select class="form-control custom-select" v-model="status" :options="allstatusdata" @change="onChangeStatus($event)">
                <option disabled value="null">Select status</option>
                <option v-for="data in allstatusdata" :value="data.status">{{data.status}}</option>
            </select>
        </b-col>
        <b-col xl="2" lg="2" md="2">
            <select class="form-control custom-select" v-model="city" :options="allcitydata" @change="onChangeCity($event)">
                <option disabled value="null">Select city</option>
                <option v-for="data in allcitydata" :value="data.city">{{data.city}}</option>
            </select>
        </b-col>
    </b-row>
</div> -->
<!-- </br>
<div class="card-body card"> -->
<!--     <div class="call-center-dashboard">
 -->      <!--   <b-row>
            <b-col xl="8" lg="8" md="8"> -->
           <!--      <b-alert show variant="danger" v-if="create_error">{{create_error}}</b-alert> -->
               <!--  <b-form @submit="onSubmit" class="date_range">
                    <div class="datepiker-block">
                        <span>From:&nbsp;</span> <b-form-datepicker id="from" v-model="date_from" :date-format-options="{ year: 'numeric', month: '2-digit', day: '2-digit' }" locale="en-IN"></b-form-datepicker>
                    </div>
                    <div class="datepiker-block"><span>To:&nbsp;</span> <b-form-datepicker id="to" v-model="date_to" :date-format-options="{ year: 'numeric', month: '2-digit', day: '2-digit' }" locale="en-IN"></b-form-datepicker></div>
                    <b-button type="submit" variant="primary">Submit</b-button>
                </b-form> -->
     <!--        </b-col> -->
          <!--   <b-col xl="4" lg="4" md="4" class="search_field">
              <b-form-input id="filter-input" v-model="filter" type="search" placeholder="Type to Search"></b-form-input>
              <b-input-group-append><b-button :disabled="!filter" @click="filter = ''">Clear</b-button></b-input-group-append>
            </b-col> -->
   <!--      </b-row>
        <div class="blue-bar"></div>
        <div class="content_bar card list-appointments space-bottom">
            <div class="col-sm-12">
                <b-row>
                    <b-col xl="5" lg="5" md="5">
                        <b-form-group class="mb-0"> Show <b-form-select id="per-page-select" v-model="perPage" :options="pageOptions" size="sm"></b-form-select> entries </b-form-group>
                    </b-col>
                       <b-col xl="5" lg="5" md="5" class="search_field">
              <b-form-input id="filter-input" v-model="filter" type="search" placeholder="Type to Search"></b-form-input>
              <b-input-group-append><b-button :disabled="!filter" @click="filter = ''">Clear</b-button></b-input-group-append>
            </b-col>
                    <b-col xl="2" lg="2" md="2">
                        <button type="button" class="download-btn btn btn-primary" v-on:click="fordeliverydownload" style="margin-left: 15px;">Download</button>
                         <button type="button" class="download-btn btn btn-primary" v-model="statusAssign" v-on:click="addstatus">Change Status</button> -->
                 <!--    </b-col>
                </b-row>
            </div>
        </div>
    </div>
</div> -->
      </br>
        <b-table striped hover responsive :items="items"
                  :sort-by.sync="sortBy"
                  :sort-desc.sync="sortDesc"
                   sort-icon-left :filter-included-fields="filterOn" :filter="filter" :fields="fields" :per-page="perPage" :current-page="currentPage" show-empty>
              <template #empty="scope">
                  <p style="text-align:center;">No record found, choose date filter to found the result.</p>
              </template>
              <template #head(select)="data">
                  <span class="text-info"><input type="checkbox" @click="selectedAll" v-model="allSelected">&nbsp;{{ data.label }}</span>
              </template>
              <template v-slot:cell(oid)="row">
                #{{(row.item.oid)}}
              </template>
              <template v-slot:cell(status)="row">
                <span :class="row.item.status"> {{(row.item.status)}}</span>
              </template>
              <template v-slot:cell(select)="row">
                <input type="checkbox" :value="row.item.oid" v-model="selectall" >
              </template>
              <!-- <template v-slot:cell(action)="row">
               <p class="h3 mb-2">   <router-link :to="{ name: 'OrderProfile', params: { oid:(row.item.oid).toString() }}"><b-icon icon="eye-fill" aria-hidden="true"></b-icon></router-link>
                &nbsp;&nbsp; <b-link @click="addstatus(row.item.oid)"><b-icon icon="type-strikethrough" variant="primary" aria-hidden="true" data-toggle="tooltip" title="Change Status"></b-icon></b-link></p>
              </template> -->
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
                              <b-form-select-option value="dtobooked">Change status to DTO Booked</b-form-select-option>
                              <b-form-select-option value="intransit">Change status to Intransit</b-form-select-option>
                              <b-form-select-option value="confirmed">Change status to Confirmed</b-form-select-option>
                              <b-form-select-option value="processing">Change status to Processing</b-form-select-option>
                              <b-form-select-option value="completed">Change status to Completed</b-form-select-option>
                              <b-form-select-option value="cancelled">Change status to Cancelled</b-form-select-option>
                              <b-form-select-option value="packed">Change status to Packed</b-form-select-option>
                              <b-form-select-option value="rtd-online">Change status to RTD-Online</b-form-select-option>
                              <b-form-select-option value="rtd-cod">Change status to RTD-COD</b-form-select-option>
                              <b-form-select-option value="refunded">Change status to Refunded</b-form-select-option>
                              <b-form-select-option value="deliveredtocust">Change status to delivered to customer</b-form-select-option>
                              <b-form-select-option value="dtodel2warehouse">Change status to DTO Delivery to Warehouse</b-form-select-option>
                              <b-form-select-option value="dtointransit">Change status to DTO Intransit</b-form-select-option>
                              <b-form-select-option value="pickedup">Change status to Picked up</b-form-select-option>
                              <b-form-select-option value="on-hold">Change status to on-hold</b-form-select-option>
                          </b-form-select>
                          <b-button type="submit" @click.prevent="assign_status" variant="primary">Submit</b-button>
                 </b-form> 
      </b-modal>

       <b-modal id="modal-2" title="Set Dispatch Status:" hide-footer  size="lg">
          <b-form>
            <b-form-input
              id="input-live"
              v-model="dispatch"
              aria-describedby="input-live-help input-live-feedback"
              placeholder="Enter your Order ID / AWB Number"
              trim
            ></b-form-input>
            <b-button type="submit" @click.prevent="assign_dispatch" variant="primary" style="display: none">Submit</b-button>
          </b-form>
          <p id="data_msg"></p>
      </b-modal>
      </b-overlay>
     </b-container>
    </template>
    <script>
    import uniq from 'lodash/uniq';
    import product from '../../api/Product.js';
    import order from '../../api/order.js';
    import user from '../../api/user.js';
    import * as XLSX from 'xlsx/xlsx.mjs';
  export default {
    props: {
    },
    mounted() {
      this.getVidz();
      //this.getState();
      this.getCity();
      this.getStatus();
      this.getDeliveryDetail();
    },
    data() 
    {
      return {
        dispatch:'',
        selected: [],
        allSelected: false,
        vendor:null,
        status_assign:"",
        allStateData: [],
        key: "",
        oid:0,
        status: null,
        status_assign: null,
        city: null,
        ariaDescribedby: "",
        time: "",
        date: "",
        vid: 0,
        allstatesdata:[],
        allcitydata:[],
        allstatusdata:[],
        time_slots: [],
        status_assign_array:[],
        seen: false,
        date_from: '',
        statusAssign:'',
        selectall:[],
        state:'',
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
          key:'select',
          label: '',
          sortable:true
        }, 

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
            key: 'status',
            label: 'Status',
            sortable: true
          },
            // {
            //   key: 'action',
            //   label: 'Action',
            //   sortable: false
            // }
        ],
        items:[],
        errors_create:[],
        successful: false,
        create_error:'',
        state2:[],
      };
    },
computed: {
      rows() {
        return this.items.length
      }
    },
    methods: {
      assign_dispatch() 
        {
          let formData = new FormData();
           formData.append('vid', this.vid);
          formData.append("dispatch", this.dispatch);
          order.changeStatusDispatch(formData)
              .then((response) => {
                // alert(response.data.msg);
                  // this.items=response.data;
                  if(response.data.error == false){
                    // data_msg.
                    // this.$bvModal.hide("modal-2");
                     // this.getDeliveryDetail();
                     // response.data.msg
                      this.dispatch = '';
                     document.getElementById("data_msg").textContent = response.data.msg;
                    setTimeout(function(){
                      this.dispatch = '';
                      // document.getElementById("data_msg").textContent = '';
                    }, 1000);
                    this.getDeliveryDetail();
                  }else{
                      this.dispatch = '';
                    document.getElementById("data_msg").textContent = response.data.msg;
                    setTimeout(function(){
                      this.dispatch = '';
                      // document.getElementById("data_msg").textContent = '';
                    }, 1000);
                  }
            })
          .catch((error) => {
              console.log(error);
              if (error.response.state == 422) {
                  this.errors_create = error.response.data.errors;
              }
                });
        },
     dispatchstatus() {
        // this.allSelected = "false";
        // this.$prompt("modal-2").then(text => {
  // do somthing with text
// });

         this.$bvModal.show("modal-2");
        // this.addstatus();
      },
      async selectedAll() {
        if (this.allSelected) {
          this.selectall = [];
        } else {
          const selected = this.items.map((u) => u.oid);
          this.selectall = selected;
        }
      },
         onChangeCity(event)
         {
          this.vid = JSON.parse(localStorage.getItem("ivid"));
          console.log(event.target.value);
          let formData = new FormData();
          formData.append('vid', this.vid);
          formData.append("city", this.city);
          order.citySearch(formData)
              .then((response) => 
                   {
                  this.items=response.data;
                  console.log(this.items);
                    })
          .catch((error) => 
                  {
              console.log(error);
              if (error.response.city == 422) {
                  this.errors_create = error.response.data.errors;
                  }
                  });
          },
        getStatus(){
          order.getStatus()
            .then((response) => {
                this.allstatusdata=response.data;
              })
                  .catch((error) => {
              if (error.response.status == 422) {
                this.errors_create = error.response.data.errors;
                }
                });
      },
        getState(){
          order.getStates()
            .then((response) => {
                this.allstatesdata=response.data;
         })
       .catch((error) => {
        if (error.response.status == 422) {
            this.errors_create = error.response.data.errors;
        }
        });
        },
        getCity(){
          order.getCity()
             .then((response) => {
                 this.allcitydata=response.data;
        })
        .catch((error) => {
            if (error.response.status == 422) {
              this.errors_create = error.response.data.errors;
              }
                 });
              },
        onChangeState(event) {
          console.log(event.target.value);
          let formData = new FormData();
           formData.append('vid', vid);
          formData.append("state", this.state);
          order.stateSearch(formData)
              .then((response) => {
                  this.items=response.data;
                  console.log(this.items);
            })
          .catch((error) => {
              console.log(error);
              if (error.response.state == 422) {
                  this.errors_create = error.response.data.errors;
              }
                });
              },
        onChangeStatus(event) {
         this.vid = JSON.parse(localStorage.getItem("ivid"));
          console.log(event.target.value);
          let formData = new FormData();
          formData.append('vid', this.vid);
          formData.append("status", this.status);
          order.statusSearch(formData)
              .then((response) => 
                   {
                  this.items=response.data;
                  console.log(this.items);
                    })
          .catch((error) => 
                  {
              console.log(error);
              if (error.response.status == 422) {
                  this.errors_create = error.response.data.errors;
                  }
                  });
          },
         getDeliveryDetail(){
           this.vid = JSON.parse(localStorage.getItem("ivid"));
            let formData = new FormData();
            formData.append('vid',this.vid);
            formData.append('type', 'get');
            product.getDeliveryDetails(formData)
                .then(( response ) => {
                console.log(response);
                    this.items=response.data;
              console.log(this.items);
            })
            .catch(response => {
                this.successful = false;
                //alert('something went wrong');
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
                               })
              .catch((error) => {
                  console.log(error);
                  if (error.response.status == 422) {
                      this.errors_create = error.response.data.errors;
                  }
                      });
        
          },
        getVidz()
           {
            if(this.$userId == 1){
              this.vid = JSON.parse(localStorage.getItem("ivid"));
              localStorage.setItem("ivid", this.vid);
                  this.getDeliveryDetail();
            }else{
              let formData= new FormData();
              formData.append("user_id", this.$userId);
              user.getVid(formData)
               .then(( response ) => {
                  this.vid = response.data;
                  localStorage.setItem("ivid", this.vid);
                  this.getDeliveryDetail();
          
                })
                .catch(response => {
                    this.successful = false;
                    //alert('something went wrong');
                })
            }
           },
        addstatus()
        {
          let formData= new FormData();
          this.$bvModal.show("modal-1");
          console.log(this.oid);
        },
       assign_status() 
        {
          this.vid = JSON.parse(localStorage.getItem("ivid"));
          let formData = new FormData();
          //formData.append("oid",this.oid);
          formData.append("status_assign",this.status_assign);
          formData.append("selectall",this.selectall)
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
      download2 : function() {
        let formData=new FormData();
        formData.append("selectall",this.selectall);
        formData.append("vid",this.vid);
          // const data = XLSX.utils.json_to_sheet(this.items)
          // const wb = XLSX.utils.book_new()
          // XLSX.utils.book_append_sheet(wb, data, 'data')
          // XLSX.writeFile(wb,'download_list.xlsx')
      },

      listOrderChangeStatus()
        {
          this.vid=JSON.parse(localStorage.getItem("ivid"));
          let formData=new FormData();
          formData.append("selectall",this.selectall);
          formData.append("statusAssign",this.statusAssign);
          formData.append("vid",this.vid);
          order.ListOrderStatus_assign(formData)
          .then((response)=>{
              alert(response.data.msg);
              window.location.reload();
          })
            .catch(response=>{
              this.successful=false;
            })
        },
      clearData()
        {
          this.oid ='';
        },

       fordeliverydownload()
      {
        this.show=true;
            let formData = new FormData();
            formData.append("selectall",this.selectall)
            formData.append("vid",this.vid)
            order.deliverysheet(formData)
             .then((response) => {
                console.log(response.data[0]);
                  this.items2=response.data[0];
                  const data = XLSX.utils.json_to_sheet(this.items2)
                const wb = XLSX.utils.book_new()
                XLSX.utils.book_append_sheet(wb, data, 'data')
                XLSX.writeFile(wb,'Dispacthed_orders.xlsx')
          })
          .catch((error) => {
              console.log(error);
              if (error.response.status == 422) {
                  this.errors_create = error.response.data.errors;
              }
              // loader.hide();
          });
          this.show=false;
      }, 

  },
};  
</script>





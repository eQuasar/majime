<template>
  <b-container fluid> 
        <b-overlay :show="show" rounded="sm" spinner-type="grow" spinner-variant="primary" spinner-small>
    <div class="header_title">
      <div class="header_inner">
        <h3><strong>List Orders</strong></h3>
      </div>
    </div> 
   <br>
    <div class="content_bar">
      <div class="select-list">
    <b-row>
      <b-form @submit="onSubmit" class="date_range">
      <b-col xl="2" lg="2" md="2">
            <select class="form-control custom-select" v-model="status" :options="allstatusdata" @change="onChangeStatus($event)">
                <option disabled value="null">Select status</option>
                <option v-for="data in allstatusdata" :value="data.status">{{data.status}}</option>
            </select>
        </b-col>
        <!-- <b-col xl="2" lg="2" md="2">
            <select class="form-control custom-select" v-model="city" :options="allcitydata" @change="onChangeCity($event)">
                <option disabled value="null">Select city</option>
                <option v-for="data in allcitydata" :value="data.city">{{data.city}}</option>
            </select>
        </b-col> -->
        <b-col xl="6" lg="6" md="6">
                  <b-alert show variant="danger" v-if="create_error">{{create_error}}</b-alert>
                    
                      <div class="datepiker-block"><span>From:&nbsp;</span> <b-form-datepicker id="from" v-model="date_from" :date-format-options="{ year: 'numeric', month: '2-digit', day: '2-digit' }"
                          locale="en"></b-form-datepicker></div>
                      <div class="datepiker-block"><span>To:&nbsp;</span> <b-form-datepicker id="to" v-model="date_to" :date-format-options="{ year: 'numeric', month: '2-digit', day: '2-digit' }" locale="en"></b-form-datepicker></div>
                      <b-button type="submit" variant="primary">Submit</b-button>
                 
              </b-col>
            </b-form>
    </b-row>
  </div>
</div>
<br>
  <div class="card-body card">
    <div class="call-center-dashboard">
          <b-row>
            
               <b-col xl="4" lg="4" md="4" class="search_field">
                <b-form-input
                        id="search"
                        v-model="filterit"
                        placeholder="Type to Search" 
                      ></b-form-input>
                      <b-input-group-append>
                        <b-button  v-on:click="search_data">Search</b-button>
                      </b-input-group-append>
                  </b-col>
            </b-row> 
          <div class="blue-bar"></div>
            <div class="content_bar card list-appointments">
              <div class="col-sm-12">
                  <b-row>
                      <b-col xl="5" lg="5" md="5">
                          <b-form-group class="mb-0"> Show <b-form-select id="per-page-select" v-model="perPage" :options="pageOptions" size="sm"></b-form-select> entries </b-form-group>
                      </b-col>
                      <b-col>
                          <button type="button" class="download-btn btn btn-primary" v-on:click="selectdownload" style="margin-left: 15px;">Download</button>
                          
                          <span v-if="saw5"><button type="button" class="download-btn btn btn-primary" v-model="statusAssign" v-on:click="addstatus">Change Status</button></span>
                      </b-col>
                  </b-row>
              </div>
          </div>
        </div>
    </div>
</div>
      </br>
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
              <template v-slot:cell(name)="row">
                {{(row.item.first_name)}} {{(row.item.last_name)}} 
              </template>
              <template v-slot:cell(status)="row">
                <span :class="row.item.status"> {{(row.item.status)}}</span>
              </template>
             
              <template v-slot:cell(select)="row">
                <input type="checkbox" :value="row.item.oid" v-model="selectall" >
              </template>
              <template v-slot:cell(action)="row">
               <p class="h3 mb-2">   <router-link :to="{ name: 'OrderProfile', params: { oid:(row.item.oid).toString() }}"><b-icon icon="eye-fill" aria-hidden="true"></b-icon></router-link>&nbsp;&nbsp;<span v-if="saw5">
                &nbsp;&nbsp; <b-link @click="addstatusOID(row.item.oid, row.item.status)"><b-icon icon="check-square-fill" variant="primary" aria-hidden="true" data-toggle="tooltip" title="Change Status"></b-icon></b-link></span></p>
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
       
       <b-modal id="modal-1" title="Change Status:" hide-footer  size="lg" >
             <b-form>
                <b-alert show variant="danger" v-if='create_error'>{{create_error}}</b-alert>
                  <div :class="['form-group m-1 p-3', (successful ? 'alert-success' : '')]" v-show="successful">
                      <span v-if="successful" class="label label-sucess">Published!</span>
                  </div>
                     <b-form-input v-model="oid" id="oid_val" style="display:none"></b-form-input>
                     <b-form-input v-model="status" id="status_val" style="display:none"></b-form-input>
               
                       <b-form-select v-model="status_assign">
                         <template v-slot:first>
                            <b-form-select-option value="null" disabled selected>-- Select Status --</b-form-select-option>
                          </template>
                              <b-form-select-option value="dtobooked">Change status to DTO Booked</b-form-select-option>
                              <b-form-select-option value="dispatched">Change status to Dispatched</b-form-select-option>
                              <b-form-select-option value="intransit">Change status to Intransit</b-form-select-option>
                              <b-form-select-option value="confirmed">Change status to Confirmed</b-form-select-option>
                              <b-form-select-option value="processing">Change status to Processing</b-form-select-option>
                              <b-form-select-option value="completed">Change status to Completed</b-form-select-option>
                              <b-form-select-option value="cancelled">Change status to Cancelled</b-form-select-option>
                              <b-form-select-option value="packed">Change status to Packed</b-form-select-option>
                              <b-form-select-option value="rtd-online">Change status to RTD-Online</b-form-select-option>
                              <b-form-select-option value="rtd-cod">Change status to RTD-COD</b-form-select-option>
                              <b-form-select-option value="dto-refunded">Change status to DTO Refunded</b-form-select-option>
                              <b-form-select-option value="deliveredtocust">Change status to delivered to customer</b-form-select-option>
                              <b-form-select-option value="dtodel2warehouse">Change status to DTO Delivery to Warehouse</b-form-select-option>
                              <b-form-select-option value="dtointransit">Change status to DTO Intransit</b-form-select-option>
                              <b-form-select-option value="pickedup">Change status to Picked up</b-form-select-option>
                              <b-form-select-option value="on-hold">Change status to on-hold</b-form-select-option>
                              <b-form-select-option value="rto-delivered">Change status to RTO Delivered</b-form-select-option>
                              <b-form-select-option value="dtodelivered">Change status to DTO Delivered </b-form-select-option>
                               <b-form-select-option value="closed">Change status to Closed </b-form-select-option>
                          </b-form-select>
      
                          <b-button type="submit" @click.prevent="assign_status" variant="primary">Submit</b-button>
                 </b-form> 
          </b-modal>
      </b-overlay>
     </b-container>
    </template>
    <script>
    import uniq from 'lodash/uniq';
    import order from '../../api/order.js';
    import user from '../../api/user.js';
    import * as XLSX from 'xlsx/xlsx.mjs';

  export default {
    props: {
    },
    mounted() {
      this.getVidz();
      //this.getState();
      // this.getCity();
      this.getStatus();
    },
    data() 
    {
      return {
        saw5: false,
        seen: false,
        show: false,
        selected: [],
        allSelected: false,
        vendor:null,
        status_assign:"",
        allStateData: [],
        key: "",
        filterit: '',
        oid:0,
        status: null,
        status_assign: null,
        city: null,
        ariaDescribedby: "",
        time: "",
        date: "",
        vid: 0,
        allstatesdata:[],
        // allcitydata:[],
        allstatusdata:[],
        time_slots: [],
        status_assign_array:[],
        date_from: '',
        statusAssign:'',
        selectall:[],
        search:"",
        state:'',
        date_to: '',
        sortBy: 'date',
        sortDesc: true,
        perPage: 10,
        currentPage: 1,
        pageOptions: [5, 10, 15, 20, 50, 100],
        filter: '',
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
            key: 'name' ,
            label: 'Name',
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
            key: 'date_created',
            label: 'Order Date ',
            sortable: true
          },
          
          {
            key: 'payment_method_title',
            label: 'Payment Mode',
            sortable: false
          },
          {
            key: 'phone',
            label: 'Contact',
            sortable: false
          },
          {
            key: 'status',
            label: 'Status',
            sortable: true
          },
          {
            key: 'waybill_no',
            label: 'AWB',
            sortable: false
          },
          {
            key: 'action',
            label: 'Action',
            sortable: false
          }
        ],
        items:[],
        items2:[],
        items3:[],
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
      async selectedAll() {
        if (this.allSelected) {
          this.selectall = [];0
        } else {
          const selected = this.items.map((u) => u.oid);
          this.selectall = selected;
        }
      },
        //  onChangeCity(event)
        //  {
        //    this.show=true;
        //   this.vid = JSON.parse(localStorage.getItem("ivid"));
        //   console.log(event.target.value);
        //   let formData = new FormData();
        //   formData.append('vid', this.vid);
        //   formData.append("city", this.city);
        //   order.citySearch(formData)
        //       .then((response) => 
        //            {
        //           this.items=response.data;
        //           this.show=false;
        //           console.log(this.items);
        //             })
        //   .catch((error) => 
        //           {
        //       console.log(error);
        //       if (error.response.city == 422) {
        //           this.errors_create = error.response.data.errors;
        //           }
        //           });
        //   },
        getStatus(){
        	 this.vid = JSON.parse(localStorage.getItem("ivid"));
        	  let formData = new FormData();
          formData.append('vid', this.vid);
          order.getStatus(formData)
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
          this.show=true;
        	 this.vid = JSON.parse(localStorage.getItem("ivid"));
        	  let formData = new FormData();
          formData.append('vid', this.vid);
          order.getStates()
            .then((response) => {
                this.allstatesdata=response.data;
         })
       .catch((error) => {
        if (error.response.status == 422) {
            this.errors_create = error.response.data.errors;
        }
        });
       this.show=false;
        },
        // getCity(){
        // 	 this.vid = JSON.parse(localStorage.getItem("ivid"));
        // 	  let formData = new FormData();
        //   formData.append('vid', this.vid);
        //   order.getCity(formData)
        //      .then((response) => {
        //          this.allcitydata=response.data;
        //            console.log(this.allcitydata);
        // })
        // .catch((error) => {
        //     if (error.response.city == 422) {
        //       this.errors_create = error.response.data.errors;
        //       }
        //          });
        //       },
        onChangeState(event) {
          this.show=true;
          console.log(event.target.value);
           formData.append('vid', vid);
          let formData = new FormData();
          formData.append("state", this.state);
          order.stateSearch(formData)
              .then((response) => {
                  this.items=response.data;
                  this.show=false;
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
          this.show=true;
         this.vid = JSON.parse(localStorage.getItem("ivid"));
          console.log(event.target.value);
          let formData = new FormData();
          formData.append('vid', this.vid);
          formData.append("status", this.status);
          order.statusSearch(formData)
              .then((response) => 
                   {
                  this.items=response.data;
                  this.show=false;
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
         getOrderDetails(vid){
           this.show = true;
            let formData = new FormData();
            formData.append('vid', vid);
            formData.append('type', 'get');
            order.getOrderDetails(formData)
                .then(( response ) => {
                console.log(response);
                    this.items=response.data;
                    this.show=false;
              console.log(this.items);
            })
            .catch(response => {
                this.successful = false;
                this.$alert("",'something went wrong');
            })
        },
        onSubmit(event) {
          this.show=true;
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
          // formData.append("city", this.city);
          formData.append("status", this.status);
          formData.append("vid", this.vid);
            order.orderSearch(formData)
              .then((response) => {    
                      this.items=response.data;
                      this.show=false;
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
            // alert("sss");
            if(this.$userId == 1){
              this.saw5 = true;
              this.vid = JSON.parse(localStorage.getItem("ivid"));
              localStorage.setItem("ivid", this.vid);
              this.getOrderDetails(this.vid);
                  this.allSelected=-false;
            }else{
              this.saw5 = false;
              let formData= new FormData();
              formData.append("user_id", this.$userId);
              user.getVid(formData)
               .then(( response ) => {
                  this.vid = response.data;
                  localStorage.setItem("ivid", this.vid);
                  this.getOrderDetails(this.vid);
                  this.allSelected=-false;
                })
                .catch(response => {
                    this.successful = false;
                    this.$alert("",'something went wrong');
                })
            }
           },
    

     addstatusOID(oid,status) {
        this.allSelected=-false;
        this.oid = oid;
        this.status = status;
        // this.allSelected = "false";
        this.$bvModal.show("modal-1");
        // this.addstatus();
      },
        addstatus()
        {
          let formData= new FormData();
          this.$bvModal.show("modal-1");
          
          // console.log(this.oid);
        },
       assign_status() 
        {
          this.show=true;
          this.$bvModal.hide("modal-1");
          this.vid = JSON.parse(localStorage.getItem("ivid"));
          let formData = new FormData();
          // formData.append("oid",this.oid);
          formData.append("status",this.status);
          formData.append("status_assign",this.status_assign);
          if(this.oid != 0){
            formData.append("selectall",this.oid)
          }else{
            formData.append("selectall",this.selectall);
          }
          formData.append("vid",this.vid);
          order.changeStatus(formData)
              .then((response) => {
                  this.$alert("","Order Sucessfully Updated");
                  // alert(response.data.msg);
//                   this.$confirm("Are you sure?").then(() => {
//   //do something...
// });
// this.$alert("Order Successfully Updated");
                  this.show=false;
                  this.getVidz();
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
              $alert(response.data.msg);
              this.getVidz();
          })
            .catch(response=>{
              this.successful=false;
            })
        },
      clearData()
        {
          this.oid ='';
        },

       selectdownload()
      {
        this.show=true;
            let formData = new FormData();
            formData.append("selectall",this.selectall)
            formData.append("vid",this.vid)
            order.downloadsheet(formData)
             .then((response) => {
              console.log(response.data[0]);
                  this.items2=response.data[0];
                  const data = XLSX.utils.json_to_sheet(this.items2)
                const wb = XLSX.utils.book_new()
                XLSX.utils.book_append_sheet(wb, data, 'data')
                XLSX.writeFile(wb,'List_orders.xlsx')
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

      search_data(){
        this.show=true;
      	let formData = new FormData();
          formData.append("filterit", this.filterit);
           formData.append("vid",this.vid)
            order.filterSearch(formData)
              .then((response) => {    
                      this.items=response.data;
                      this.show=false;
                      console.log(this.items);
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


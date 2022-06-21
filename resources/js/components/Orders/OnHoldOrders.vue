  <template>
    <b-container fluid>
        <div class="header_title">
            <div class="header_inner">
                <h2><strong>On-Hold Orders</strong></h2>
            </div>
        </div>
        <div class="clear">&nbsp;</div>
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
                                    <button type="button" class="download-btn btn btn-primary" v-on:click="ProcessingList_download" style=" margin-left: 15px;">Download</button>
                                                                       
                                </b-col>                                
                            </b-row>
                        </div>
                        <br>
                        <div class="groomer-page">

                          <b-table
                              striped
                              hover
                              responsive
                              :items="items"
                              :sort-by.sync="sortBy"
                              :sort-desc.sync="sortDesc"
                              sort-icon-left
                              :filter-included-fields="filterOn"
                              :filter="filter"
                              :fields="fields"
                              :per-page="perPage"
                              :current-page="currentPage"
                              show-empty
                          >
                            <template #head(select)="data">
                              <span class="text-info"><input type="checkbox" @click="selectedAll" v-model="selectallcheckbox">&nbsp;{{ data.label }}</span>
                            </template>
                            <template #empty="scope">
                              <p style="text-align: center;">No record found, choose date filter to found the result.</p>
                            </template>
                            <template v-slot:cell(sr)="row">            
                                {{((currentPage-1)*perPage)+(row.index)+1}} 
                            </template>
                            <template v-slot:cell(select)="row">   
                                <input type="checkbox" :value="row.item.oid" v-model="allSelected">
                            </template>
                            <template v-slot:cell(status)="row">
                              <span :class="row.item.status"> {{(row.item.status)}}</span>
                            </template>
                              <template v-slot:cell(action)="row">
                               <p class="h3 mb-2"><b-link @click="confirmstatusOID(row.item.oid)"><b-icon icon="check-circle-fill" variant="primary" aria-hidden="true" data-toggle="tooltip" title="Confirm Status" v-model="statusAssign"></b-icon></b-link>
                                &nbsp;<b-link @click="cancelstatusOID(row.item.oid)"><b-icon icon="x-square-fill" variant="primary" aria-hidden="true" data-toggle="tooltip" title="Cancel Status" v-model="statusAssign"></b-icon></b-link>
                               </p>
                              </template>
                      
                         </b-table>
                        </div>
                        <div class="text-center" v-if="seen">
                            <b-spinner variant="primary" label="Text Centered"></b-spinner>
                        </div>
                        <b-pagination v-model="currentPage" :total-rows="rows" :per-page="perPage" aria-controls="my-table"></b-pagination>
                </div>
            </b-col>
          </b-row>
          
  </b-container>
</template>

<script>
    import order from '../../api/order.js';
    import user from '../../api/user.js';
    import * as XLSX from 'xlsx/xlsx.mjs';

  export default {
    props: {
    },
    mounted() {
      this.getVidz();
      //this.getPackdetailRefund();
    },
    data() 
    {
      return {
        vendor:null,
        status_assign:"",
        oid:0,
        //selected: null,
        options: [
          { value: null, text: 'Vendor Wise Detail' },
        ],
        ariaDescribedby: "",
        time: "",
        date: "",
        vid: 0,
        time_slots: [],
        status_assign_array:[],
        allSelected: false,
        selectallcheckbox:false,
        seen: false,
        date_from: '',
        date_to: '',
        statusAssign:'',
        sortBy: 'date',
        sortDesc: true,
        perPage: 10,
        currentPage: 1,
        allSelected:[],
        status:'',
        pageOptions: [5, 10, 15, 20, 50, 100],
        filter: null,
        filterOn: [],
        fields: [
          {
            key: 'select',
            label: 'Select All',
            sortable: true
          },
     
          {
            key: 'oid',
            label: 'Order ID',
            sortable: true
          },
          {
            key: 'date_created_gmt',
            label: 'Date',
            sortable: true
          },
          {
            key: 'first_name',
            label: 'First Name',
            sortable: true
          },
          {
            key: 'last_name',
            label: 'Last Name',
            sortable: true
          },
          {
            key: 'phone',
            label: 'Contact',
            sortable: true
          },
          {
            key: 'email',
            label: 'Email',
            sortable: true
          },
          // {
          //   key: 'payment_method',
          //   label: 'Payment',
          //   sortable: true
          // },
          {
            key: 'status',
            label: 'Status',
            sortable: true
          },

          {
            key: 'action',
            label: 'Action',
            sortable: true
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
    async selectedAll() {
      if (this.selectallcheckbox) {
        this.allSelected = [];
      } else {
        const selected = this.items.map((u) => u.oid);
        this.allSelected = selected;
      }
    },
     confirmstatus(){
      if(this.allSelected != ''){
        this.addstatus();
      }else{
        alert("Please choose at least one value from checkbox...")
      }
     },
     confirmstatusOID(oid) {
        this.oid = oid;
        this.allSelected = false;
        this.addstatus();
      },
      addstatus(){
        const modalTimeoutSeconds = 3;
      const modalId = 'confirm-modal'
      let modalSetTimeout = null;      
      this.$bvModal.msgBoxConfirm(`Are You Sure Want to Change Confirm Status`, {
        id: modalId
      })
      .then(wasOkPressed => {
        if(wasOkPressed) {
          this.vid = JSON.parse(localStorage.getItem("ivid"));
          let formData = new FormData();
          formData.append("oid",this.oid);
          formData.append("status_assign",'confirmed');
          formData.append("allSelected",this.allSelected);
          formData.append("vid",this.vid);
          formData.append("status",this.status);
          order.changeProcessingStatus(formData)
              .then((response) => {
                  alert('Status Update Successfully');
                this.getVidz();
              })
              .catch((error) => {
                  // console.log(error);
                  if (error.response.status == 422) {
                      this.errors_create = error.response.data.errors;
                  }
                  // loader.hide();
              });
        } else {
          /* Do something else */
        }
      })
      .catch(() => {
        console.log('The modal closed unexpectedly')
      })
      .finally(() => {
        clearTimeout(modalSetTimeout)
      })
      
      modalSetTimeout = setTimeout(() => {
        this.$bvModal.hide(modalId)
      }, modalTimeoutSeconds * 2000)
    },
      cancelstatusOID(oid) {
        this.oid = oid;
        this.allSelected = false;
        this.cancelstatus();
      },
     cancelstatus(){
        const modalTimeoutSeconds = 3;
      const modalId = 'confirm-modal'
      let modalSetTimeout = null;      
      this.$bvModal.msgBoxConfirm(`Are You Sure Want to Change Cancel Status`, {
        id: modalId
      })
      .then(wasOkPressed => {
        if(wasOkPressed) {
          this.vid = JSON.parse(localStorage.getItem("ivid"));
          let formData = new FormData();
          formData.append("oid",this.oid);
          formData.append("status_assign",'cancelled');
          formData.append("allSelected",this.allSelected);
          formData.append("vid",this.vid);
          formData.append("status",this.status);
          order.changeProcessingStatus(formData)
              .then((response) => {
                  alert('Status Update Successfully');
                this.getVidz();
              })
              .catch((error) => {
                  // console.log(error);
                  if (error.response.status == 422) {
                      this.errors_create = error.response.data.errors;
                  }
                  // loader.hide();
              });
        } else {
          /* Do something else */
        }
      })
      .catch(() => {
        console.log('The modal closed unexpectedly')
      })
      .finally(() => {
        clearTimeout(modalSetTimeout)
      })
      
      modalSetTimeout = setTimeout(() => {
        this.$bvModal.hide(modalId)
      }, modalTimeoutSeconds * 2000)
    },
    getvendor()
    {
      let formData = new FormData();
      formData.append('vendor', this.vendor);
      formData.append('type', 'get');
      console.log(this.vendor);
      order.getProcessing_OrderDetails(formData)
        .then(( response ) => {
          console.log(response);
          this.items=response.data;
          // this.countrys = response.data.data;
          console.log(this.items);
          //this.seen = false;
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

  getVidz()
     {
      let formData= new FormData();
      formData.append("user_id", this.$userId);
      user.getVid(formData)
       .then(( response ) => {
          this.vid = response.data;
          localStorage.setItem("ivid", this.vid);
          this.getonHolddata(this.vid);
        })
        .catch(response => {
            this.successful = false;
            //alert('something went wrong');
        })

      // alert("aaa");
     },

    getonHolddata(vid) 
    {
       order.getProcessing_data(vid, 'on-hold')
          .then(( response ) => {
            if(response.data)
            {
              this.items=response.data;
            }
          })
          .catch(error => {
              console.log(error);
            });
          console.log("Pack - 3");
    },

    Pendingassign_status() 
    {
        this.vid = JSON.parse(localStorage.getItem("ivid"));
         let formData = new FormData();
        formData.append("allSelected",this.allSelected);
        formData.append("status",this.status);
        formData.append("vid",this.vid);
        order.PendingRefund_changeStatus(formData)
          .then((response) => {
               alert(response.data.msg);
              this.getVidz();
             })
                    .catch(response => {
            this.successful = false;
            //alert('something went wrong');
          })
     },
          
    // check(event)
    // {
    //   alert(this.allSelected);
    //              // this.oid += oid;
    //              // let formData= new FormData();
    //              //  formData.append("oid", this.oid);
    //              //this.$bvModal.show("modal-1");
    //              // console.log(this.oid);
    // },
clearData()
    {
      this.oid ='';
    },

        ProcessingList_download()
      {
            let formData = new FormData();
            formData.append("allSelected",this.allSelected)
            formData.append("vid",this.vid)
            order.OnHold_downloadsheet(formData)
             .then((response) => {
              console.log(response.data[0]);
                  this.items2=response.data[0];
                  const data = XLSX.utils.json_to_sheet(this.items2)
                const wb = XLSX.utils.book_new()
                XLSX.utils.book_append_sheet(wb, data, 'data')
                XLSX.writeFile(wb,'OnHoldList_Downloads.xlsx')
          })
          .catch((error) => {
              console.log(error);
              if (error.response.status == 422) {
                  this.errors_create = error.response.data.errors;
              }
              // loader.hide();
          });
      }, 

  },
};  
</script>


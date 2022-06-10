<template>
    <b-container fluid>
        <div class="header_title">
            <div class="header_inner">
                <h2><strong>Ready to Pack Order List</strong></h2>
            </div>
        </div>
        <div class="clear">&nbsp;</div>
        <b-row>
            <b-col xl="12" lg="12" md="12">
                <div class="list-appointments content_bar ">
                    <div class="card-body card">
                        <b-row>
                            <b-col>
                                <button type="button" class="download-btn btn btn-primary" v-on:click="download">Pickup List Download</button>
                            </b-col>
                            <b-col>
                                <button type="button" class="download-btn btn btn-primary" v-on:click="selectdownload">Selected Download</button>
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
                                <span class="text-info"><input type="checkbox" @click="selectedAll" v-model="allSelected"> {{ data.label }}</span>
                              </template>
                              <template #empty="scope">
                                <p style="text-align: center;">No record found, choose date filter to found the result.</p>
                              </template>
                               <template v-slot:cell(select)="row">
                                <input type="checkbox" :value="row.item.OrderID" v-model="selectedval" >                               
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
    },
    data() 
    {
      return {
        vendor:null,
        status_assign:0,
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
        selectedval: [],
        selectdata: '',
      seen: false,
      allSelected: false,
        date_from: '',
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
            key: 'select',
            label: 'Select All',
            sortable: true
          },

            {
            key: 'OrderID',
            label: 'Order ID',
            sortable: true
          },

            {
            key: 'Date',
            label: 'Date',
            sortable: true
          },

            {
            key: 'SKU',
            label: 'Item SKU',
            sortable: true
          },

            {
            key: 'Name',
            label: 'Item Name',
            sortable: true
          },

            {
            key: 'Qty',
            label: 'Quantity',
            sortable: true
          },         
        ],
        items: [],
        items2: [],
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
      if (this.allSelected) {
        this.selectall = [];
      } else {
        const selected = this.items.map((u) => u.oid);
        this.selectall = selected;
      }
    },
    getvendor()
    {
      let formData = new FormData();
      formData.append('vendor', this.vendor);
      formData.append('type', 'get');
      console.log(this.vendor);
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

  getVidz()
     {
      let formData= new FormData();
      formData.append("user_id", this.$userId);
      user.getVid(formData)
       .then(( response ) => {
          this.vid = response.data;
          localStorage.setItem("ivid", this.vid);
          this.getPackdetail(this.vid);
        })
        .catch(response => {
            this.successful = false;
            alert('something went wrong');
        })

      // alert("aaa");
     },

    getPackdetail(vid) {
      order.getPackDetail(vid)
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
    addstatus(oid){
        let formData = new FormData();
        formData.append("oid",oid);
      order.changeStatus(formData)
          .then((response) => {
                  this.status_assign_array=response.data.data;
                  this.$bvModal.show("modal-1");
               //}
          })
          .catch((error) => {
              // console.log(error);
              if (error.response.status == 422) {
                  this.errors_create = error.response.data.errors;
              }
              // loader.hide();
          });
    },
   assign_status() 
    {
      let formData = new FormData();
        formData.append("id", this.id);
        formData.append("status_assign", this.status_assign);
        order
         .assignStatus(formData)
          .then((response) => {
                  this.$bvModal.hide("modal-1");
                  //this.appointment();
              // }
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
                XLSX.writeFile(wb,'readytopack_orders.xlsx')
            },

 
  selectdownload()
  {
    alert(this.selectedval);
            let formData = new FormData();
            formData.append("selectedval",this.selectedval)
            formData.append("vid",this.vid)
            order.downloadsheet(formData)
             .then((response) => {
                  this.items2=response.data;
                  const data = XLSX.utils.json_to_sheet(this.items2)
                const wb = XLSX.utils.book_new()
                XLSX.utils.book_append_sheet(wb, data, 'data')
                XLSX.writeFile(wb,'readytopack_orders.xlsx')
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


<template>
  <b-container fluid> 
    <div class="header_title">
    <div class="header_inner">
        <h3><strong>Confirmed Orders</strong></h3><br/>
      </div>
    </div>
    
    <div class="content_bar card">
        <div class="card-body">
          <div class="call-center-dashboard">
          <b-row>
            <b-col xl="9" lg="9" md="9">
            
            </b-col>
            <b-col>
                <button type="button" class="download-btn btn btn-primary" v-on:click="assignAWB">Assign AWB</button>
            </b-col>
          </b-row>
         <div class="blue-bar"></div>
        <div class="content_bar card list-appointments space-bottom">
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
                    placeholder="Type to Search"
                  ></b-form-input>
                  <b-input-group-append>
                    <b-button :disabled="!filter" @click="filter = ''">Clear</b-button>
                  </b-input-group-append>
              </b-col>
            </b-row>
          </div>
          
        </div>
        </div>
         <b-table striped hover responsive :items="items"
      :sort-by.sync="sortBy"
      :sort-desc.sync="sortDesc"
      sort-icon-left :filter-included-fields="filterOn" :filter="filter" :fields="fields" :per-page="perPage" :current-page="currentPage" show-empty>
            <template #empty="scope">
                <p style="text-align:center;">No record found, choose date filter to found the result.</p>
            </template>
            <template v-slot:cell(oid)="row">
              #{{(row.item.oid)}}
            </template>
          <template v-slot:cell(sr)="row">
            {{((currentPage-1)*perPage)+(row.index)+1}}
          </template>
            <template v-slot:cell(action)="row">
                <b-link @click="assignAWBOrder(row.item.oid)"><b-icon icon="key" aria-hidden="true" data-toggle="tooltip" title="Assign AWB Number"></b-icon></b-link>
            </template>

               <b-modal id="modal-1" title="Change Status:" hide-footer  size="lg">
      <b-form>
        <b-alert show variant="danger" v-if='create_error'>{{create_error}}</b-alert>
        <div :class="['form-group m-1 p-3', (successful ? 'alert-success' : '')]" v-show="successful">
          <span v-if="successful" class="label label-sucess">Published!</span>
        </div>
        <b-form-input v-model="oid" id="oid_val" style="display:none"></b-form-input>
        <b-form-select v-model="status_assign" class="" :options="status_assign_array" value-field="id" text-field="vehicle_number">
          <template v-slot:first>
            <b-form-select-option :value="0" disabled>-- Select Status --</b-form-select-option>
          </template>
          

        </b-form-select>
        
        <b-button type="submit" @click.prevent="s" variant="primary">Submit </b-button>
      </b-form> 
    </b-modal>



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
</b-container>
</template>

<script>
    import order from '../../api/order.js';
    import user from '../../api/user.js';
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
      seen: false,
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
            key: 'sr',
            sortable: true
          },

            {
            key: 'oid',
            label: 'Order ID',
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
            key: 'payment_method_title',
            label: 'Payment Method',
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
          order.getOrderOnStatus(this.vid,"confirmed")
            .then(( response ) => {
              if(response.data)
              {
                this.items=response.data;
              }
            })
            .catch(error => {
                console.log(error);
                
            });
        })
        .catch(response => {
            this.successful = false;
            alert('something went wrong');
        })
     },

      assignAWB(){
        // alert('Assigned Successfully');
        this.vid = JSON.parse(localStorage.getItem("ivid"));
        let formData= new FormData();
      formData.append("vid", this.vid);
        order.assignAWB(formData)
       .then(( response ) => {
          alert(response.data.msg);

        })
        .catch(response => {
            this.successful = false;
            alert('something went wrong');
        })
      },
      assignAWBOrder(oid){
        // alert('Assigned Successfully');
        this.vid = JSON.parse(localStorage.getItem("ivid"));
        let formData= new FormData();
        formData.append("vid", this.vid);
        formData.append("oid", oid);
          order.assignAWBOrder(formData)
         .then(( response ) => {
            alert(response.data.msg);
          })
          .catch(response => {
              this.successful = false;
              alert('something went wrong');
          })
      }
  },





clearData()
    {
      this.oid ='';
    },

};  
</script>

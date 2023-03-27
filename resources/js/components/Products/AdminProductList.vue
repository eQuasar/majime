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
          <h3><strong>Product List</strong></h3><br/>
        </div>
      </div>
    
      <div class="content_bar">
          <div class="card-body card">
            <div class="call-center-dashboard">
              <b-row>
                <b-col xl="10" lg="10" md="10">
                  <b-alert show variant="danger" v-if="create_error">{{create_error}}</b-alert>
                  <b-form @submit="onSubmit" class="date_range">
                    
                    <div class="datepiker-block">
                      <span>From:&nbsp;</span>  <b-form-datepicker  id="from" v-model="date_from" :date-format-options="{ year: 'numeric', month: '2-digit', day: '2-digit' }"  locale="en-IN"></b-form-datepicker>
                    </div>
                    <div class="datepiker-block">
                      <span>To:&nbsp;</span> <b-form-datepicker  id="to" v-model="date_to" :date-format-options="{ year: 'numeric', month: '2-digit', day: '2-digit' }"  locale="en-IN"></b-form-datepicker>
                    </div>
                    <b-button type="submit" variant="primary">Submit</b-button>
                  </b-form>
                </b-col>
                <b-col>
                  <button type="button" class="download-btn btn btn-primary" v-on:click="download">Download</button>
                </b-col>
              </b-row>
              <div class="blue-bar"></div>
              <div class="content_bar card list-appointments space-bottom">
                <div class="col-sm-12">
                  <b-row>
                    <b-col xl="5" lg="5" md="5">
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
                    <b-col xl="7" lg="7" md="7" class="search_field">
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
            </div>
            <br>
            <b-table striped hover responsive :items="items"
        :sort-by.sync="sortBy"
        :sort-desc.sync="sortDesc"
        sort-icon-left :filter-included-fields="filterOn" :filter="filter" :fields="fields" :per-page="perPage" :current-page="currentPage" show-empty>
              <template #empty="scope">
                  <p style="text-align:center;">No record found, choose date filter to found the result.</p>
              </template>
              <template v-slot:cell(oid)="row">
                {{(row.item.order_id)}}
              </template>
              <template v-slot:cell(categories)="row">
                cat
              </template>

              <template v-slot:cell(size)="row">
                <span v-for="(code, index) in (row.item.name.split('-'))">
                  <span v-for="(c, index2) in (code.split(','))" v-if="index == 1">
                    <span v-if="index2 == 1">{{c}}</span>
                  </span>
                </span>
              </template>
              <template v-slot:cell(color)="row">
                <span v-for="(code, index) in (row.item.name.split('-'))">
                  <span v-for="(c, index2) in (code.split(','))" v-if="index == 1">
                    <span v-if="index2 == 0">{{c}}</span>
                  </span>
                </span>
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
</b-container>
</template>

<script>
    import order from '../../api/order.js';
    import vendors from '../../api/vendors.js';
    import product_list from '../../api/Product.js';
    import user from '../../api/user.js';
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
        ariaDescribedby: "",
        time: "",
        date: "",
        time_slots: [],
      seen: false,
        date_from: '',
        vid: 0,
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
            key: 'product_id',
            label: 'Product Id',
            sortable: true
          },
          {
            key: 'variation_id',
            label: 'Variation Id',
            sortable: true
          },

          {
            key: 'name',
            label: 'Name',
            sortable: true
          },
          {
            key: 'quantity',
            label: 'Quantity',
            sortable: true
          },
          {
            key: 'categories',
            label: 'Category',
            sortable: true
          },
          {
            key: 'size',
            label: 'Size',
            sortable: true
          },
          {
            key: 'color',
            label: 'Color',
            sortable: true
          },
          {
            key: 'price',
            label: 'Amount',
            sortable: true
          },
        
        
        
        ],
        pro_cat:'',
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
  methods:{
    vendor_wise_detail(){
      console.log(this.vendor);
      window.location.assign("https://majime.nmf.im/admin/ProductList?vid="+this.vendor);
        // this.$router.push({name: 'adminlistOrders', params: {vid: this.vendor}})
    },
    getcat(pro_id){
      return this.pro_cat = "sss";
    },
    onSubmit(event) {
      event.preventDefault()
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
      
      appointment
          .appointmentSearch(formData)
          .then((response) => {
              
                  this.items=response.data.data;
           
          })
          .catch((error) => {
              console.log(error);
              if (error.response.status == 422) {
                  this.errors_create = error.response.data.errors;
              }
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
      this.ProductDetail(this.$route.query.vid);
     },

    ProductDetail(vid) {
      let formData= new FormData();
      formData.append("vid", vid);
      product_list.getProductDetail(formData)
       .then(( response ) => {
          console.log(response);
          this.items=response.data;
          console.log(this.items);
          
        })
        .catch(response => {
            this.successful = false;
            alert('something went wrong');
        })
    },
        download : function() {
                const data = XLSX.utils.json_to_sheet(this.items)
                const wb = XLSX.utils.book_new()
                XLSX.utils.book_append_sheet(wb, data, 'data')
                XLSX.writeFile(wb,'download_list.xlsx')
            },
  }
};  
</script>

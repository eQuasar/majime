<template>
  <b-container fluid> 
    <div class="header_title">
      <div class="header_inner">
        <h3><strong>Product List</strong></h3><br/>
      </div>
    </div>
    <div class="content_bar">
      <div class="select-list">
          <b-row>
            <b-col xl="3" lg="3" md="3">
                        <select class='form-control custom-select' v-model='name' :options="allproductdata" @change='onChangeproduct($event)'>
                             <option disabled value="null">Select Product</option>
                            <option v-for='data in allproductdata' :value='data.name'>{{data.name}}</option>
                        </select>
               </b-col>
              <b-col xl="3" lg="3" md="3">
                        <select class='form-control custom-select' v-model='size' :options="allsizedata" @change='onChangeproduct($event)'>
                            <option value='null' disabled>Select Size</option>
                            <option v-for='data in size' :value='data.size'>{{size}}</option>
                        </select>
               </b-col>
                <b-col xl="3" lg="3" md="3">
                        <select class='form-control custom-select' v-model='color' :options="allcolordata" @change='onChangecolor($event)'>
                            <option value='null' disabled>Select Color</option>
                            <option v-for='data in color' :value='data.color'>{{color}}</option>
                        </select>
               </b-col>
          </b-row>
      </div>
      </br>
        <div class="card-body card">
          <div class="call-center-dashboard">
            <b-row>
              <b-col xl="8" lg="8" md="8">
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
              <b-col xl="4" lg="4" md="4" class="search_field">
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
                  <b-col>
                    <button type="button" class="download-btn btn btn-primary" v-on:click="download">Download</button>
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
            <template #head(select)="data">
              <span class="text-info"><input type="checkbox" @click="selectedAll" v-model="allSelected">&nbsp;{{ data.label }}</span>
            </template>
            <template v-slot:cell(action)="row">
               <p class="h3 mb-2">   <router-link :to="{ name: 'productprofile', params: { variation_id:(row.item.variation_id).toString() }}"><b-icon icon="eye-fill" aria-hidden="true"></b-icon></router-link></p>
              </template>
            <template #empty="scope">
                <p style="text-align:center;">No record found, choose date filter to found the result.</p>
            </template>
            <template v-slot:cell(oid)="row">
              {{(row.item.order_id)}}
            </template>
            <template v-slot:cell(categories)="row">
              {{(row.item.categories)}}
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
            <template v-slot:cell(select)="row">
            	<input type="checkbox" :value="row.item.oid" v-model="selectall">
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
</b-container>
</template>

<script>
    import order from '../../api/order.js';
    import product from '../../api/Product.js';
    import product_list from '../../api/Product.js';
    import user from '../../api/user.js';
    import * as XLSX from 'xlsx/xlsx.mjs';

  export default {

    props: {
    },
    mounted() {
      this.getVidz();
      this.getProduct();
      this.getStatus();
      // this.getSize();
    },
    data() 
    {
      return {
        ariaDescribedby: "",
        time: "",
        date: "",
        color: "",
        time_slots: [],
        allproductdata:[],
        allstatusdata:[],
        allsizedata:[],
        seen: false,
        date_from: '',
        allSelected: false,
        vid: 0,
        size:"",
        date_to: '',
        color: null,
        name: null,
        size: null,
        product: null,
        variation_id:"",
        sortBy: 'date',
        sortDesc: true,
        perPage: 10,
        currentPage: 1,
        pageOptions: [5, 10, 15, 20, 50, 100],
        selectall:[],
        filter: null,
        filterOn: [],
        fields: [
        	{
        		key: 'select',
        		label:'Select All',
        		sortable :true
        	},
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
            key: 'action',
            label: 'Action',
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
  	 async selectedAll() {
      if (this.allSelected) {
        this.selectall = [];
      } else {
        const selected = this.items.map((u) => u.oid);
        this.selectall = selected;
      }
    },
        getsize(){
          product.getsize()
            .then((response) => {
                this.allsizedata=response.data;
              })
                  .catch((error) => {
              if (error.response.status == 422) {
                this.errors_create = error.response.data.errors;
                }
                });
      },


    getProduct(){
          product.getProduct()
            .then((response) => {
                this.allproductdata=response.data;
              })
                  .catch((error) => {
              if (error.response.status == 422) {
                this.errors_create = error.response.data.errors;
                }
                });
      },
       getStatus(){
          product.getStatus()
            .then((response) => {
                this.allstatusdata=response.data;
                  
              })
                  .catch((error) => {
              if (error.response.status == 422) {
                this.errors_create = error.response.data.errors;
                }
                });
      },
    onChangeproduct(event) 
         {
           this.vid = JSON.parse(localStorage.getItem("ivid"));
          console.log(event.target.value);
          let formData = new FormData();
          formData.append('vid', this.vid);
          formData.append('name', this.name);
          product.productSearch(formData)
              .then((response) => 
                   {
                  this.items=response.data;
                  console.log(this.items);
                    })
          .catch((error) => 
                  {
              console.log(error);
              if (error.response.name == 422) {
                  this.errors_create = error.response.data.errors;
                  }
                  });
          },
         //  onChangeproduct(event) 
         // {
         //  console.log(event.target.value);
         //  let formData = new FormData();
         //  formData.append("product", this.product);
         //  order.productSearch(formData)
         //      .then((response) => 
         //           {
         //          this.items=response.data;
         //          console.log(this.items);
         //            })
         //  .catch((error) => 
         //          {
         //      console.log(error);
         //      if (error.response.product == 422) {
         //          this.errors_create = error.response.data.errors;
         //          }
         //          });
         //  },
          onChangecolor(event) 
         {
          console.log(event.target.value);
          let formData = new FormData();
          formData.append("color", this.color);
          order.colorSearch(formData)
              .then((response) => 
                   {
                  this.items=response.data;
                  console.log(this.items);
                    })
          .catch((error) => 
                  {
              console.log(error);
              if (error.response.color == 422) {
                  this.errors_create = error.response.data.errors;
                  }
                  });
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
    getVidz()
     {
      let formData= new FormData();
      formData.append("user_id", this.$userId);
      user.getVid(formData)
       .then(( response ) => {
          this.vid = response.data;
          localStorage.setItem("ivid", this.vid);
          this.ProductDetail(this.vid);
        })
        .catch(response => {
            this.successful = false;
            alert('something went wrong');
        })

      // alert("aaa");
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

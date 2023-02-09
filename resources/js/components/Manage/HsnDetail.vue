<template>
    <b-container fluid> 
     
      <div>
        <div class="card-body">
          <!-- <b-button pill variant="info" style="float: right" @click="goToCreate()"
          >Go Back</b-button> -->
          <h3><strong>HSN Detail Details</strong></h3>
          <br/>
          <b-form>
              <b-alert show variant="danger" v-if='create_error'>{{create_error}}</b-alert>
              <div :class="['form-group m-1 p-3', (successful ? 'alert-success' : '')]" v-show="successful">
                <span v-if="successful" class="label label-sucess">Transaction Detail Enter Sucessfully</span>
              </div>
              <b-overlay :show="show" rounded="sm" class="transaction_details">
                <b-row style="margin-bottom: 10px;">
                  <b-col xl="6" lg="6" md="6">
                       <!-- <b-form-group id="input-group-description" label="Description" label-for="input-name">
                    <b-form-select v-model="description" :options="descriptions" label="Description" label-for="input-city"></b-form-select>
                       </b-form-group>
                  </b-form-select>
       
                  <b-form-group label="Type of Transaction" v-slot="{ ariaDescribedby }">
                    <b-form-radio v-model="tranType" :aria-describedby="ariaDescribedby"  name="tranType" value="In"><span class="in">In</span></b-form-radio>
                    <b-form-radio v-model="tranType" :aria-describedby="ariaDescribedby" name="tranType" value="Out"><span claa="out">Out</span></b-form-radio>
                 </b-form-group>
            
               <b-form-group id="input-group-Vendor" label="Choose Vendor" label-for="input-Vendor">
                  <b-form-select v-model="vendor" class="" :options="allvendors" value-field="id" text-field="name" >
                        <template v-slot:first>
                            <b-form-select-option :value="0" disabled>-- Select Vendor --</b-form-select-option>
                        </template>
                  </b-form-select>
              </b-form-group>  -->
                  <b-form-group id="input-group-hsn" label="HSN Code" label-for="input-hsn">
                    <b-form-input id="input-amount"  v-model="hsn" type="text" required placeholder="Enter HSN Code"></b-form-input>
                    </b-form-group>
                    <b-form-group id="input-group-slab1" label="Slab1" label-for="input-slab1">
                    <b-form-input id="input-amount"  v-model="slab1" type="text" required placeholder="Enter Value of Slab 1"></b-form-input>
                    </b-form-group>
                    <b-form-group id="input-group-slab2" label="Slab2" label-for="input-slab2">
                    <b-form-input id="input-amount"  v-model="slab2" type="text" required placeholder="Enter Value of Slab 2"></b-form-input>
                    </b-form-group>
                    <b-form-group id="input-group-amount" label="Enter Slab amount" label-for="input-amount">
                    <b-form-input id="input-amount"  v-model="slab_amount" type="text" required placeholder="Enter Slab Amount"></b-form-input>
                    </b-form-group>
                    <b-form-group id="input-group-amount" label="Description" label-for="input-description">
                    <b-form-input id="input-amount" v-model="description" type="text" required placeholder="Enter Description"></b-form-input>
                    </b-form-group>
                      </b-form-group>  
                  <b-button type="submit" @click.prevent="create" variant="primary">Submit </b-button>
                </b-col>       
               </b-row>
              </b-overlay>
            </b-form>
        </div>
      </div>
    </b-container>
  </template>
  
  <script>
    import HSN from '../../api/hsn.js';
    import vendors from '../../api/vendors.js';
    import user from '../../api/user.js';
    // import apidata from '../../api/json.js';
    // import vendor from '../../api/vendors.js';
    export default {
      props: {
      },
      mounted() {
      },
      data() 
      {
        return {
          show: false,
           ariaDescribedby: "",
          name: "",
          vid:0,
          status:"",
          city: "",
          date:"",
          pin:"",
          allvendors: [],
          amount:"",
          description:"",
          tranType:"",
          vendor:0,
          country:"",
          hsn:"",
          slab1:"",
          slab2:"",
          slab_amount:"",
          description:"",
          phone:"",
          add:"",
          token:"",
          type:"",
          role_id:1,
          seen: false,
          sortBy: 'date',
          sortDesc: true,
          perPage: 10,
          awb_id:0,
          currentPage: 1,
          pageOptions: [5, 10, 15, 20, 50, 100],
          filter: null,
           description: null,
           descriptions: [
            { value: null, text: 'Please select an option'},
            { value: 'Setup Charges',  text: 'Setup Charges' },
            { value: 'Design Charges', text: 'Design Charges' },
            { value: 'PhotoShot Charges', text: 'Photo Shoot Charges'},
            { value: 'SMS Charges', text: 'SMS Charges' },
            { value: 'Digital Ads', text: 'Digital Ads' },
            { value: 'Bank Transfer', text: 'Bank Transfer' },
            { value: 'Cash', text: 'Cash' },
            { value: 'Debit Note', text: 'Debit Note' },
            { value: 'Credit Note', text: 'Credit Note' },
          ],
           
             
          fields: [
            {
              key: 'id',
              label: 'id',
              sortable: true
            },
            {
              key: 'description',
              label: 'Description',
              sortable: true
            },
             {
              key: 'type',
              label: 'Type',
              sortable: true
            },
             {
              key: 'amount',
              label: 'Amount',
              sortable: true
            },
            {
              key: 'date',
              label: 'date',
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
  
      //     vendor_wise_detail(){
      //   console.log(this.vendor);
      //   window.location.assign("https://majime.nmf.im/admin/TransactionDetail?vid="+this.vendor);
      //     // this.$router.push({name: 'adminlistOrders', params: {vid: this.vendor}})
      // },
  
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
          if (!this.hsn) {
            this.create_error += "Enter HSN Code,";
          }
          if (!this.slab1) {
            this.create_error += "Enter Value of Slab 1,";
          }
          if (!this.slab2) {
            this.create_error += "Enter Value of Slab 2,";
          }
          if (!this.slab_amount) {
            this.create_error += "Enter Slab Amount,";
          }
          if (!this.description) {
            this.create_error += "Enter description,";
          }
          if (this.create_error != "") {
            return false;
          }
          // this.vid = JSON.parse(localStorage.getItem("ivid"));
          let formData = new FormData();
          formData.append("hsn", this.hsn);
          formData.append("slab1", this.slab1); 
          formData.append("slab2", this.slab2);
          formData.append("slab_amount", this.slab_amount);
          formData.append("description", this.description);
          HSN.hsn_detail(formData)
            .then((response) => {
              this.successful = true;
              this.error = true;
              this.$router.push({name: 'hsn_detail'});
              // this.$router.push("/admin/HSNDetail");
            })
            .catch((error) => {
                console.log(error);
                if (error.response.status == 422) {
                    this.errors_create = error.response.data.errors;
                }
            });
        },
    //    goToCreate() {
    //   this.$router.push("/admin/HSNDetail");
    // },
   },
    };  
  </script>
  
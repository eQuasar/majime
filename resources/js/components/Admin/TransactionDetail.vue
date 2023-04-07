<template>
  <b-container fluid> 
   
    <div>
      <div class="card-body">
        <h3><strong>Transaction Details</strong></h3>
        <br/>
        <b-form>
            <b-alert show variant="danger" v-if='create_error'>{{create_error}}</b-alert>
            <div :class="['form-group m-1 p-3', (successful ? 'alert-success' : '')]" v-show="successful">
              <span v-if="successful" class="label label-sucess">Transaction Detail Enter Sucessfully</span>
            </div>
            <b-overlay :show="show" rounded="sm" class="transaction_details">
              <b-row style="margin-bottom: 10px;">
                <b-col xl="6" lg="6" md="6">
                     <b-form-group id="input-group-description" label="Description" label-for="input-name">
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
            </b-form-group> 



            
                <b-form-group id="input-group-amount" label="Amount" label-for="input-amount">
                  <b-form-input id="input-amount" :value="this.amount" v-model="amount" type="text" required placeholder="Enter Amount"></b-form-input>
                  </b-form-group>

             

                  <label for="input-date">Choose a date</label>
                 <b-form-datepicker id="input-date" :value="this.date" v-model="date" class="mb-2"></b-form-datepicker>

                 <!--  //<b-form-input id="input-name" :value="this.date" v-model="date" type="text" required placeholder="Enter Name"></b-form-input> -->
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
  import TransactionDetail from '../../api/TransactionDetail.js';
  import vendors from '../../api/vendors.js';
  import user from '../../api/user.js';
  // import apidata from '../../api/json.js';
  // import vendor from '../../api/vendors.js';
  export default {
    props: {
    },
    mounted() {
      if(this.$route.query.vid){
        this.getVendor();
      }else{
        this.getVendor();
      }
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
          { value: 'Customer Bank Refund', text: 'Customer Bank Refund' },
          { value: 'Server Charges', text: 'Server Charges' },
          { value: 'CDC Charges', text: 'CDC Charges' },
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
        if (!this.description) {
          this.create_error += "Select Description,";
        }
        if (!this.tranType) {
          this.create_error += "Select Type,";
        }
        if (!this.amount) {
          this.create_error += "Enter Amount ,";
        }
        if (!this.date) {
          this.create_error += "Select Date,";
        }
        if (this.create_error != "") {
          return false;
        }
        // this.vid = JSON.parse(localStorage.getItem("ivid"));
        let formData = new FormData();
        formData.append("description", this.description);
        formData.append("vid", this.vendor); 
        formData.append("type", this.tranType);
        formData.append("amount", this.amount);
        formData.append("date", this.date);
        
        TransactionDetail.AddTrasaction(formData)
          .then((response) => {
            this.successful = true;
            this.error = true;
            //this.getAWBLocation();
            // this.items=response.data;
             // this.items.push (response.data);
             this.description='';
             this.tranType='';
             this.amount='';
              this.date='';
              this.vendor='';
            // this.country='';
            // this.phone='';
            // this.add='';
            // this.token='';
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

		
    }
  };  
</script>

<template>
  <b-container fluid> 
   
    <div>
      <div class="card-body">
        <h3><strong>Transaction Detail</strong></h3>
        <br/>
        <b-form>
            <b-alert show variant="danger" v-if='create_error'>{{create_error}}</b-alert>
            <div :class="['form-group m-1 p-3', (successful ? 'alert-success' : '')]" v-show="successful">
              <span v-if="successful" class="label label-sucess">Transaction Detail Enter Sucessfully</span>
            </div>
            <b-overlay :show="show" rounded="sm">
              <b-row style="margin-bottom: 10px;">
                <b-col xl="6" lg="6" md="6">
                     <b-form-group id="input-group-description" label="Description" label-for="input-name">
                  <b-form-select v-model="description" :options="descriptions" label="Description" label-for="input-city"></b-form-select>
            
                  </b-form-group>
                </b-form-select>
     
                <b-form-group label="Type of Transaction" v-slot="{ ariaDescribedby }">
                  <b-form-radio v-model="tranType" :aria-describedby="ariaDescribedby" name="tranType" value="In">In</b-form-radio>
                  <b-form-radio v-model="tranType" :aria-describedby="ariaDescribedby" name="tranType" value="Out">Out</b-form-radio>
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
  // import apidata from '../../api/json.js';
  // import vendor from '../../api/vendors.js';
  export default {
    props: {
    },
    mounted() {
      //this.AddTrasaction();
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
        amount:"",
        description:"",
        tranType:"",
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
          { value: null, text: 'Please select an option' },
          { value: 'Setup Charges', text: 'Setup Charges' },
          { value: 'Design Charges', text: 'Design Charges' },
          { value: 'PhotoShot Charges', text: 'Photo Shoot Charges'},
          { value: 'SMS Charges', text: 'SMS Charges' },
          { value: 'Digital Ads', text: 'Digital Ads' },
          { value: 'Recharge', text: 'Recharge' },
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
        this.vid = JSON.parse(localStorage.getItem("ivid"));
        let formData = new FormData();
        formData.append("description", this.description);
        formData.append("type", this.tranType);
        formData.append("amount", this.amount);
        formData.append("date", this.date);
        
        TransactionDetail.AddTrasaction(formData)
          .then((response) => {
            this.successful = true;
            this.error = true;
            //this.getAWBLocation();
            this.items=response.data;
             this.items.push(response.data);
             this.description='';
             this.tranType='';
             this.amount='';
              this.date='';
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
      // update() {
      //   this.create_error = "";
      //   if (!this.city) {
      //     this.create_error += "Add city,";
      //   }
      //   if (!this.name) {
      //     this.create_error += "Add Name,";
      //   }
      //   if (!this.pin) {
      //     this.create_error += "Add Pincode,";
      //   }
      //   if (!this.country) {
      //     this.create_error += "Add Country,";
      //   }
      //   if (!this.phone) {
      //     this.create_error += "Add Phone,";
      //   }
      //   if (!this.add) {
      //     this.create_error += "Add Address,";
      //   }
      //   if (!this.token) {
      //     this.create_error += "Add Token,";
      //   }
      //   if (this.create_error != "") {
      //     return false;
      //   }
      //   this.vid = JSON.parse(localStorage.getItem("ivid"));
      //   let formData = new FormData();
      //   formData.append("user_id", this.$userId);
      //   formData.append("vid", this.vid);
      //   formData.append("city", this.city);
      //   formData.append("name", this.name);
      //   formData.append("pin", this.pin);
      //   formData.append("country", this.country);
      //   formData.append("phone", this.phone);
      //   formData.append("add", this.add);
      //   formData.append("token", this.token);
      //   formData.append("id", this.awb_id);
        
      //   awb.updateWayData(formData)
      //     .then((response) => {
      //       this.successful = true;
      //       this.error = true;
      //       this.getAWBLocation();
      //       //this.items=response.data;
      //       // this.items.push(response.data);
      //       // this.city='';
      //       // this.name='';
      //       // this.pin='';
      //       // this.country='';
      //       // this.phone='';
      //       // this.add='';
      //       // this.token='';
      //     })
      //     .catch((error) => {
      //         console.log(error);
      //         if (error.response.status == 422) {
      //             this.errors_create = error.response.data.errors;
      //         }
      //     });
      // },
      // import_data(id, url) {
      //   // console.log(id);
      //   // console.log(url);
      //   let formData = new FormData();
      //   formData.append("vid", id);
      //   formData.append("url", url);
        
      //   apidata
      //     .getJson(formData)
      //     .then((response) => {
      //         alert("Data imported successfully.");
      //     })
      //     .catch((error) => {
      //         console.log(error);
      //         if (error.response.status == 422) {
      //             this.errors_create = error.response.data.errors;
      //         }
      //     });
      // },
    //   getAWBLocation() {
    //     this.vid = JSON.parse(localStorage.getItem("ivid"));
    //     let formData = new FormData();
    //     formData.append("user_id", this.$userId);
    //     formData.append("vid", this.vid);
    //     awb.getAWBLocation(formData)
    //     .then(( response ) => {
    //       this.vid=response.data[0].vid;
    //       this.user_id=response.data[0].user_id;
    //       this.city=response.data[0].city;
    //       this.name=response.data[0].name;
    //       this.pin=response.data[0].pin;
    //       this.country=response.data[0].country;
    //       this.phone=response.data[0].phone;
    //       this.add=response.data[0].add;
    //       this.token=response.data[0].token;
    //       this.awb_id=response.data[0].id;
    //     })
    //     .catch(response => {
    //         this.successful = false;
    //         alert('something went wrong');
    //     })
  //   //   }
    }
  };  
</script>

<template>
  <b-container fluid>
    <div>
      <div class="card-body">
        <h3><strong>Zone Rate Card</strong></h3>
        <br />
        <b-form>
          <b-alert show variant="danger" v-if="create_error">{{
            create_error
          }}</b-alert>
          <div
            :class="['form-group m-1 p-3', successful ? 'alert-success' : '']"
            v-show="successful"
          >
            <span v-if="successful" class="label label-sucess"
              >Transaction Detail Enter Sucessfully</span
            >
          </div>
          <b-overlay :show="show" rounded="sm" class="transaction_details">
            <!-- <b-row style="margin-bottom: 10px">
              <b-col xl="6" lg="6" md="6">
                <b-form-group
                  id="input-group-Vendor"
                  label="Choose Vendor"
                  label-for="input-Vendor"
                  placeholder="Select Vendor"
                > -->
                  <!-- <b-form-select
                    v-model="vendor"
                    class=""
                    :options="allvendors"
                    value-field="id"
                    text-field="name"
                  >
                    <template v-slot:first>
                      <b-form-select-option :value="0" disabled
                        > Select Vendor </b-form-select-option
                      >
                    </template>
                  </b-form-select> -->
                  <!-- <b-form-input
                    id="input-fwd"
                    :value="this.vendordetail"
                    v-model="vendor"
                    type="text"
                    required
                  ></b-form-input> -->
                
              
           <!-- <span> {{this.vendordetail}}</span> -->
                <!-- </b-form-group> -->
                  
                     <b-form-group id="input-group-zone" label=" Choose Zone" label-for="input-zone" placeholder="Select Zone">
                  <b-form-select v-model="zone" :options="alloption" label="Zone " label-for="input-ZOne" v-on:change="showzonedetail"></b-form-select>
                     </b-form-group>
                </b-form-select>
              </b-form-group>
                  
                  <b-form-group id="input-group-zone" label=" Choose Courier Service" label-for="input-courier" placeholder="Select Courier Service">
               <b-form-select v-model="courier" :options="courierdetail" label="Zone " label-for="input-courier"></b-form-select>
                  </b-form-group>
             </b-form-select>
    

                <b-form-group
                  id="input-group-fwd"
                  label="Enter Value of FWD"
                  label-for="input-fwd"
                >
                  <b-form-input
                    id="input-fwd"
                    :value="this.fwd"
                    v-model="fwd"
                    type="text"
                    required
                    placeholder="Enter FWD"
                  ></b-form-input>
                </b-form-group>
                <b-form-group
                  id="input-group-dto"
                  label="Enter Value of DTO "
                  label-for="input-dto"
                >
                  <b-form-input
                    id="input-dto"
                    :value="this.dto"
                    v-model="dto"
                    type="text"
                    required
                    placeholder="Enter DTO"
                  ></b-form-input>
                </b-form-group>

                <b-form-group
                  id="input-group-rto"
                  label="Enter VAlue of RTO"
                  label-for="input-rto"
                >
                  <b-form-input
                    id="input-rto"
                    :value="this.rto"
                    v-model="rto"
                    type="text"
                    required
                    placeholder="Enter RTO"
                  ></b-form-input>
                </b-form-group>

                <b-button
                  type="submit"
                  @click.prevent="create"
                  variant="primary"
                  >Submit
                </b-button>
              </b-col>
            </b-row>
          </b-overlay>
        </b-form>
      </div>
    </div>
  </b-container>
</template>

<script>
import TransactionDetail from "../../api/TransactionDetail.js";
import vendors from "../../api/vendors.js";
import order from "../../api/order.js";
import master from "../../api/master.js";
import user from "../../api/user.js";
// import apidata from '../../api/json.js';
// import vendor from '../../api/vendors.js';
export default {
  props: {},
  mounted() {
    if (this.$route.query.vid) {
      this.getVendor();
      this.getZone();
      // this.getVidz();
    } else {
      this.getVendor();
      this.getZone();
    }
    // this.showzonedetail();
  },
  data() {
    return {
      show: false,
      ariaDescribedby: "",
      name: "",
      vid: 0,
      status: "",
      zone: "",
      zone: null,
      dto: "",
      rto: "",
      fwd: "",
      city: "",
      date: "",
      pin: "",
    
      allvendors: [],
      alloption:[],
      allzone: [],
      courierdetail:[],
      courier:"",
      amount: "",
      description: "",
      tranType: "",
      vendor: 0,
      country: "",
      phone: "",
      add: "",
      token: "",
      type: "",
      vendordetail:"",
      role_id: 1,
      seen: false,
      sortBy: "date",
      sortDesc: true,
      perPage: 10,
      awb_id: 0,
      currentPage: 1,
      pageOptions: [5, 10, 15, 20, 50, 100],
      filter: null,
      description: null,
      zone: null,
      courier:majime;
      descriptions: [
        { value: null, text: "Please select an option" },
        { value: "Setup Charges", text: "Setup Charges" },
        { value: "Design Charges", text: "Design Charges" },
        { value: "PhotoShot Charges", text: "Photo Shoot Charges" },
        { value: "SMS Charges", text: "SMS Charges" },
        { value: "Digital Ads", text: "Digital Ads" },
        { value: "Recharge", text: "Recharge" },
      ],
      alloption: [
        { value: null, text: "Please select Zone No" },
        { value: "A", text: "Zone A" },
        { value: "B", text: "Zone B" },
        { value: "C", text: "Zone C" },
        { value: "D", text: "Zone D" },
        { value: "E", text: "Zone E" },
        { value: "F", text: "Zone F" },
      ],
      courierdetail: [
        { value: null, text: "Please Select Courier Service" },
        { value: "0", text: "Self" },
        { value: "1", text: "Majime" },
      ],

      fields: [
        {
          key: "zone",
          label: "Zone No",
          sortable: true,
        },
        {
          key: "pincode",
          label: "PinCode",
          sortable: true,
        },
      ],
      items: [],
      errors_create: [],
      successful: false,
      create_error: "",
    };
  },
  computed: {
    rows() {
      return this.items.length;
    },
  },
  methods: {
    //     vendor_wise_detail(){
    //   console.log(this.vendor);
    //   window.location.assign("https://majime.nmf.im/admin/TransactionDetail?vid="+this.vendor);
    //     // this.$router.push({name: 'adminlistOrders', params: {vid: this.vendor}})
    // },

    isNumber: function (evt) {
      evt = evt ? evt : window.event;
      var charCode = evt.which ? evt.which : evt.keyCode;
      if (
        charCode > 31 &&
        (charCode < 48 || charCode > 57) &&
        charCode !== 46
      ) {
        evt.preventDefault();
      } else {
        return true;
      }
    },
    create() {
      this.create_error = "";
      // if (!this.vendor) {
      //   this.create_error += "Select Vendor,";
      // }
      if (!this.zone) {
        this.create_error += "Select Zone,";
      }
      if (!this.courier) {
        this.create_error += "Select Courier Serice,";
      }
      if (!this.fwd) {
        this.create_error += "Enter FWD ,";
      }
      if (!this.dto) {
        this.create_error += "Enter DTO,";
      }
      if (!this.rto) {
        this.create_error += "Enter RTO,";
      }
      if (this.create_error != "") {
        return false;
      }
      this.vid = JSON.parse(localStorage.getItem("ivid"));
      let formData = new FormData();

      formData.append("zone", this.zone);
      formData.append("courier", this.courier);
      formData.append("vendor", this.vid);
      formData.append("fwd", this.fwd);
      formData.append("dto", this.dto);
      formData.append("rto", this.rto);

      master
        .zoneratecard(formData)
        .then((response) => {
          this.successful = true;
          this.error = true;
          //this.getAWBLocation();
          // this.items=response.data;
          // this.items.push (response.data);
          this.vendor = "";
          this.zone = "";
          this.fwd = "";
          this.dto = "";
          this.rto = "";
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
    showzonedetail()
    {
      this.vid = JSON.parse(localStorage.getItem("ivid"));
      let formData = new FormData();
      formData.append("vendor", this.vid);
      formData.append("selectzone", this.zone);
      master
        .showzonedetail(formData)
          .then((response) => {
            if(response.data.msg){
              alert(response.data.msg);
            }
            else{
            this.fwd=response.data[0].fwd;
            this.dto=response.data[0].dto;
            this.rto=response.data[0].rto;
            }
        })
        .catch((error) => {
          console.log(error);
          if (error.response.status == 422) {
            this.errors_create = error.response.data.errors;
          }
        });



    },
    getVendor() {
      vendors
        .getVendors()
        .then((response) => {
          this.allvendors = response.data;
        })
        .catch((error) => {
          // console.log(error);
          if (error.response.status == 422) {
            this.errors_create = error.response.data.errors;
          }
          // loader.hide();
        });
    },

    getZone() {
      master
        .getzone()
        .then((response) => {
          this.allzone = response.data;
        })
        .catch((error) => {
          // console.log(error);
          if (error.response.status == 422) {
            this.errors_create = error.response.data.errors;
          }
          // loader.hide();
        });
    },
  },
};
</script>

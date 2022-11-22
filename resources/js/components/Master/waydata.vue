<template>
  <b-container fluid>
    <div>
      <div class="card-body">
        <h3><strong>Vendor Rate Card</strong></h3>
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
              >Vendor Rate Card Detail Enter Sucessfully</span
            >
          </div>
          <b-overlay :show="show" rounded="sm" class="transaction_details">
            <b-row style="margin-bottom: 10px">
              <b-col xl="6" lg="6" md="6">
                <b-form-group
                  id="input-group-Vendor"
                  label="Choose Vendor"
                  label-for="input-Vendor"
                >
                  <b-form-select
                    v-model="vendor"
                    class=""
                    :options="allvendors"
                    value-field="id"
                    text-field="name"
                  >
                    <template v-slot:first>
                      <b-form-select-option :value="0" disabled>
                        Select Vendor
                      </b-form-select-option>
                    </template>
                  </b-form-select>
                </b-form-group>

                <b-form-group
                  id="input-group-cod"
                  label="Enter Value of COD"
                  label-for="input-cod"
                >
                  <b-form-input
                    id="input-cod"
                    :value="this.cod"
                    v-model="cod"
                    type="text"
                    required
                    placeholder="Enter COD"
                  ></b-form-input>
                </b-form-group>
                <b-form-group
                  id="input-group-codpercent"
                  label="Enter Percentage of COD"
                  label-for="input-codpercent"
                >
                  <b-form-input
                    id="input-codper"
                    :value="this.codper"
                    v-model="codper"
                    type="text"
                    required
                    placeholder="COD Percentage "
                  ></b-form-input>
                </b-form-group>
                <b-form-group
                  id="input-group-above"
                  label="Enter Value When Weight is Above 500gm"
                  label-for="input-above"
                >
                  <b-form-input
                    id="input-above"
                    :value="this.above"
                    v-model="above"
                    type="text"
                    required
                    placeholder="After 500gm "
                  ></b-form-input>
                </b-form-group>
                <b-form-group
                  id="input-group-sms"
                  label="SMS Charges"
                  label-for="input-sms"
                >
                  <b-form-input
                    id="input-sms"
                    :value="this.sms"
                    v-model="sms"
                    type="text"
                    required
                    placeholder="SMS Charges"
                  ></b-form-input>
                </b-form-group>

                <b-form-group
                  id="input-group-mjm"
                  label="Enter Percentage of Majime Charges"
                  label-for="input-mjm"
                >
                  <b-form-input
                    id="input-sms"
                    :value="this.mjm_charges"
                    v-model="mjm_charges"
                    type="text"
                    required
                    placeholder=" Majime Charges"
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
import user from "../../api/user.js";
import master from "../../api/master.js";
// import apidata from '../../api/json.js';
// import vendor from '../../api/vendors.js';
export default {
  props: {},
  mounted() {
    if (this.$route.query.vid) {
      this.getVendor();
      this.getVidz();
    } else {
      this.getVendor();
    }
  },
  data() {
    return {
      show: false,
      ariaDescribedby: "",
      name: "",
      vid: 0,
      status: "",
      city: "",
      date: "",
      pin: "",
      allvendors: [],
      amount: "",
      cod: "",
      codper: "",
      mjm_charges: "",
      above: "",
      sms: "",
      description: "",
      tranType: "",
      vendor: 0,
      country: "",
      phone: "",
      add: "",
      token: "",
      type: "",
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
      descriptions: [
        { value: null, text: "Please select an option" },
        { value: "Setup Charges", text: "Setup Charges" },
        { value: "Design Charges", text: "Design Charges" },
        { value: "PhotoShot Charges", text: "Photo Shoot Charges" },
        { value: "SMS Charges", text: "SMS Charges" },
        { value: "Digital Ads", text: "Digital Ads" },
        { value: "Recharge", text: "Recharge" },
      ],

      fields: [
        {
          key: "id",
          label: "id",
          sortable: true,
        },
        {
          key: "cod",
          label: "COD",
          sortable: true,
        },
        {
          key: "codper",
          label: "COD Percentage",
          sortable: true,
        },
        {
          key: "above",
          label: "Above 500 gm",
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
      if (!this.cod) {
        this.create_error += "Enter Cod,";
      }
      if (!this.codper) {
        this.create_error += "Enter  COD Percentage,";
      }
      if (!this.above) {
        this.create_error += "Enter Above 500gm ,";
      }
      if (!this.vendor) {
        this.create_error += "Choose Vendor  ,";
      }
      if (!this.mjm_charges) {
        this.create_error += "Enter Majime Charges  ,";
      }
      if (!this.sms) {
        this.create_error += "Enter SMS Charges  ,";
      }
      if (this.create_error != "") {
        return false;
      }
      // this.vid = JSON.parse(localStorage.getItem("ivid"));
      let formData = new FormData();
      formData.append("cod", this.cod);
      formData.append("codper", this.codper);
      formData.append("above", this.above);
      formData.append("vendor", this.vendor);
      formData.append("sms", this.sms);
      formData.append("mjm_charges", this.mjm_charges);
      master
        .vendor_ratecard(formData)
        .then((response) => {
          this.successful = true;
          this.error = true;
          //this.getAWBLocation();
          // this.items=response.data;
          // this.items.push (response.data);
          this.cod = "";
          this.codper = "";
          this.above = "";
          this.mjm_charges = "";
          this.sms = "";
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
  },
};
</script>

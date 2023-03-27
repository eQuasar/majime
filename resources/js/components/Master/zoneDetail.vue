<template>
  <b-container fluid>
    <div>
      <div class="card-body">
        <h3><strong>Enter Zone Detail</strong></h3>
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
          <b-overlay :show="show" rounded="sm">
            <b-row style="margin-bottom: 10px">
              <b-col xl="6" lg="6" md="6">
                <b-form-group
                  id="input-group-zone"
                  label="Enter Zone No"
                  label-for="input-zoneno"
                >
                  <b-form-input
                    id="input-amount"
                    :value="this.zoneno"
                    v-model="zoneno"
                    type="text"
                    required
                    placeholder="Enter Zone No"
                  ></b-form-input>
                </b-form-group>
                <b-form-group
                  id="input-group-pin"
                  label="Enter Pin Code"
                  label-for="input-pincode"
                >
                  <b-form-input
                    id="input-amount"
                    :value="this.pincode"
                    v-model="pincode"
                    type="text"
                    required
                    placeholder="Enter Pin Code"
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
import master from "../../api/master.js";
import vendors from "../../api/vendors.js";
import user from "../../api/user.js";
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
      pincode: "",
      zoneno: "",
      seen: false,

      fields: [
        {
          key: "id",
          label: "id",
          sortable: true,
        },
        {
          key: "pincode",
          label: "Pin Code",
          sortable: true,
        },
        {
          key: "zoneno",
          label: "Zone No",
          sortable: true,
        },

        {
          key: "date",
          label: "date",
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
      if (!this.zoneno) {
        this.create_error += "Enter Zone No ,";
      }
      if (!this.pincode) {
        this.create_error += "Enter Pincode,";
      }
      if (this.create_error != "") {
        return false;
      }
      // this.vid = JSON.parse(localStorage.getItem("ivid"));
      let formData = new FormData();
      formData.append("zoneno", this.zoneno);
      formData.append("pincode", this.pincode);
      master
        .addZoneDeatil(formData)
        .then((response) => {
          this.successful = true;
          this.error = true;
          //this.getAWBLocation();
          // this.items=response.data;
          // this.items.push (response.data);
          this.description = "";
          this.tranType = "";
          this.amount = "";
          this.date = "";
          this.vendor = "";
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

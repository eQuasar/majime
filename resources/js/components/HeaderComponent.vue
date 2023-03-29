<template>
  <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="nav_btn" @click="openNav()">
      <i class="fas fa-bars fa-fw"></i>
    </div>
    <div
      class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center"
    >
      <a class="navbar-brand brand-logo mr-5" href="/"><h6>MAJIME</h6></a>
    </div>
    <div
      class="navbar-menu-wrapper d-flex align-items-center justify-content-end"
    >
      <div v-if="this.$userId == 1">
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
            @change="vendor_wise_detail"
          >
            <template v-slot:first>
              <b-form-select-option :value="0" disabled
                >Select Vendor</b-form-select-option
              >
            </template>
          </b-form-select>
        </b-form-group>
      </div>
      <b-dropdown :text="uname" variant="primary">
        <!-- <b-dropdown-item href="#">My Profile</b-dropdown-item> -->
        <b-dropdown-item href="#" @click="logout"
          ><i class="fas fa-power-off fa-fw"></i
          >&nbsp;&nbsp;Logout</b-dropdown-item
        >
      </b-dropdown>
    </div>
    <form id="logout-form" action="/logout" method="POST" style="display: none">
      <input type="hidden" name="_token" :value="token" />
    </form>
  </nav>
</template>
<script>
import user from "../api/user.js";
import vendors from "../api/vendors.js";
import ServiceSidebar from "./ServiceSidebar.vue";
export default {
  data() {
    return {
      vendor: null,
      allvendors: [],
      uid: this.$userId,
      uname: this.$userName,
      message: "This is the home component!",
    };
  },
  components: {},

  mounted() {
    // if(this.$route.query.vid){
    //   this.getVendor();
    //   this.getVidz();
    // }else{
    this.getVendor();
    const ivid = localStorage.getItem("ivid");
    this.vendor = ivid;
    // }
  },
  computed: {
    token() {
      let token = document.head.querySelector('meta[name="csrf-token"]');
      return token.content;
    },
  },
  methods: {
    logout() {
      document.getElementById("logout-form").submit();
    },
    vendor_wise_detail() {
      console.log(this.vendor);
      localStorage.setItem("ivid", this.vendor);

      // this.$router.go("?vid="+this.vendor);
      window.location.assign("?vid=" + this.vendor);

      // this.$router.push({name: 'adminlistOrders', params: {vid: this.vendor}})
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
    // openNav() {
    //   alert("helo");
    //   // document.getElementById(nav_btn).style.width = "250px";
    //   // document.getElementById("main").style.marginLeft = "250px";
    // },
  },
};
</script>

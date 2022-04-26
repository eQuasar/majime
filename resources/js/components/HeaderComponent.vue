<template>
    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo mr-5" href="/"><h6>MAJIME</h6></a>
      </div>
      <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
        <b-dropdown :text="uname" variant="primary">
          <!-- <b-dropdown-item href="#">My Profile</b-dropdown-item> -->
          <b-dropdown-item href="#" @click="logout"><i class="fas fa-power-off fa-fw"></i>&nbsp;&nbsp;Logout</b-dropdown-item>
        </b-dropdown>
      </div>
      <form id="logout-form" action="/logout" method="POST" style="display: none;">
          <input type="hidden" name="_token" :value="token">
      </form>
    </nav>
</template>
<script>
export default {
  data () {
    return {
      uname: this.$userName,
      message: 'This is the home component!'
    };
  },
  computed: {
    token() {
        let token = document.head.querySelector('meta[name="csrf-token"]');
        return token.content
    }
  },
  methods: {
     logout()
     {
         document.getElementById('logout-form').submit()
     }
 }
};
</script>																																																																																								
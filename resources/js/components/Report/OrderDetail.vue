<template>
  <b-container fluid>  
    <div class="content_bar card">
      <div class="card-body">
          <b-row>
            <b-col xl="12" lg="12" md="12">
              <div class="profile_info">
                 <h3 class="own-heading">Report Information &nbsp;<strong><router-link :to="{ name: 'orderdetail', params: { clientid: (this.clientid).toString() }}"></router-link></strong></h3>
            	    <b-row>
                    <b-col xl="2" lg="2" md="2">
                      <img :src="getImgUrl(image)" v-if="image">
                      <img src="/home/equasar/Development/majime/public/images/1.jpg" v-else>
                    </b-col>
                    <b-col xl="10" lg="10" md="10">
                      <p><strong>Order NO:</strong>{{id}}</p>
              
                    </b-col>
                  </b-row>
              </div>
            </b-col>
          </b-row>
  		</div>
  	</div>
  </b-container>
</template>
<script>

  import profile from '../../api/profile.js';
  export default {

    props: {
      clientid: {
        type: String,
        required: true
      },
    },
    mounted() {
      this.getOrderProfile();
    },
    data() {
      return {
        show: false,
        name:'',
        image:'',
        image_file:null,
        mobile:'',
        email:'',
        address:'',
        dob:'',
        successful: false,
        errors_create:[],
        create_error:'',
        selected: 'first',
        options: [],
        fields: [
           {
            key: 'id',
            sortable: true
          },
          
        ],
        items: [],
      };
    },
    methods: {
      getOrderProfile() {
          profile.getOrderprofile()
          .then(( response ) => {
            if(response.data.data)
            {
              this.id = response.data.data.user.id;
   
              
            // }
            }
          })
          .catch(error => {
              console.log(error);
              
          });

      },
     
    
    },
  };
</script>
import axios from './axios.js'

export default {

     getdashboard_detail(data) {
        return axios.get('Order_detail', data)
    },

   }
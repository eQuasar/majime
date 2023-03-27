import axios from './axios.js'

export default {

    getAccountsDetail() {
        return axios.get('Accountdetail')
    },
   }
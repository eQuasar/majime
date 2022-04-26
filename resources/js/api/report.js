import axios from './axios.js'

export default {

    getReportDetail() {
        return axios.get('reportdetail')
    },
   }
import axios from './axios.js'

export default {

    getReportDetail() {
        return axios.get('reportdetail')
    },

    getStatusDetails(data) {
        return axios.post('status_details', data)
    },

}
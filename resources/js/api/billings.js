import axios from './axios.js'

export default {

    getBillingsDetail() {
        return axios.get('billings')
    }
}
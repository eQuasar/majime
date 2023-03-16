import axios from './axios.js'

export default {

    getBillingsDetail() {
        return axios.get('billings')
    },
    get_biiling_detail(data)
    {
        return axios.post('billing_detail', data)
    }
}
import axios from './axios.js'

export default {

    getBillingsDetail() {
        return axios.get('billings')
    },
    get_biiling_detail(data)
    {
        return axios.post('return_billing_process', data)
    },
    billing_process(data)
    {
        return axios.post('billing_process', data)
    },
}
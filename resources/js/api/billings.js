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
    get_hsn_detail(data)
    {
        return axios.post('hsn_wise_detail_copy', data)
    },
    get_state_detail(data)
    {
        return axios.post('state_wise_detail', data)
    },
    get_salereturn_detail(data)
    {
        return axios.post('sale_return_wise_detail', data)
    },
    get_saleinvoice_detail(data)
    {
        return axios.post('sale_invoice_wise_detail', data)
    },
    get_biiling_filter(data)
    {
        return axios.post('billing_filter', data)
    },
  
}



import axios from './axios.js'

export default {

    hsn_detail(data) {
        return axios.post('hsn_detail', data)
    },
    getHsn()
    {
        return axios.get('get_hsn')
    },
    update_hsn_weight(data)
    {
        return axios.post('hsn_weight_update', data) 
    },
    getProduct_data(data)
    {
        return axios.post('getProduct_data',data)
    },

  

}
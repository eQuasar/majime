import axios from './axios.js'

export default {

    getProductDetail(data) {
        return axios.post('getProductdetail', data)
    },

}
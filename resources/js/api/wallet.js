import axios from './axios.js'

export default {


	  getWalletdetail(data) {
        return axios.post('walletDetail', data)
    },

}
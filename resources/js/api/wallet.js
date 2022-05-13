import axios from './axios.js'

export default {

    getWalletdetail() {
        return axios.get('walletdetail')
    },
   
}
import axios from './axios.js'

export default {


	  getWalletdetail(data) {
        return axios.post('walletDetail', data)
    },

    complete_downloadsheet(data) {
        return axios.post('complete_download_sheet', data)
    },
    getStats(vid) {
        return axios.get('stat/'+vid)
    },   
    wallet_sheet(data) {
        return axios.post('wallet_Sheet_download', data)
    }

}
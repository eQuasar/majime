import axios from './axios.js'

export default {

    addZoneDeatil(data) {
        return axios.post('addZone_Deatil', data)
    },
    getzone(data) {
        return axios.get('get_zone', data)
    },
    zoneratecard(data) {
        return axios.post('zonerate_card', data)
    },
    vendor_ratecard(data) {
        return axios.post('vendor_rate_card', data)
    },
}
    import axios from './axios.js'

export default {

    getVendors() {
        return axios.get('vendors')
    },

    getVendor(id) {
        return axios.get('vendors/' + id)
    },

    addVendor(data) {
        return axios.post('vendors', data)
    },

    editVendor(id, data) {
        return axios.post('vendors/' + id, data)
    },

    deleteVendor(id) {
        return axios.delete('vendors/' + id)
    },
}
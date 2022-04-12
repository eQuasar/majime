import axios from './axios.js'

export default {

    getServiceModes() {
        return axios.get('servicemode')
    },

    getServiceMode(id) {
        return axios.get('servicemode/' + id)
    },

    addServiceMode(data) {
        return axios.post('servicemode', data)
    },

    editServiceMode(id, data) {
        return axios.post('servicemode/' + id, data)
    },

    deleteServiceMode(id) {
        return axios.delete('servicemode/' + id)
    },
}
import axios from './axios.js'

export default {

    getServices() {
        return axios.get('service')
    },

    getService(id) {
        return axios.get('service/' + id)
    },

    addService(data) {
        return axios.post('service', data)
    },

    editService(id, data) {
        return axios.post('service/' + id, data)
    },

    deleteService(id) {
        return axios.delete('service/' + id)
    },
    getMyServices(data){
        return axios.post('getmyservices', data)
    }
}
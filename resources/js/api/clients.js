import axios from './axios.js'

export default {

    getClients() {
        return axios.get('clients')
    },

    getClient(id) {
        return axios.get('clients/' + id)
    },

    addClient(data) {
        return axios.post('clients', data)
    },

    editClient(id, data) {
        return axios.post('clients/' + id, data)
    },

    deleteClient(id) {
        return axios.delete('clients/' + id)
    },

    countClients() {
        return axios.get('countclients')
    },

    getClientInfo(data) {
        return axios.post('getclientinfo', data)
    },
}
import axios from './axios.js'

export default {

    getServiceCosts() {
        return axios.get('servicecost')
    },

    getServiceCost(id) {
        return axios.get('servicecost/' + id)
    },

    addServiceCost(data) {
        return axios.post('servicecost', data)
    },

    editServiceCost(id, data) {
        return axios.post('servicecost/' + id, data)
    },

    deleteServiceCost(id) {
        return axios.delete('servicecost/' + id)
    },
    getCost(data) {
        return axios.post('getcost', data)
    },
}
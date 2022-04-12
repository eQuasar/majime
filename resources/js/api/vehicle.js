import axios from './axios.js'

export default {

    getVehicles() {
        return axios.get('vehicle')
    },

    getVehicle(id) {
        return axios.get('vehicle/' + id)
    },

    addVehicle(data) {
        return axios.post('vehicle', data)
    },

    editVehicle(id, data) {
        return axios.post('vehicle/' + id, data)
    },

    deleteVehicle(id) {
        return axios.delete('vehicle/' + id)
    },
}
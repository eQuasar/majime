import axios from './axios.js'

export default {

    getPetAggresives() {
        return axios.get('petaggresive')
    },

    getPetAggresive(id) {
        return axios.get('petaggresive/' + id)
    },

    addPetAggresive(data) {
        return axios.post('petaggresive', data)
    },

    editPetAggresive(id, data) {
        return axios.post('petaggresive/' + id, data)
    },

    deletePetAggresive(id) {
        return axios.delete('petaggresive/' + id)
    },
}
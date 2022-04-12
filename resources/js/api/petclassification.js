import axios from './axios.js'

export default {

    getPetClassifications() {
        return axios.get('petclassification')
    },

    getPetClassification(id) {
        return axios.get('petclassification/' + id)
    },

    addPetClassification(data) {
        return axios.post('petclassification', data)
    },

    editPetClassification(id, data) {
        return axios.post('petclassification/' + id, data)
    },

    deletePetClassification(id) {
        return axios.delete('petclassification/' + id)
    },
}
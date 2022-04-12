import axios from './axios.js'

export default {

    getPetClasses() {
        return axios.get('petclass')
    },

    getPetClass(id) {
        return axios.get('petclass/' + id)
    },

    addPetClass(data) {
        return axios.post('petclass', data)
    },

    editPetClass(id, data) {
        return axios.post('petclass/' + id, data)
    },

    deletePetClass(id) {
        return axios.delete('petclass/' + id)
    },
}
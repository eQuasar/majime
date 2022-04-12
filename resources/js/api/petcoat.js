import axios from './axios.js'

export default {

    getPetCoats() {
        return axios.get('petcoat')
    },

    getPetCoat(id) {
        return axios.get('petcoat/' + id)
    },

    addPetCoat(data) {
        return axios.post('petcoat', data)
    },

    editPetCoat(id, data) {
        return axios.post('petcoat/' + id, data)
    },

    deletePetCoat(id) {
        return axios.delete('petcoat/' + id)
    },
}
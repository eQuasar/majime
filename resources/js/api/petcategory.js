import axios from './axios.js'

export default {

    getPetCategories() {
        return axios.get('petcategory')
    },

    getPetCategory(id) {
        return axios.get('petcategory/' + id)
    },

    addPetCategory(data) {
        return axios.post('petcategory', data)
    },

    editPetCategory(id, data) {
        return axios.post('petcategory/' + id, data)
    },

    deletePetCategory(id) {
        return axios.delete('petcategory/' + id)
    },
}
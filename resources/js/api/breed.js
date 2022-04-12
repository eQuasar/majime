import axios from './axios.js'

export default {

    getBreeds() {
        return axios.get('breed')
    },

    getBreed(id) {
        return axios.get('breed/' + id)
    },

    addBreed(data) {
        return axios.post('breed', data)
    },

    editBreed(id, data) {
        return axios.post('breed/' + id, data)
    },

    deleteBreed(id) {
        return axios.delete('breed/' + id)
    },
}
import axios from './axios.js'

export default {

    getCountries() {
        return axios.get('country')
    },

    getCountry(id) {
        return axios.get('country/' + id)
    },

    addCountry(data) {
        return axios.post('country', data)
    },

    editCountry(id, data) {
        return axios.post('country/' + id, data)
    },

    deleteCountry(id) {
        return axios.delete('country/' + id)
    },
}
import axios from './axios.js'

export default {

    getCities() {
        return axios.get('city')
    },

    getCity(id) {
        return axios.get('city/' + id)
    },

    addCity(data) {
        return axios.post('city', data)
    },

    editCity(id, data) {
        return axios.post('city/' + id, data)
    },

    deleteCity(id) {
        return axios.delete('city/' + id)
    },
}
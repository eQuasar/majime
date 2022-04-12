import axios from './axios.js'

export default {

    getAreas() {
        return axios.get('area')
    },

    getArea(id) {
        return axios.get('area/' + id)
    },

    addArea(data) {
        return axios.post('area', data)
    },

    editArea(id, data) {
        return axios.post('area/' + id, data)
    },

    deleteArea(id) {
        return axios.delete('area/' + id)
    },
}
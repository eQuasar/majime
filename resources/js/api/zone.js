import axios from './axios.js'

export default {

    getZones() {
        return axios.get('zone')
    },

    getZone(id) {
        return axios.get('zone/' + id)
    },

    addZone(data) {
        return axios.post('zone', data)
    },

    editZone(id, data) {
        return axios.post('zone/' + id, data)
    },

    deleteZone(id) {
        return axios.delete('zone/' + id)
    },
}
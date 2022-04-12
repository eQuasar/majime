import axios from './axios.js'

export default {

    getOtherUsers() {
        return axios.get('otheruser')
    },

    getOtherUser(id) {
        return axios.get('otheruser/' + id)
    },

    addOtherUser(data) {
        return axios.post('otheruser', data)
    },

    editOtherUser(id, data) {
        return axios.post('otheruser/' + id, data)
    },

    deleteOtherUser(id) {
        return axios.delete('otheruser/' + id)
    },
}
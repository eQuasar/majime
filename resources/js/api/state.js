import axios from './axios.js'

export default {

    getStates() {
        return axios.get('state')
    },

    getState(id) {
        return axios.get('state/' + id)
    },

    addState(data) {
        return axios.post('state', data)
    },

    editState(id, data) {
        return axios.post('state/' + id, data)
    },

    deleteState(id) {
        return axios.delete('state/' + id)
    },
}
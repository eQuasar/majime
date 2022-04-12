import axios from './axios.js'

export default {

    getTimeSlots() {
        return axios.get('timeslot')
    },

    getTimeSlot(id) {
        return axios.get('timeslot/' + id)
    },

    addTimeSlot(data) {
        return axios.post('timeslot', data)
    },

    editTimeSlot(id, data) {
        return axios.post('timeslot/' + id, data)
    },

    deleteTimeSlot(id) {
        return axios.delete('timeslot/' + id)
    },
}
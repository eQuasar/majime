import axios from './axios.js'

export default {

    getClassificationCharges() {
        return axios.get('classificationcharge')
    },

    getClassificationCharge(id) {
        return axios.get('classificationcharge/' + id)
    },

    addClassificationCharge(data) {
        return axios.post('classificationcharge', data)
    },

    editClassificationCharge(id, data) {
        return axios.post('classificationcharge/' + id, data)
    },

    deleteClassificationCharge(id) {
        return axios.delete('classificationcharge/' + id)
    },
}
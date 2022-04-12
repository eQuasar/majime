import axios from './axios.js'

export default {

    getClassificationMonths() {
        return axios.get('classificationmonth')
    },

    getClassificationMonth(id) {
        return axios.get('classificationmonth/' + id)
    },

    addClassificationMonth(data) {
        return axios.post('classificationmonth', data)
    },

    editClassificationMonth(id, data) {
        return axios.post('classificationmonth/' + id, data)
    },

    deleteClassificationMonth(id) {
        return axios.delete('classificationmonth/' + id)
    },
}
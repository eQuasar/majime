import axios from './axios.js'

export default {

    getOrderDetail() {
        return axios.get('getOrderdetail')
    },

    
    /*

    getAppointment(id) {
        return axios.get('appointment/' + id)
    },

    addAppointment(data) {
        return axios.post('appointment', data)
    },

    editAppointment(id, data) {
        return axios.post('appointment/' + id, data)
    },

    deleteAppointment(id) {
        return axios.delete('appointment/' + id)
    },
    appointmentSearch(data) {
        return axios.post('appointmentSearch', data)
    },
    clientAppointment(data) {
        return axios.post('clientappointment', data)
    },
    freeVehicle(data) {
        return axios.post('free_vehicle', data)
    },
    assignAppointment(data) {
        return axios.post('assign_appointment', data)
    },
    scheduleAppointment(data) {
        return axios.post('schedule_appointment', data)
    },
    cancelAppointment(data) {
        return axios.post('cancel_appointment', data)
    },
    processAppointment(data) {
        return axios.post('process_appointment', data)
    },
    feedbackAppointment(data) {
        return axios.post('feedback', data)
    },
    upcomingAppointments() {
        return axios.get('upcomingappointment')
    },
    // groomerAppointment(id) {
    //     return axios.get('groomerappointment')
    // },
    groomerAppointments(data) {
        return axios.post('groomerappointments', data)
    },
    excelAppointments(data) {
        return axios.post('excelappointments', data)
    },
    getPetAppointment(data) {
        return axios.post('getpetappointment', data)
    }*/
}
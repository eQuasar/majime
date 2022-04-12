import axios from './axios.js'

export default {

    getPets() {
        return axios.get('pets')
    },

    getPet(id) {
        return axios.get('pets/' + id)
    },

    addPet(data) {
        return axios.post('pets', data)
    },

    editPet(id, data) {
        return axios.post('pets/' + id, data)
    },

    deletePet(id) {
        return axios.delete('pets/' + id)
    },
    clientPet(data) {
        return axios.post('clientpet', data)
    },
    getMyPets(data){
        return axios.post('getmypets', data)
    }
}
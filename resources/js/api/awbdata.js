import axios from './axios.js'

export default {

    addWayData(data) {
        return axios.post('addWayData', data)
    },
    updateWayData(data) {
        return axios.post('updateWayData', data)
    },
    getAWBLocation(data){
    	return axios.post('getAWBLocation', data)
    },
    getawbdata(data) {
        return axios.post('getawbdata', data)
    },

}
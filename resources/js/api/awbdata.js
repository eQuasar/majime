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
    }

}
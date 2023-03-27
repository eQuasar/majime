import axios from './axios.js'

export default {

    getdashboard_detail(vid) {
        return axios.get('dashboard_detail/' + vid)
    },
    dashbaordSearch(data) {
        return axios.post('dashboard_search', data)
    },
    getchart(vid) {
        return axios.get('chart_data/' + vid)
    },
    getpiechart(vid) {
        return axios.get('piechart_data/' + vid)
    },
    delgetpiechart(vid) {
        return axios.get('delpiechart_data/' + vid)
    },
    getsecondpiechart(vid) {
        return axios.get('secondpiechart_data/' + vid)
    },
    getsales_detail(vid) {
        return axios.get('getsales_data/' + vid)
    },
    getmargin_report(vid) {
        return axios.get('getmargin_report/' + vid)
    },
    // getsale_detail(vid) {
    //     return axios.get('getsales/' + vid)
    // },

}
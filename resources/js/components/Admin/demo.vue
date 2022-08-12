<template>
  <b-container fluid>
    <b-overlay
      :show="show"
      rounded="sm"
      spinner-type="grow"
      spinner-variant="primary"
      spinner-small
    >
      <div class="Dashboard_title">
        <div class="header_inner">
          <h3><strong>Dashboard</strong></h3>
          <br />
        </div>
      </div>
      <template>
        <div class="row chartsss">
        

          <div class="col-sm-4">

            <div class="content_bar card">
              <Pie
                :chart-options="pieOptions"
                :chart-data="pieData"
                :chart-id="chartId"
                :dataset-id-key="datasetIdKey"
                :plugins="plugins"
                :css-classes="cssClasses"
                :styles="styles"
                :width="width"
                :height="height"
              />
            </div>
          </div>
            <div class="col-sm-4">
            <div class="content_bar card">
            	  	 <h3><strong>All Orders Data</strong></h3>
              <apexchart
                width="500"
                type="bar"
                :options="options"
                :series="series"
              ></apexchart>
            </div>
          </div>

           <div class="col-sm-4">
            <div class="content_bar card">
               <Doughnut
                :chart-options="chartpie"
                :chart-data="chartdatapie"
                :chart-id="chartId"
                :dataset-id-key="datasetIdKey"
                :plugins="plugins"
                :css-classes="cssClasses"
                :styles="styles"
                :width="width"
                :height="height"
              />  
            </div>
          </div>

       <!--    <div class="col-sm-4">
            <div class="content_bar card">
              <apexchart
                width="380"
                type="pie"
                :options="chartOptions1"
                :series="series1"
              ></apexchart>
            </div>
          </div> -->
        </div>
      </template><br>
      <div class="content_bar card">
        <div class="card-body">
          <div class="call-center-dashboard">
            <b-col xl="8" lg="8" md="8">
              <b-alert show variant="danger" v-if="create_error">{{
                create_error
              }}</b-alert>
              <b-form @submit="onSubmit" class="date_range">
                <div class="datepiker-block">
                  <span>From:&nbsp;</span>
                  <b-form-datepicker
                    id="from"
                    v-model="date_from"
                    :date-format-options="{
                      year: 'numeric',
                      month: 'short',
                      day: '2-digit',
                      weekday: 'short',
                    }"
                    locale="en"
                  ></b-form-datepicker>
                </div>
                <div class="datepiker-block">
                  <span>To:&nbsp;</span>
                  <b-form-datepicker
                    id="to"
                    v-model="date_to"
                    :date-format-options="{
                      year: 'numeric',
                      month: '2-digit',
                      day: '2-digit',
                    }"
                    locale="en"
                  ></b-form-datepicker>
                </div>
                <b-button type="submit" variant="primary">Submit</b-button>
              </b-form>
            </b-col>
          </div>
        </div>
      </div>
      <br />
      <div class="right">
        <div class="stats red">
          <h4>Total Orders</h4>
          <span>Total Orders:{{ dashboardData.totalcount }} </span>
          <p><i>₹ </i>{{ dashboardData.totalSaleAmount }}</p>
        </div>
        <div class="stats blu">
          <h4>Wallet Processed</h4>
          <span>Total Orders:{{ dashboardData.walletcount }} </span>
          <p><i>₹ </i>{{ dashboardData.walletsale }}</p>
        </div>
        <div class="stats orng">
          <h4>Delivered to Customer</h4>
          <span>Total Orders:{{ dashboardData.delivercustcount }} </span>
          <p><i>₹ </i>{{ dashboardData.deliversale }}</p>
        </div>
        <div class="stats grn">
          <h4>Intransit</h4>
          <span>Total Orders:{{ dashboardData.intransitcount }} </span>
          <p><i>₹ </i>{{ dashboardData.intransitSaleAmount }}</p>
        </div>
      </div>
      <br />
      <br />
        <div class="margin-report-title">
          <h3><strong>Margin Report</strong></h3>
          <h6>(Estimate Value)</h6>
        </div>
      <div class="margin-report">
        <div class="stats blu">
          <h4>Gross sale</h4>
          <span>Total Orders:{{ dashboardData.grosscount }}</span>
          <p><i>₹ </i>{{ dashboardData.grossSaleAmount }}</p>
        </div>
        <div class="stats red">
          <h4>Estimated Net Sale</h4>
          <span>{{ dashboardData.netcount }}</span>
          <p><i>₹ </i>{{ dashboardData.netsale }}</p>
        </div>
        <div class="stats orng">
          <h4>Estimated Product Cost</h4>
          <span>N/A</span>
          <p><i>₹ </i>N/A</p>
        </div>
        <div class="stats grn">
          <h4>Ad Cost</h4>
          <span>N/A</span>
          <p><i>₹ </i>N/A</p>
        </div>
      </div>

      <div class="margin-report-title">
          <h3>Actual Report</h3>
          <h6>(Actual Value)</h6>
        </div>
      <div class="margin-report">
        <div class="stats blu">
          <h4>Actual Gross sale</h4>
          <span>Total Orders:{{ dashboardData.grosscount }}</span>
          <p><i>₹ </i>{{ dashboardData.grossSaleAmount }}</p>
        </div>
        <div class="stats red">
          <h4>Actual Net Sale</h4>
          <span>{{ dashboardData.netcount }}</span>
          <p><i>₹ </i>{{ dashboardData.netsale }}</p>
        </div>
        <div class="stats orng">
          <h4>Actual Product Cost</h4>
          <span>N/A</span>
          <p><i>₹ </i>N/A</p>
        </div>
        <div class="stats grn">
          <h4>Ad Cost</h4>
          <span>N/A</span>
          <p><i>₹ </i>N/A</p>
        </div>
      </div>

    </b-overlay>
  </b-container>
</template>
<script>
import dashboard from "../../api/dashboard.js";
import user from "../../api/user.js";
import VueApexCharts from "vue-apexcharts";
import { Pie } from "vue-chartjs/legacy";
import { Doughnut } from 'vue-chartjs/legacy';
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  ArcElement,
  CategoryScale,
} from "chart.js";

ChartJS.register(Title, Tooltip, Legend, ArcElement, CategoryScale);

export default {
  mounted() {
    this.getVidz();
  },
  name: "PieChart",
  name: 'DoughnutChart',
  components: {
    Pie,
    apexcharts: VueApexCharts,
     Doughnut,
  },
  props: {
    chartId: {
      type: String,
      default: "pie-chart",
    },
    datasetIdKey: {
      type: String,
      default: "label",
    },
    width: {
      type: Number,
      default: 300,
    },
    height: {
      type: Number,
      default: 300,
    },
    cssClasses: {
      default: "",
      type: String,
    },
    styles: {
      type: Object,
      default: () => {},
    },
    plugins: {
      type: Array,
      default: () => [],
    },
     chartData: {
      type: Array
    },
  },
  props: {
    chartId: {
      type: String,
      default: 'doughnut-chart'
    },
    datasetIdKey: {
      type: String,
      default: 'label'
    },
    width: {
      type: Number,
      default: 300
    },
    height: {
      type: Number,
      default: 300
    },
    cssClasses: {
      default: '',
      type: String
    },
    styles: {
      type: Object,
      default: () => {}
    },
    plugins: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      show: false,
      ariaDescribedby: "",
      time: "",
      date: "",
      time_slots: [],
      seen: false,
      date_from: "",
      date_to: "",
      sortBy: "date",
      sortDesc: true,
      perPage: 10,
      chartData: '',
      currentPage: 1,
      filter2: null,
      pageOptions: [5, 10, 15, 20, 50, 100],
      filter: null,
      filterOn2: [],
      filterOn: [],
      items: [],
      chartdata: [],
      errors_create: [],
      dashboardData: [],
      successful: false,
      create_error: "",
      options: {
        chart: {
          id: "vuechart-example",
        },
        xaxis: {
          categories: [
            "Processing",
            "Confirmed",
            "Packed",
            "Dispatched",
            "Intransit",
            "Delivered",
            "Completed",
            "Closed",
            "Dtobooked",
            "On-hold",
            "Cancelled",
            "Rto-delivered",
            "Dto-delivered",
          ],
        },
      },
      series: [
        {
          name: "series-1",
          data: [],
        },
      ],
      pieData: {
            labels: [
              "Processed Orders",
              "Failed Orders",
              "Cancelled Orders",
              "Others",
            ],
        datasets: [
          {
            backgroundColor: ["#41B883", "#E46651", "#DD1B16", "#00D8FF"],
            data:[],
          },
        ],
      },
      pieOptions: {
        responsive: true,
        maintainAspectRatio: false,
      },
      chartdatapie: {
        labels: ['Processed Amount', 'Deliverd Amount', 'Intransit Amount'],
        datasets: [
          {
            backgroundColor: ['#41B883', '#E46651', '#00D8FF'],
            data:[],
          }
        ]
      },
      chartpie: {
        responsive: true,
        maintainAspectRatio: false
      },

    };
  },

  computed: {
    rows() {
      return this.items.length;
    },
  },

  methods: {
    getVidz() {
      if (this.$userId == 1) {
        this.show = true;
        this.vid = JSON.parse(localStorage.getItem("ivid"));
        localStorage.setItem("ivid", this.vid);
        // this.getWalletDetail(this.vid);
        this.Orderdetail(this.vid);
        this.dashboard_data(this.vid);
        this.dashboard_piedata(this.vid);
         this.dashboard_secondpiedata(this.vid) 
        this.show = false;
      } else {
        this.show = true;
        let formData = new FormData();
        formData.append("user_id", this.$userId);
        user
          .getVid(formData)
          .then((response) => {
            this.vid = response.data;
            localStorage.setItem("ivid", this.vid);
            // this.getWalletDetail(this.vid);
            this.Orderdetail(this.vid);
            this.dashboard_data(this.vid);
            this.dashboard_piedata(this.vid);
             this.dashboard_secondpiedata(this.vid) 
            this.show = false;
          })
          .catch((response) => {
            this.successful = false;
            //alert('something went wrong');
          });
      }
      // alert("aaa");
    },
    onSubmit(event) {
      event.preventDefault();
      this.create_error = "";
      if (!this.date_from) {
        this.create_error += "Add date from,";
      }
      if (!this.date_to) {
        this.create_error += "Add date to,";
      }
      if (this.create_error != "") {
        return false;
      }
      let formData = new FormData();
      formData.append("date_from", this.date_from);
      formData.append("date_to", this.date_to);
      formData.append("vid", this.vid);
      dashboard
        .dashbaordSearch(formData)
        .then((response) => {
          var resp = response.data;
          this.dashboardData = resp;
          console.log(this.dashboardData);

          // }
        })
        .catch((error) => {
          console.log(error);
          if (error.response.status == 422) {
            this.errors_create = error.response.data.errors;
          }
          // loader.hide();
        });
      // console.log(this.date_from);
      // console.log(this.date_to);
      // alert(JSON.stringify(this.form))
    },

    Orderdetail(vid) {
      // this.seen = true;
      let formData = new FormData();
      dashboard
        .getdashboard_detail(vid)
        .then((response) => {
          var resp = response.data;
          this.dashboardData = resp;
          console.log(this.dashboardData);
        })
        .catch((response) => {
          this.successful = false;
          alert("something went wrong");
        });
    },
    dashboard_data() {
      this.vid = JSON.parse(localStorage.getItem("ivid"));
      localStorage.setItem("ivid", this.vid);
      dashboard
        .getchart(this.vid)
        .then((response) => {
          // this.series = response.data;
          var chart = response.data;
          var val = chart.values;
          this.series = [{
          data: val
        }]
        })
        .catch((response) => {
          this.successful = false;
          alert("something went wrong");
        });
    },
     dashboard_piedata(vid) 
     {
        dashboard
        .getpiechart(this.vid)
        .then((response) => {
        const responseData = response.data; 
       this.pieData={
          datasets: [
            {
            data:responseData.pie
            }
         ]
        }
          })
         .catch(e => {
        this.errors.push(e)
      })
        
      },
      dashboard_secondpiedata(vid) 
     {
        dashboard
        .getsecondpiechart(this.vid)
        .then((response) => 
        {
          const responseData = response.data;   
          this.chartdatapie={
          datasets: [
            {
            data:responseData.amount
            }
         ]
        }
      })
      .catch(e => {
        this.errors.push(e)
      })
    }
  }
};
</script>

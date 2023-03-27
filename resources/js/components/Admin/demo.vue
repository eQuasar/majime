  <template>
    <b-container fluid>
        <b-overlay :show="show" rounded="sm" spinner-type="grow" spinner-variant="primary" spinner-small>
            <div class="main">
                <div class="Dashboard_title">
                    <div class="header_inner">
                        <h3><strong>Dashboard</strong></h3>
                        <br />
                    </div>
                </div>
            </div>
            <div class="content_bar card">
                <div class="card-body padd-off">
                    <div class="call-center-dashboard">
                        <b-col xl="8" lg="8" md="8" class="padd-off">
                            <b-alert show variant="danger" v-if="create_error">{{ create_error }}</b-alert>
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
                                      month: 'short',
                                      day: '2-digit',
                                      weekday: 'short',
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
            <br>
            <div class="content_bar card grey dashboard-sales">
                <div class="card-body">
                    <div class="sales">
                        <div class="header_inner text-center">
                            <br />
                                <h3><strong>Sales Performance</strong></h3>
                            <br />
                        </div>

                        <div class="sales">
                          <div class="row">
                            <div class="col-sm-6">
                                <b-table
                                  striped
                                  hover
                                  responsive
                                  :items="valdata"
                                  :sort-by.sync="sortBy"
                                  :sort-desc.sync="sortDesc"
                                  sort-icon-left
                                  :filter-included-fields="filterOn"
                                  :filter="filter"
                                  :fields="fields"
                                  :per-page="perPage"
                                  :current-page="currentPage"
                                  show-empty
                                  class="tbl-blk"
                                 >
                                  <template #empty="scope">
                                    <p style="text-align: center">
                                      No record found, choose date filter to found the result.
                                    </p>
                                  </template>
                                  <template v-slot:cell(row.items.status)="row">
                                    {{ row.items.status }}
                                  </template>
                                  <template v-slot:cell(row.items.count)="row">
                                    {{ row.items.count }}
                                  </template>
                                  <template v-slot:cell(row.items.sale)="row">     
                                      {{ row.items.sale }}
                                    >
                                  </template>
                                </b-table>
                            </div>
                            <div class="col-sm-6">
                                <div class="content_bar card">
                                    <apexchart width="100%"  height="250" type="bar" :options="options" :series="series"></apexchart>
                        
                                  <!-- <Doughnut
                                    :chart-options="chartpie"
                                    :chart-data="chartdatapie"
                                    :chart-id="chartId"
                                    :dataset-id-key="datasetIdKey"
                                    :plugins="plugins"
                                    :css-classes="cssClasses"
                                    :styles="styles"
                                    :width="100"
                                    :height="height"
                                  /> -->
                                </div>
                            </div>
                          </div>
                        </div>
                    </div>
                </div>
            </div>
            </br>
            <div class="content_bar card grey dashboard-sales">
                <div class="card-body">
                    <div class="table">
                        <div class="header_inner text-center">
                            <br />
                            <h3><strong>Logistics Performance</strong></h3>
                            <br />
                        </div>

                        <div class="sales row">
                            <div class="col-sm-4">
                                <Pie :chart-options="pieOptions" :chart-data="pieData" :chart-id="chartId" :dataset-id-key="datasetIdKey" :plugins="plugins" :css-classes="cssClasses" :styles="styles" :width="width" :height="height" />
                            </div>
                            <div class="col-sm-8">
                               <b-table
                                  striped
                                  hover
                                  responsive
                                  :items="logisticsdata"
                                  :sort-by.sync="sortBy"
                                  :sort-desc.sync="sortDesc"
                                  sort-icon-left
                                  :filter-included-fields="filterOn"
                                  :filter="filter"
                                  :fields="fields3"
                                  :per-page="perPage"
                                  :current-page="currentPage"
                                  show-empty
                                  class="tbl-blk"
                                 >
                                  <template #empty="scope">
                                    <p style="text-align: center">
                                      No record found, choose date filter to found the result.
                                    </p>
                                  </template>
                                  <template v-slot:cell(row.items.status)="row">
                                    {{ row.items.status }}
                                  </template>
                                  <template v-slot:cell(row.items.count)="row">
                                    {{ row.items.count }}
                                  </template>
                                  <template v-slot:cell(row.items.amount)="row">
                                      {{ row.items.amount }}
                                  </template>
                                     <template v-slot:cell(row.items.percentage)="row">
                                      {{ row.items.percentage }}
                                  </template>
                                  </b-table>
                                <!-- <div class="content_bar card">
                                    <apexchart width="100%"  height="300" type="bar" :options="options" :series="series"></apexchart>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </br>
            <div class="content_bar card grey dashboard-sales">
              <div class="card-body">
                <div class="header_inner text-center">
                            <br />
                               <h3><strong>Operational Pendency</strong></h3>
                            <br />
                        </div>
                <div class="row">
                  <div class="col-sm-8">
                   <b-table
                                  striped
                                  hover
                                  responsive
                                  :items="pendencydata"
                                  :sort-by.sync="sortBy"
                                  :sort-desc.sync="sortDesc"
                                  sort-icon-left
                                  :filter-included-fields="filterOn"
                                  :filter="filter"
                                  :fields="fields4"
                                  :per-page="perPage"
                                  :current-page="currentPage"
                                  show-empty
                                  class="tbl-blk"
                                 >
                                  <template #empty="scope">
                        <p style="text-align: center">
                          No record found, choose date filter to found the result.
                        </p>
                      </template>
                      <template v-slot:cell(row.items.status)="row">
                        {{ row.items.status }}
                      </template>
                      <template v-slot:cell(row.items.count)="row">
                        {{ row.items.count }}
                      </template>
                      <template v-slot:cell(row.items.amount)="row">
                          {{ row.items.amount }}
                      </template>
                      <template v-slot:cell(row.items.perc)="row">
                          {{ row.items.perc }}
                      </template>

                      </b-table>
                  </div>
                  <div class="col-sm-4 boxes">
                       <Pie :chart-options="delpieOptions" :chart-data="delpieData" :chart-id="chartId" :dataset-id-key="datasetIdKey" :plugins="plugins" :css-classes="cssClasses" :styles="styles" :width="width" :height="height" />

                  </div>
              </div>
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
import { Doughnut } from "vue-chartjs/legacy";
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
  name: "DoughnutChart",
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
      default: 250,
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
      type: Array,
    },
  },
  props: {
    chartId: {
      type: String,
      default: "doughnut-chart",
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
      default: 250,
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
      chartData: "",
      percentage:"",
      perc:"",
      currentPage: 1,
      filter2: null,
      pageOptions: [5, 10, 15, 20, 50, 100],
      filter: null,
      filterOn2: [],
      filterOn: [],
      pendencydata:[],
      items: [],
      logisticsdata:[],
      valdata: [],
      dsales: [],
      dcount: [],
      dstatus: [],
      chartdata: [],
      errors_create: [],
      dashboardData: [],
      successful: false,
      create_error: "",
      fields: [
        {
          key: "status",
          label: "Status",
          sortable: false,
        },
        {
          key: "count",
          label: "Count",
          sortable: false,
        },

        {
          key: "sale",
          label: "Sale Amount",
          sortable: false,
        },

      ],
      fields3: [
       {
          key: "status",
          label: "Status",
          sortable: false,
        },
        {
          key: "count",
          label: "Count",
          sortable: false,
        },
        {
          key: "amount",
          label: "Sale Amount",
          sortable: false,
        },
         {
          key: "percentage",
          label: "Percentage",
          sortable: false,
        },
      ],
        fields4: [
       {
          key: "status",
          label: "Status",
          sortable: false,
        },
        {
          key: "count",
          label: "Count",
          sortable: false,
        },
        {
          key: "amount",
          label: "Sale Amount",
          sortable: false,
        },
      ],
      items: [],
      items2: [],
      values: [],
      stats: [],
      status: [],
      errors_create: [],
      successful: false,
      create_error: "",

      options: {
        chart: {
          id: "vuechart-example",
        },
        xaxis: {
          categories: [],
        },
      },
      series: [
        {
          name: "",
          data: [],
        },
      ],
      pieData: {
        labels: ["Delivered Orders", "RTO", "DTO", "Intransit"],
        datasets: [
          {
            backgroundColor: ["#41B883", "#1CD6CE", "#E64848", "#3AB4F2"],
            data: [],
          },
        ],
      },
      pieOptions: {
        responsive: true,
        maintainAspectRatio: false,
      },
      chartdatapie: {
        labels: ["Processed", "RTO", "DTO", "Dispatched","Packed","Processing","confirmed","intransit","completed","on-hold"],
        datasets: [
          {
            backgroundColor: ["#0078AA","#1CD6CE","#E64848","#3AB4F2","#DF7861","#59CE8F","#76BA99","#377D71","#F2DF3A","#FEB139"],
            data: [],
          },
        ],
      },
      chartpie: {
        responsive: true,
        maintainAspectRatio: false,
      },

        delpieData: {
        labels: ["Processing","Confirmed","Packed","On-hold"],
        datasets: [
          {
            backgroundColor: ["#41B883", "#E64848","#3AB4F2","#FEB139"],
            data: [],
          },
        ],
      },
      delpieOptions: {
        responsive: true,
        maintainAspectRatio: false,
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
        this.dashboard_secondpiedata(this.vid);
        this.getsalesdetail(this.vid);
        this.dashboard_delpiedata(this.vid);
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
            this.dashboard_secondpiedata(this.vid);
            this.getsalesdetail(this.vid);
            this.dashboard_delpiedata(this.vid);
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

          //  var resp = response.data[5];
          // this.dashboardData = resp;
          //  var chart = response.data;
          // var val = chart.values;
          // var cat = chart.catgories;

          var resp = response.data[5];
          this.valdata = resp;
          // console.log(response.data[0]);
          var logistic=response.data[1];
          this.logisticsdata =logistic;
          const responseDatalogistic = response.data[0];
           this.pieData = {
            datasets: [
              {
                data: responseDatalogistic.pie,
              },
            ],
          };        
          const responseDatapie = response.data[0];
          this.pieData = {
            datasets: [
              {
                data: responseDatapie.pie,
              },
            ],
          };
           const responseDatadel = response.data[2];
            var pendency=response.data[3]
           this.pendencydata = pendency;
           this.delpieData = {
            datasets: [
              {
                data: responseDatadel.deldata,
              },
            ],
          };
          const responseDatapiedata = response.data[4];
          this.chartdatapie = {
            datasets: [
              {
                data: responseDatapiedata.piedata,
                
              },
            ],
          };

         var chart = response.data[6];
          var val = chart.values;
          var cat = chart.catgories;
          this.series = [
            {
              data: val,
            },
          ];
          
          this.options = {
            chart: {
              id: "Sales Chart",
            },
            xaxis: {
              categories: cat,
            },
          };
      

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

    getsalesdetail(vid) {
      // this.seen = true;
      let formData = new FormData();
      dashboard
        .getsales_detail(vid)
        .then((response) => {
          var data = response.data;
          // this.dsales = data.sale;
          // this.dcount = data.count;
          // this.dstatus = data.status;
          this.valdata = data;
        })
        .catch((error) => {
          console.log(error);
          if (error.response.status == 422) {
            this.errors_create = error.response.data.errors;
          }
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
          var cat = chart.catgories;
          this.series = [
            {
              data: val,
            },
          ];
          
          this.options = {
            chart: {
              id: "Sales Chart",
            },
            xaxis: {
              categories: cat,
            },
          };

          console.log(JSON.stringify(this.categories))
          
        })
        .catch((response) => {
          this.successful = false;
          alert("something went wrong");
        });
    },
    dashboard_piedata(vid) {
      dashboard
        .getpiechart(this.vid)
        .then((response) => {
          const responseData = response.data[0];
          var logistic=response.data[1]
           this.logisticsdata = logistic;
          this.pieData = {
            datasets: [
              {
                data: responseData.pie,
              },
            ],
          };
        })
        .catch((e) => {
          this.errors.push(e);
        });
    },
      dashboard_delpiedata(vid) {
      dashboard
        .delgetpiechart(this.vid)
        .then((response) => {
          const responseData = response.data[0];
          console.log("responseData "+responseData)
          var pendency=response.data[1]
           this.pendencydata = pendency;
          this.delpieData = {
            datasets: [
              {
                data: responseData.deldata,
              },
            ],
          };
        })
        .catch((e) => {
          console.log("Error "+e)
          // this.errors.push(e);
        });
    },
    dashboard_secondpiedata(vid) {
      dashboard
        .getsecondpiechart(this.vid)
        .then((response) => {
          const responseData = response.data;
          this.chartdatapie = {
            datasets: [
              {
                data: responseData.piedata,
                
              },
            ],
          };
        })
        .catch((e) => {
          this.errors.push(e);
        });
    },
  },
};
</script>
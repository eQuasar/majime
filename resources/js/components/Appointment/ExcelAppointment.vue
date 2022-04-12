<template>
    <b-container fluid>
        <div class="header_title">
            <div class="header_inner">
                <h2><strong>Download Appointments in Excel Form</strong></h2>
            </div>
        </div>
        <div class="clear">&nbsp;</div>
        <b-row>
            <b-col xl="12" lg="12" md="12">
                <div class="list-appointments content_bar card">
                    <div class="card-body">
                        <b-row>
                            <b-col>
                                <h4 class="own-heading">Today’s Appointments</h4>
                            </b-col>
                            <b-col>
                                <button type="button" class="download-btn btn btn-primary" v-on:click="download">Download</button>
                            </b-col>
                        </b-row>
                        <div class="call-center-dashboard">
                          <b-row class="revers-block">
                              <b-col xl="5" lg="5" md="5">
                                  <b-form-group class="mb-0"> Show <b-form-select id="per-page-select" v-model="perPage" :options="pageOptions" size="sm"></b-form-select> entries </b-form-group>
                              </b-col>
                              <b-col xl="7" lg="7" md="7" class="search_field">
                                  <b-form-input id="filter-input" v-model="filter" type="search" placeholder="Type to Search"></b-form-input>
                                  <b-input-group-append>
                                      <b-button :disabled="!filter" @click="filter = ''">Clear</b-button>
                                  </b-input-group-append>
                              </b-col>
                          </b-row>
                        </div>
                        <div class="groomer-page">
                          <b-table
                              striped
                              hover
                              responsive
                              :items="items"
                              :sort-by.sync="sortBy"
                              :sort-desc.sync="sortDesc"
                              sort-icon-left
                              :filter-included-fields="filterOn"
                              :filter="filter"
                              :fields="fields"
                              :per-page="perPage"
                              :current-page="currentPage"
                              @row-clicked="myRowClickHandler"
                              show-empty
                          >
                              <template #empty="scope">
                                <p style="text-align: center;">No record found, choose date filter to found the result.</p>
                              </template>
                               <template v-slot:cell(sr)="row">
                                {{((currentPage-1)*perPage)+(row.index)+1}}
                              </template>
                          </b-table>
                        </div>
                        <div class="text-center" v-if="seen">
                            <b-spinner variant="primary" label="Text Centered"></b-spinner>
                        </div>
                        <b-pagination v-model="currentPage" :total-rows="rows" :per-page="perPage" aria-controls="my-table"></b-pagination>
                    </div>
                </div>
            </b-col>
        </b-row>
    </b-container>
</template>

<script>
    import { BTable } from 'bootstrap-vue';
    import * as XLSX from 'xlsx/xlsx.mjs';
    import timeslot from "../../api/timeslot.js";
    import appointment from "../../api/appointment.js";
    export default {
        components: {
          BTable
        },
        mounted() {
            this.gettimeslot();

            this.excelappointment();
        },
        data() {
            return {
                ariaDescribedby: "",
                time: "",
                date: "",
                time_slots: [],
                seen: false,
                vehicle_assign: 0,
                vehicle_assign_array: [],
                successful: false,
                vehicle_id: 0,
                appointment_id: 0,
                create_error: "",
                date_from: "",
                date_to: "",
                sortBy: "date",
                sortDesc: true,
                perPage: 10,
                currentPage: 1,
                pageOptions: [5, 10, 15, 20, 50, 100],
                filter: null,
                filterOn: [],
                fields: [
                    {
                        key: "sr",
                        sortable: true,
                    },
                    {
                        key: "client_name",
                        label: "Client Name",
                        sortable: true,
                    },
                    {
                        key: "client_email",
                        label: "Client Name",
                        sortable: true,
                    },
                    {
                        key: "client_phone",
                        label: "Client Phone",
                        sortable: true,
                    },
                    {
                        key: "client_address",
                        label: "Client Address",
                        sortable: true,
                    },
                    {
                        key: "client_gender",
                        label: "Client Gender",
                        sortable: true,
                    },
                    {
                        key: "client_dob",
                        label: "Client DOB",
                        sortable: true,
                    },
                    {
                        key: "client_alternate_address",
                        label: "Client Alternate Address",
                        sortable: true,
                    },
                    {
                        key: "client_alternate_phone",
                        label: "Client Alternate Phone",
                        sortable: true,
                    },
                    {
                        key: "groomer_name",
                        label: "Groomer Name",
                        sortable: true,
                    },
                    {
                        key: "groomer_email",
                        label: "Groomer Email",
                        sortable: true,
                    },
                    {
                        key: "groomer_phone",
                        label: "Groomer Phone",
                        sortable: true,
                    },
                    {
                        key: "vehicle_type",
                        label: "Vehicle Type",
                        sortable: true,
                    },
                    {
                        key: "vehicle",
                        label: "Vehicle Name",
                        sortable: true,
                    },
                    {
                        key: "vehicle_number",
                        label: "Vehicle Number",
                        sortable: true,
                    },
                    {
                        key: "pet_name",
                        label: "Pet Name",
                        sortable: true,
                    },
                    {
                        key: "pet_breed",
                        label: "Pet Breed",
                        sortable: true,
                    },
                    {
                        key: "pet_dob",
                        label: "Pet DOB",
                        sortable: true,
                    },
                    {
                        key: "pet_aggresive",
                        label: "Pet Aggresive",
                        sortable: true,
                    },
                    {
                        key: "pet_coat_level",
                        label: "Pet Coat level",
                        sortable: true,
                    },
                    {
                        key: "services",
                        label: "Services",
                        sortable: true,
                    },
                    {
                        key: "status",
                        label: "Status",
                        sortable: true,
                    },
                    {
                        key: "total_cost",
                        label: "Total Cost",
                        sortable: true,
                    }
                ],
                items: [],
                items2: [],
                myitems: [],
                errors_create: [],
                successful: false,
                create_error: "",
                uid:0,
            };
        },
        computed: {
            rows() {
                return this.items.length;
            },
        },
        methods: {
            download : function() {
                const data = XLSX.utils.json_to_sheet(this.items2)
                const wb = XLSX.utils.book_new()
                XLSX.utils.book_append_sheet(wb, data, 'data')
                XLSX.writeFile(wb,'report_pupping.xlsx')
            },
            myRowClickHandler(record, index) {
                // 'record' will be the row data from items
                // `index` will be the visible row number (available in the v-model 'shownItems')
                console.log(record.id); // This will be the item data for the row
                this.$router.push({ name: 'bookingdetail', params: { appointmentid: (record.id).toString() }});
            },
            gettimeslot() {
                let formData = new FormData();
                formData.append("type", "get_active");
                timeslot
                    .addTimeSlot(formData)
                    .then((response) => {
                        this.time_slots = response.data.data;
                    })
                    .catch((error) => {
                        console.log(error);
                        if (error.response.status == 422) {
                            this.errors_create = error.response.data.errors;
                        }
                    });
            },
            excelappointment(address) {
                let formData = new FormData();
                formData.append("uid", this.$userId);
                this.seen = true;
                appointment
                    .excelAppointments(formData)
                    .then((response) => {
                        this.items2 = response.data.data;
                        this.items = response.data.data2;
                        // this.countrys = response.data.data;
                        this.seen = false;
                    })
                    .catch((response) => {
                        this.successful = false;
                        alert("something went wrong");
                    });
            },
        },
    };
</script>
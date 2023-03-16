<template>
  <b-container fluid>
    <div class="header_title">
      <div class="header_inner">
        <b-row>
          <b-col xl="4" lg="4" md="4">
            <h3><strong>HSN Detail</strong></h3>
            <br />
          </b-col>
        </b-row>
      </div>
    </div>
    <b-button pill variant="info" style="float: right" @click="goToCreate()"
      >Enter HSN Detail</b-button>
      <b-button pill variant="info" style="float: right" @click="first()"
      >Download</b-button>
    <div class="content_bar">
      <div class="card-body card">
        <div class="call-center-dashboard">
          <b-row>
            <b-col xl="6" lg="" md="6">
              <b-alert show variant="danger" v-if="create_error">{{
                create_error
              }}</b-alert>
            </b-col>
          </b-row>
        </div>
      </div>
      <div class="blue-bar"></div>
      <div class="card list-appointments space-bottom">
        <div class="col-sm-12">
          <b-row>
            <b-col xl="4" lg="4" md="4">
              <b-form-group class="mb-0">
                Show
                <b-form-select
                  id="per-page-select"
                  v-model="perPage"
                  :options="pageOptions"
                  size="sm"
                ></b-form-select>
                entries
              </b-form-group>
            </b-col>
            <b-col xl="8" lg="8" md="8" class="search_field">
              <b-form-input
                id="filter-input"
                v-model="filter"
                type="search"
                placeholder="Type to Search"
              >
              </b-form-input>
              <b-input-group-append>
                <b-button :disabled="!filter" @click="filter = ''"
                  >Clear</b-button
                >
              </b-input-group-append>
            </b-col>
          </b-row>
        </div>
      </div>

      <div class="text-center" v-if="seen">
        <b-spinner variant="primary" label="Text Centered"></b-spinner>
      </div>
      <b-table
        striped
        hover
        responsive
        :items="items"
        :filter-included-fields="filterOn"
        :filter="filter"
        :fields="fields"
        :per-page="perPage"
        :current-page="currentPage"
        show-empty
      >
        <template #empty="scope"> No result found. </template>
        <template v-slot:cell(sr)="row">
          {{ (currentPage - 1) * perPage + row.index + 1 }}
        </template>
        <!-- <template v-slot:cell(id)="row"> #{{ row.item.id }} </template> -->
        <template v-slot:cell(hsn_code)="row">
          <span> {{ row.item.hsn_code}}</span>
        </template>
        <template v-slot:cell(slab_1)="row">
          <span> {{row.item.slab_1}}</span>
        </template> 
        <template v-slot:cell(slab_2)="row">
          <span>{{ row.item.slab_2}}</span>
        </template>
        <template v-slot:cell(slab_amount)="row">
          <span>{{ row.item.slab_amount }}</span>
        </template>
        <template v-slot:cell(description)="row">
          <span>{{ row.item.description}}</span>
        </template>
        <!-- <template v-slot:cell(action)="row">
          <router-link
            :to="{
              name: 'LRView',
              params: { id: row.item.uid.toString() },
            }"
            ><b-icon icon="eye-fill" aria-hidden="true"></b-icon
          ></router-link>
          &nbsp;&nbsp;
        </template> -->
      </b-table>
      <b-pagination
        v-model="currentPage"
        :total-rows="rows"
        :per-page="perPage"
        aria-controls="my-table"
      >
      </b-pagination>
    </div>
  </b-container>
</template>
<script>
import hsn from "../../api/hsn.js";
import * as XLSX from "xlsx/xlsx.mjs";


export default {
  props: {},
  mounted() {
    //this.getVidz();
    this.getHsnDetail();
  },
  data() {
    return {
      vendor: null,
      status_assign: "",
      key: "",
      oid: 0,
      selected: null,
      options: [{ value: null, text: "Vendor Wise Detail" }],
      ariaDescribedby: "",
      time: "",
      date: "",
     

      vid: 0,
      time_slots: [],
      status_assign_array: [],
      seen: false,
      date_from: "",
      city: "",
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
          label: "S No.",
          sortable: true,
        },
        {
          key: "hsn_code",
          label: "HSN Detail",
          sortable: true,
        },
        {
          key: "slab_1",
          label: "Slab 1",
          sortable: true,
        },
        {
          key: "slab_2",
          label: "Slab 2 ",
          sortable: true,
        },
        {
          key: "slab_amount",
          label: "Amount",
          sortable: true,
        },
        {
          key: "description",
          label: "Description",
          sortable: true,
        },

        // {
        //   key: 'action',
        //   label: 'Action',
        //   sortable: false
        // }
      ],
      items: [],
      errors_create: [],
      successful: false,
      create_error: "",
    };
  },
  computed: {
    rows() {
      return this.items.length;
    },
  },
  methods: {
    getHsnDetail(vid) {
      hsn
        .getHsn()
        .then((response) => {
          console.log(response);
          this.items = response.data.data;
          // this.countrys = response.data.data;
          console.log(this.items);
          //this.seen = false;
        })
        .catch((response) => {
          this.successful = false;
          alert("something went wrong");
        });
    },
      first: function () {
      const data = XLSX.utils.json_to_sheet(this.items);
      const wb = XLSX.utils.book_new();
      /* fix headers */
      XLSX.utils.sheet_add_aoa(data, [["HSN Detail","Slab 1","Slab 2","Amount","Description"]], { origin: "A1" });  
      XLSX.utils.book_append_sheet(wb, data, "data");
      XLSX.writeFile(wb, "hsn_masterdownload.xlsx");
    },
    goToCreate() {
      this.$router.push("/admin/HsnDeatil");
    },
  },
};
</script>

<template>
  <!-- <b-alert show variant="danger" v-if='create_error'>{{create_error}}</b-alert>
  <div :class="['form-group m-1 p-3', (successful ? 'alert-success' : '')]" v-show="successful">
    <span ="successful" class="label label-sucess">File Uploaded Sucessfully</span>
  <div> -->
    <div class="card">
      <div class="card-body">
    <form @submit.prevent="uploadFile">
      <div class="mb-3">
        <label for="formFileMultiple" class="form-label" ref="file-input">Choose File</label></label>
        <input class="form-control" type="file" id="formFileMultiple" multiple  @change="onFileChange" >
      </div>
      <!-- <input type="file" name="file" @change="onFileChange"> -->
      <br>
      <b-button pill variant="primary" type="submit" style="float: left;">Submit</b-button>
      <!-- <button type="submit" style="float: left;" >Submit</button> -->
      <b-button pill variant="outline-danger"  input type="reset" value="Reset" style="float: left;">Reset</b-button>
      <!-- <b-button pill variant="outline-danger"  @click="clearFiles" style="float: left;">Reset</b-button> -->
      <b-button pill variant="outline-success"  @click="downloadFile" style="float: left;">Download</b-button>
     
    </form>
  </div>


  <!-- <div class="content_bar"> -->
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
       striped hover
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
        show-empty
        class="tbl-blk"
      >
        <template #empty="scope">
          <p style="text-align: center">
            No record found, choose date filter to found the result.
          </p>
        </template>
      </b-table>
      <b-pagination
      v-model="currentPage"
      :total-rows="rows"
      :per-page="perPage"
      aria-controls="my-table"
    >
    </b-pagination>
  <!-- <table class="table table-hover">
    <thead>
      <tr>
        <th scope="col">S.no</th>
        <th scope="col">Product Id</th>
        <th scope="col">HSN No.</th>
        <th scope="col">Weight</th>
        <th scope="col">Categories</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="item in items" :key="item.id">
         <th scope="row">1</th> -->
        <!-- <td>{{ product_id }}</td>
        <td>{{ hsn }}</td>
        <td>{{ weight }}</td>
        <td>{{ categories }}</td>
      </tr>
    </tbody>
  </table> -->
</div>
</div>

</template>

<script>
import axios from 'axios';
import product from "../../api/Product.js";
import * as XLSX from 'xlsx/xlsx.mjs';
import Swal from 'sweetalert2'
export default {
  data() {
    return {
      file: null,
      vid: 0,
      items:[],
      sortBy: 'date',
      sortDesc: true,
      perPage: 10,
      currentPage: 1,
      pageOptions: [5, 10, 15, 20, 50, 100],
      filter: null,
      filterOn: [],
    };
  },
  mounted() {
    // Make an API request to fetch the data
    this.fetchData();
  },
  data() {
    return {
      sortDesc: true,
      vid: 0,
      perPage: 10,
      sortBy: "date",
      seen:false,
      row:"",
      currentPage: 1,
      pageOptions: [5, 10, 15, 20, 50, 100],
      filter: null,
      filterOn: [],
      fields: [
        {
          key: "product_id",
          label: "Product Id",
          sortable: true,
        },
        {
          key: "cost",
          label: "Cost",
          sortable: true,
        },

        {
          key: "hsn_code",
          label: "HSN Code",
          sortable: true,
        },
        {
          key: "weight",
          label: "Weight",
          sortable: true,
        },
        {
          key: "categories",
          label: "Categories",
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
        return this.items.length
        //  return this.items2.length
      }
    },
  methods: {
    paymentSuccessful () {
    Swal.fire({
      icon: 'success',
      title: 'File Success!',
      text: 'Excelsheet Uploaded .',
      confirmButtonText: 'OK'
    })
  },
    fetchData() {
      this.vid = JSON.parse(localStorage.getItem("ivid"));
      let formData = new FormData();
      formData.append('vid',this.vid);
      product
        .get_import_data(formData)
        .then((response) => {
          console.log(response);
          this.items = response.data;
          this.hsndetailfile();
          this.successful = true;
            this.error = true;
          console.log(this.items);
          this.$router.push({name: 'import_file'});
          //this.seen = false;
        })
        .catch((response) => {
          this.successful = false;
          alert("something went wrong");
        });
    },
    hsndetailfile(vid) {
    this.vid = JSON.parse(localStorage.getItem("ivid"));
    let formData= new FormData();
    formData.append("vid",this.vid);
    product.get_import_data(formData)
     .then(( response ) => {
        this.items=response.data;
        console.log(this.items);
      })
      .catch(response => {
          this.successful = false;
          alert('something went wrong');
      })
  },
    onFileChange(event) {
      this.file = event.target.files[0];
    },
    uploadFile() {
      this.vid = JSON.parse(localStorage.getItem("ivid"));
      const formData = new FormData();
      formData.append('vid', this.vid);
      formData.append('file', this.file);
      // product.get_import_data(formData)
      // axios.post('https://majimedev.isdemo.in/api/v1/import_product_info', formData).then((response) => {
        product.import_data(formData)
     .then(( response ) => {
        this.paymentSuccessful ();
        setTimeout(function () {
            // window.location.href = "/admin/import/data";
            // this.$router.push({name: 'import_file'});
            this.$router.push("/admin/import/data").catch(()=>{});
          }, 2000);
        console.log(response.data);

      
      });
    },
    downloadFile() {
                  const data = XLSX.utils.json_to_sheet(this.items);
                const wb = XLSX.utils.book_new()
                XLSX.utils.book_append_sheet(wb, data, 'data')
                XLSX.writeFile(wb,'product details.xlsx')
    },
    clearFiles() {
      this.form.reset();
      // this.clearFiles();
    },
  },
};
</script>

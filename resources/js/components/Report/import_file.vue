<!-- <template>
  <b-container fluid>
    <div>
      <div class="card-body">
        <table class="table">
          <tr>
            <a href="" class="btn btn-danger">Download</a>
            <label class="btn btn-success"
              >Select File
              <input
                style="diplay: none"
                type="file"
                name="select_users_file"
                class="btn btn-danger"
              />
            </label>
            <label class="btn btn-success"
              >Upload File
              <input
                style="diplay: none"
                type="submit"
                name="upload"
                @click.prevent="save_file"
              />
            </label>
          </tr>
        </table>
      </div>
    </div>
  </b-container>
</template> -->
<!-- <template> -->
<!-- <div> -->
<!-- Styled -->
<!-- <b-form-file
      v-model="file"
      :state="Boolean(file)"
      placeholder="Choose a file or drop it here..."
      drop-placeholder="Drop file here..."
    ></b-form-file> -->
<!-- <div class="mt-3">Selected file: {{ file ? file.name : "" }}</div> -->
<!-- Plain mode -->
<!-- <b-form-file v-model="file" class="mt-3" plain></b-form-file>
  </div>
</template> -->

<template>
  <div>
      <!-- Styled -->
      <b-form-file
        v-model="file"
        :state="Boolean(file)"
        placeholder="Choose a file or drop it here..."
        drop-placeholder="Drop file here..."
      ></b-form-file>
      <div class="mt-3">Selected file: {{ file ? file.name : '' }}</div>
    <!-- <input type="file" ref="fileInput" v-model="file" /> -->
    <button @click="submitData">Submit</button>
  </div>
</template>

<!-- <template>
  <div>
    <b-form-file v-model="file" ref="file-input" class="mb-2"></b-form-file>
    <p class="mt-2">
      Selected file: <b>{{ file ? file.name : "" }}</b>
    </p>
    <b-button @click="clearFiles" class="mr-2">Reset</b-button>
    <b-button @click="submit" class="mr-2">submit </b-button>
  </div>
</template> -->

<script>
import axios from "axios";
export default {
  props: {},
  mounted() {
    // if (this.$route.query.vid) {
    //   this.getVendor();
    // } else {
    //   this.getVendor();
    // }
  },
  data() {
    return {
      // File:null,
      file: null,
      fields: [],
      file:'',
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

submitData() {
    const formData = new FormData();
    formData.append('file', this.file);

    axios.post('https://majimedev.isdemo.in/api/v1/import_product_info',this.formData)
      .then(response => {
        console.log(response.data);
      })
      .catch(error => {
        console.log(error);
      });
    },


    clearFiles() {
      this.$refs["file-input"].reset();
    },
  },
};
</script>

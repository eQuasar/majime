<html>

<head>
    <title>Laravel Phone Number Authentication - eQuasar Solutions</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    </script>
</head>

<body>
    <div class="main-container">
        <div class="heading marg-bottom">
            <h1>Welcome to Pupping</h1>
            <p>All the services are performed inside our state of the art mobile grooming van.</p>
        </div>
        <div id="login_box">
            <div class="alert alert-danger" id="error" style="display: none;"></div>
            <div class="send_otp_page" id="send_otp">
                <div class="main_otp">
                    <div class="alert alert-success" id="sentSuccess" style="display: none;"></div>
                    <form>
                        <!-- <label>Phone Number:</label> -->
                        <input type="text" id="name" name="name" class="form-control" placeholder="Name">
                        <input type="text" id="number" name="phone_number" class="form-control" placeholder="Phone number">
                        <input type="text" id="email" name="email" class="form-control" placeholder="Email Address">
                        <div class="row">
                            <div class="col-sm-6">
                                <select name="gender" id="gender">
                                    <option value="" selected>Choose Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <input type="date" name="dob" id="dob" placeholder="mm/dd/yyyy">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <select name="country_id" id="country_id">
                                    <option value="101" selected>India</option>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <select name="state_id" id="state_id">
                                    <option value="32" selected>Punjab</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <select name="city_id" id="city_id">
                                    <option value="3225" selected>Ludhiana</option>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <select name="area_id" id="area_id">
                                    <option value="" selected>Choose Area</option>
                                    <option value="1">Ludhiana East</option>
                                    <option value="2">Ludhiana West</option>
                                    <option value="4">Ludhiana North</option>
                                    <option value="5">Ludhiana South</option>
                                </select>
                            </div>
                        </div>
                        <input type="text" id="pincode" name="pincode" class="form-control" placeholder="Pincode">
                        <textarea name="address" id="address" placeholder="Address"></textarea>
                        <button type="button" class="btn btn-success" id="sendotp">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <style type="text/css">
        .send_otp_page select,  .send_otp_page textarea{
            width: 100%;
            float: left;
            border-radius: 5px !important;
            margin: 10px 0px;
              margin-bottom: 10px;
            background: transparent;
            color: #8b8b8b;
            font-size: 16px;
            font-weight: 300;
            text-align: left;
            padding: 14px 20px;
            border: 1px solid;
            margin-bottom: 20px;
        }
    </style>
    <script type="text/javascript">
    $(document).ready(function() {
        $("#sendotp").click(function() {
            var phone_number = $("#number").val();
            if ($.trim(phone_number) == "") {
                $("#error").text("Please input your mobile number");
                $("#error").show();
            } else {
                sendNewOTP();
            }
        });

        function sendNewOTP() {
            $.ajax({
                    url: 'registerUser',
                    type: 'POST',
                    data: { phone: $("#number").val(), name: $("#name").val(), email: $("#email").val(), country_id: $("#country_id").val(), state_id: $("#state_id").val(), city_id: $("#city_id").val(), gender: $("#gender").val(), dob: $("#dob").val(), area_id: $("#area_id").val(), pincode: $("#pincode").val(), address: $("#address").val() },
                })
                .done(function(res) {
                    // res = JSON.parse(res);
                    // alert(res.otp); /* TO be deleted on production */
                    if (res.ErrorCode === undefined || res.ErrorCode == -1) {
                        $("#error").text("Something went wrong");
                        $("#error").show().delay(3000).hide("slow");;
                    } else if (res.ErrorCode == -2) {
                        $("#error").text(res.msg);
                        $("#error").show().delay(3000).hide("slow");;
                    } else {
                        $("#sentSuccess").text(res.msg);
                        $("#sentSuccess").show().delay(5000).hide("slow");
                        window.location.href = "{{ route('checkLogin')}}";
                    }
                })
                .fail(function() {
                    $("#error").text("Something went wrong");
                    $("#error").show().delay(3000).hide("slow");;
                });
        }
    });

    </script>
    <link href="{{ asset('css/user.css') }}" rel="stylesheet">
</body>
</html>

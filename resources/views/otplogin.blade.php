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
            <h1>Welcome Back</h1>
            <p>All the services are performed inside our state of the art mobile grooming van.</p>
        </div>
        <div id="login_box">
            <div class="alert alert-danger" id="error" style="display: none;"></div>
            <div class="send_otp_page" id="send_otp">
                <div class="main_otp">
                    <div class="alert alert-success" id="sentSuccess" style="display: none;"></div>
                    <form>
                        <!-- <label>Phone Number:</label> -->
                        <input type="text" id="number" class="form-control" placeholder="Phone number">
                        <br />
                        <button type="button" class="btn btn-success" id="sendotp">Send OTP</button>
                    </form>
                </div>
            </div>
            <div class="send_otp_page" id="verify_otp" style="display: none;">
                <div class="">
                    <div class="alert alert-success" id="successRegsiter" style="display: none;"></div>
                    <form>
                        <input type="hidden" name="ph_number" id="ph_number">
                        <input type="text" id="otp" class="form-control" placeholder="Enter OTP">
                        <div class="row">
                            <div class="col-6">
                                <button type="button" class="btn btn-success" id="verifyotp">Verify OTP</button>
                            </div>
                            <div class="col-6">
                                <a href="javascript:void(0);" class="link-primary btn-success" id="resend_otp">Resend OTP</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
    $(document).ready(function() {
        $("#sendotp").click(function() {
            var phone_number = $("#number").val();
            if ($.trim(phone_number) == "") {
                $("#error").text("Please input your mobile number");
                $("#error").show();
            } else {
                sendNewOTP(phone_number);
            }
        });

        $("#resend_otp").click(function() {
            var phone_number = $("#ph_number").val();
            sendNewOTP(phone_number);
        });
        $("#verifyotp").click(function() {
            var phone_number = $("#ph_number").val();
            var otp = $("#otp").val();
            verifyOTP(phone_number, otp);
        });

        function verifyOTP(phone_number, otp) {
            $.ajax({
                    url: 'verifyOTP',
                    type: 'POST',
                    data: { number: phone_number, otp: otp },
                })
                .done(function(res) {
                    res = JSON.parse(res);
                    console.log(res);
                    if (res.ErrorCode === undefined || res.ErrorCode == -1) {
                        $("#error").text("Something went wrong");
                        $("#error").show().delay(3000).hide("slow");;
                    } else if (res.ErrorCode == -2) {
                        $("#error").text(res.msg);
                        $("#error").show().delay(3000).hide("slow");;
                    } else {
                        $("#successRegsiter").text(res.msg);
                        $("#successRegsiter").show().delay(5000).hide("slow");
                        window.location.href = "{{ route('checkLogin')}}";
                        /************ Redirect code *************

                                    Your Code Goes Here

                         ************ Redirect code *************/
                    }
                })
                .fail(function() {
                    $("#error").text("Something went wrong");
                    $("#error").show().delay(3000).hide("slow");;
                });
        }

        function sendNewOTP(phone_number) {
            $.ajax({
                    url: 'sendOTP',
                    type: 'POST',
                    data: { number: phone_number },
                })
                .done(function(res) {
                    res = JSON.parse(res);
                    // alert(res.otp); /* TO be deleted on production */
                    if (res.ErrorCode === undefined || res.ErrorCode == -1) {
                        $("#error").text("Something went wrong");
                        $("#error").show().delay(3000).hide("slow");;
                    } else {
                        $("#send_otp").hide("slow");
                        $("#verify_otp").show("slow"); 
                        $("#successRegsiter").text("A One Time Password "+ res.otp +" has been send to: " + phone_number); /* TO be deleted on production */    
                        
                        $("#successRegsiter").show().delay(5000).hide("slow");
                        $("#ph_number").val(phone_number);
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

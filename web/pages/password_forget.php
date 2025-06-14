<div class="container" id="container" style="padding-top: 88px; min-height:100vh;">
    <div class="center">
        <form id="resetForm" method="post" action="../pages/password_lookup.php" enctype="multipart/form-data">
            <h1 class="display-5 font-weight-bold text-center text-coekku">Forget Password <i class="fas fa-question"></i></h1>
            <div class="card">
                <!--Body-->
                <div class="card-body mb-1">
                    <h6 class="text-center">ส่งคำร้องรีเซ็ตรหัสผ่าน</h6>
                    <?php if (isset($_SESSION['error'])) {echo '<div class="alert alert-danger" role="alert">'. $_SESSION['error'] .'</div>'; $_SESSION['error'] = null;} ?>
                    <div class="md-form form-sm">
                        <i class="fas fa-users prefix"></i>
                        <input type="email" name="reset" id="reset"
                            class="form-control form-control-sm validate mb-2" required placeholder="กรุณาใส่ E-Mail ใช้ในการสมัครเพื่อรีเซ็ตรหัสผ่าน">
                        <label for="reset">รีเซ็ตรหัสผ่าน</label>
                    </div>
                    <div class="h-captcha text-center" data-sitekey="d9826c31-b8d7-4648-b04f-c5595ffb8c22"></div>
                    <button class="btn btn-coekku btn-block btn-md" type="submit" name="resetPassword" value="รีเซ็ตรหัสผ่าน">รีเซ็ตรหัสผ่าน</button>
                    <div class="text-center"><a href="../login/" class="text-danger">ล็อกอิน</a> | <a href="../register/">สมัครเข้าใช้งาน</a></div>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    document.querySelector("#resetForm").addEventListener("submit", function(event) {
        var hcaptchaVal = document.querySelector('[name="h-captcha-response"]').value;
        if (hcaptchaVal === "") {
            event.preventDefault();
            swal("Oops","Please complete captcha!", "error");
        }
    });
</script>
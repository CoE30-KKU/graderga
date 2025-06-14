<?php if (!isset($_SESSION['allowAccessResetpasswordPage']) || $_SESSION['allowAccessResetpasswordPage'] == false) back(); ?>
<div class="container" id="container" style="padding-top: 88px; min-height:100vh;">
    <div class="center">
        <form method="post" action="../pages/password_resetpass.php" enctype="multipart/form-data">
            <h1 class="display-5 font-weight-bold text-center text-coekku">Setting New Password <i class="fas fa-lock"></i></h1>
            <div class="card">
                <!--Body-->
                <div class="card-body mb-1">
                    <h6 class="text-center">ตั้งค่ารหัสผ่านใหม่</h6>
                    <?php if (isset($_SESSION['error'])) {echo '<div class="alert alert-danger" role="alert">'. $_SESSION['error'] .'</div>'; $_SESSION['error'] = null;} ?>
                    <div class="md-form form-sm mb-5">
                        <i class="fas fa-key prefix"></i>
                        <input type="password" name="setNewPassword" id="setNewPassword"
                            class="form-control form-control-sm validate" required>
                        <label for="setNewPassword">รหัสผ่านใหม่</label>
                    </div>
                </div>
                <!--Footer-->
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <div class="align-self-center">
                            <a href="../login/" class="text-danger">เข้าสู่ระบบหรอ?</a> หรือ <a href="../register/">สมัครเข้าใช้งานที่นี่!</a>
                        </div>
                        <div>
                            <input class="btn btn-success btn-md mr-0 ml-0" type="submit" name="resetPassword" value="รีเซ็ต"></input>
                            <a class="btn btn-danger btn-md mr-0 ml-0" href="../home/">ยกเลิก</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
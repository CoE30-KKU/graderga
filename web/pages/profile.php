<?php

    $user = -1;
    if (isset($_GET['id'])) {
        $user = new User((int) $_GET['id']);    
        if ($user->getID() == -1) header("Location: ../home/");
    } else if (isLogin()) {
        $user = $_SESSION['user'];
    } else {
        header("Location: ../home/");
    }
?>
<div class="container" style="padding-top: 88px;">
    <div class="container mb-3" id="container">
    <form method="POST" action="../pages/profile_save.php" enctype="multipart/form-data" id="userFormEdit">
        <input type="hidden" name="id" value="<?php echo $user->getID(); ?>"/>
        <div class="row">
                <div class="col-12 col-md-4">
                    <div class="card mb-3">
                        <div class="card-body">
                            <img src="<?php echo $user->getProfile(); ?>" class="card-img-top img-fluid mb-3" alt="Profile" id="profile_preview">
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="profile_upload" id="profile_upload" aria-describedby="profile_upload" accept="image/*"/>
                                    <input type="hidden" name="real_profile_url" id="real_profile_url" value="<?php echo $user->getProfile(); ?>"/>
                                    <label class="custom-file-label" for="profile_upload">Choose file</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" id="save" name="save" class="btn btn-coekku btn-block">Save</button>
                </div>
                <div class="col-12 col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <!-- Personal Zone -->
                            <h4 class="font-weight-bold">ข้อมูลส่วนตัว - Information <i
                                        class="fas fa-info-circle"></i></h4>
                                <hr>
                                <!-- name -->
                                <div class="md-form input-group mt-0 mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text md-addon font-weight-bold">ชื่อที่แสดง</span>
                                    </div>
                                    <input type="text" class="form-control mr-sm-3" id="name" name="name"
                                        placeholder="<?php echo $user->getName(); ?>"
                                        value="<?php echo $user->getName(); ?>">
                                </div>
                                <!-- name -->
                                <!-- Security Zone -->
                                <h4 class="mt-5 font-weight-bold">ความปลอดภัย - Security <i class="fas fa-lock"></i>
                                </h4>
                                <hr>
                                <!-- Email -->
                                <div class="md-form input-group mt-0 mb-0">
                                    <div class="input-group-prepend mt-0 mb-0">
                                        <span class="input-group-text md-addon font-weight-bold">อีเมล</span>
                                    </div>
                                    <input type="hidden" id="real_email" name="real_email" value="<?php echo $user->getEmail(); ?>">
                                    <input type="text" class="form-control mr-sm-3" id="email" name="email"
                                        placeholder="<?php echo $user->getEmail(); ?>"
                                        value="<?php echo $user->getEmail(); ?>" required>
                                </div>
                                <!-- Email -->
                                <!-- Password -->
                                <div class="md-form input-group mt-0 mb-0">
                                    <div class="input-group-prepend mb-0">
                                        <span class="input-group-text md-addon font-weight-bold">เปลี่ยนรหัสผ่าน</span>
                                    </div>
                                </div>
                                <div class="md-form input-group mb-0 mt-0">
                                    <div class="input-group-prepend mb-0 mt-0">
                                        <span class="input-group-text md-addon mb-0 mt-0 ml-5">รหัสผ่านใหม่</span>
                                    </div>
                                    <input type="password" class="form-control mr-sm-3" id="password" name="password" value="">
                                </div>
                                <div class="md-form input-group mb-0 mt-0">
                                    <div class="input-group-prepend mb-0 mt-0">
                                        <span class="input-group-text md-addon mb-0 mt-0">รหัสผ่านใหม่อีกครั้ง</span>
                                    </div>
                                    <input type="password" class="form-control mr-sm-3" id="newPassword" name="newPassword" value="">
                                </div>
                                <small id="al" class="text-danger text-center" style="display: none;">Password doesn't match</small>
                                <!-- Password -->
                                <!-- Security Zone -->
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div id="uploadimageModal" class="modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Upload & Crop Image</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col text-center">
                        <div id="image_demo" style="width:100%; margin-top:30px"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success crop_image">Crop & Upload Image</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function () {
    $image_crop = $('#image_demo').croppie({
        enableExif: true,
        viewport: {
            width: 325,
            height: 325,
            type: 'square' //circle
        },
        boundary: {
            width: 333,
            height: 333
        }
    });

    $('#profile_upload').on('change', function () {
        var reader = new FileReader();
        reader.onload = function (event) {
            $image_crop.croppie('bind', {
                url: event.target.result
            }).then(function () {
                console.log('jQuery bind complete');
            });
        }
        reader.readAsDataURL(this.files[0]);
        $('#uploadimageModal').modal('show');
    });

    $('.crop_image').click(function (event) {
        $image_crop.croppie('result', {
            type: 'canvas',
            size: 'viewport'
        }).then(function (response) {
            $.ajax({
                url: "../pages/profile_upload.php",
                type: "POST",
                data: {
                    "userID": <?php echo $user->getID(); ?>,
                    "image": response
                },
                success: function (data) {
                    $('#uploadimageModal').modal('hide');
                    $('#profile_preview').attr('src',data);
                    $('#real_profile_url').val(data);
                    console.log($('#real_profile_url').val());
                }
            });
        })
    });

});
</script>
<script type="text/javascript">
    $(function () {
        var editor = editormd("editormd", {
            width: "100%",
            height: "500",
            path: "../vendor/editor.md/lib/",
            theme : "<?php if (isDarkmode()) echo "dark"; else echo "default"; ?>",
            previewTheme : "<?php if (isDarkmode()) echo "dark"; else echo "default"; ?>",
            editorTheme : "<?php if (isDarkmode()) echo "monokai"; else echo "default"; ?>",
            emoji: true,
            toolbarIcons : function() {
                return [
                    "undo", "redo", "|",
                    "bold", "del", "italic", "quote", "|",
                    "h1", "h2", "h3", "h4", "h5", "h6", "|",
                    "list-ul", "list-ol", "hr", "|",
                    "link", "reference-link", "image", "code", "preformatted-text", "code-block", "table", "emoji", "|",
                    "watch", "preview", "search", "|",
                    "help", "info"
                ];
            }
        });
    });
</script>
<script>
    var pw = document.getElementById("password");
    var pwc = document.getElementById("newPassword");
    var al = document.getElementById("al");
    var validPassword = true;
    pwc.addEventListener("change", function() {
        if (pw.value === "") {    
            pw.classList.remove("invalid");
            pwc.classList.remove("invalid");
            pw.classList.remove("valid");
            pwc.classList.remove("valid");
            al.style.display = "none";
            validPassword = true;
        } else {
            if (pw.value != pwc.value) {
                pw.classList.add("invalid");
                pwc.classList.add("invalid");
                pw.classList.remove("valid");
                pwc.classList.remove("valid");
                al.style.display = "block";
                validPassword = false;
            } else {
                pw.classList.add("valid");
                pwc.classList.add("valid");
                pw.classList.remove("invalid");
                pwc.classList.remove("invalid");
                al.style.display = "none";
                validPassword = true;
            }
        }
    });
    document.querySelector("#userEditForm").addEventListener("submit", function(event) {
        if (!validPassword) {
            event.preventDefault();
            swal("Oops","Please check your password and try again", "error");
        }
    });
</script>

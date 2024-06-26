<?php needAdmin(); 
    $probName = "";$probCodename = "";$probScore = "";$probRate = "";$probTime = "";$probMemory = ""; $id = -1; $accept = ""; $hide = 0; $last_hide_updated = time();
    if (isset($_GET['id'])) {
        $id = (int) $_GET['id'];
        if ($stmt = $conn -> prepare("SELECT * FROM `problem` WHERE id = ?")) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                while ($row = $result->fetch_assoc()) {
                    $probName = $row['name']; $probCodename = $row['codename']; $probMemory = $row['memory']; $probTime = $row['time']; $probScore = $row['score']; $probAuthor = $row['author'];
                    
                    $prop = json_decode($row['properties'], true);

                    $accept = array_key_exists("accept", $prop) ? $prop["accept"] : null;
                    $hide = array_key_exists("hide", $prop) ? $prop["hide"] : false;
                    $last_hide_updated = array_key_exists("last_hide_updated", $prop) ? $prop["last_hide_updated"] : time();
                    $probRate = array_key_exists("rating", $prop) ? $prop["rating"] : 0;
                }
                $stmt->free_result();
                $stmt->close();  
            } else {
                header("Location: ../problem/");
            }
        } else {
            header("Location: ../problem/");
        }
    }
    $probDoc = "static/elements/demo.pdf";
    if (isset($probCodename) && !empty($probCodename)) {
        $probDoc = "doc/$id-$probCodename";
    }

?>
<div class="container" style="padding-top: 88px;">
    <div class="container mb-3" id="container">
        <form method="post" action="../pages/problem_save.php<?php if (isset($_GET['id'])) echo '?id=' . $_GET['id']; ?>" enctype="multipart/form-data">
            <div class="font-weight-bold text-coekku">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="md-form">
                            <input type="text" id="name" name="name" class="form-control" value="<?php echo $probName; ?>" required/>
                            <label class="form-label" for="name">Problem Name</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="md-form">
                            <input type="text" id="codename" name="codename" class="form-control" value="<?php echo $probCodename; ?>" required />
                            <label class="form-label" for="codename">Codename</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="md-form">
                            <input type="text" id="author" name="author" class="form-control" value="<?php if (!empty($probAuthor)) echo $probAuthor; ?>"/>
                            <label class="form-label" for="author">Author</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-8">
                    <input type="file" accept=".pdf" class="mb-1" name="pdfPreview" id="pdfPreview">
                    <input type="hidden" name="probDoc" id="probDoc" value="<?php echo $probDoc; ?>"/></input>
                    <script>
                    $("#pdfPreview").on('change', function(){
                        var fd = new FormData();
                        var files = $('#pdfPreview')[0].files;
                        if(files.length > 0 ){
                            fd.append('file',files[0]);
                            $.ajax({
                                url: '../pages/upload.php',
                                type: 'post',
                                data: fd,
                                contentType: false,
                                processData: false,
                                success: function(response){
                                    if(response != 0){
                                        $("#pdfViewer").attr("src","../vendor/pdf.js/web/viewer.html?file=../../" + response);
                                        $("#probDoc").val(response); 
                                    }else{
                                        alert('file not uploaded');
                                    }
                                },
                            });
                        }
                    });
                    </script>
                    <iframe
                        src="../vendor/pdf.js/web/viewer.html?file=../../../<?php echo $probDoc?>"
                        width="100%" height="600" class="z-depth-1" id="pdfViewer" name="pdfViewer"></iframe>
                </div>
                <div class="col-12 col-md-4">
                    <div id="problemDetails">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="font-weight-bold text-coekku">Config</h5>
                                <div class="md-form">
                                <label for="rating">Rating</label>
                                    <select class="mdb-select md-form colorful-select dropdown-primary" id="rating" name="rating" required>
                                        <option value="0">Peaceful</option>
                                        <option value="1">Easy</option>
                                        <option value="2">Normal</option>
                                        <option value="3">Hard</option>
                                        <option value="4">Insane</option>
                                        <option value="5">MERCILESS</option>
                                    </select>
                                    <script>
                                        $('#rating option[value=<?php echo $probRate; ?>]').attr('selected', 'selected');
                                    </script>
                                </div>
                                <div class="md-form">
                                    <input type="text" id="score" name="score" class="form-control" value="<?php echo $probScore; ?>" required  />
                                    <label class="form-label" for="score">Score</label>
                                </div>
                                <div class="md-form">
                                    <input type="text" id="time" name="time" class="form-control" value="<?php echo $probTime; ?>" required  />
                                    <label class="form-label" for="time">Time (Millisecond)</label>
                                </div>
                                <div class="md-form">
                                    <input type="text" id="memory" name="memory" class="form-control" value="<?php echo $probMemory; ?>" required />
                                    <label class="form-label" for="memory">Memory (Megabyte)</label>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="font-weight-bold text-coekku">Language</h5>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="C" id="C" name="lang[]">
                                    <label class="form-check-label" for="C">C</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="Cpp" id="C++" name="lang[]">
                                    <label class="form-check-label" for="C++">C++</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="Java" id="Java" name="lang[]">
                                    <label class="form-check-label" for="Java">Java</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="Python" id="Python" name="lang[]">
                                    <label class="form-check-label" for="Python">Python</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="TXT" id="Plain Text" name="lang[]">
                                    <label class="form-check-label" for="Plain Text">Plain Text</label>
                                </div>
                                <?php if (!isset($_GET['id']) || empty($accept)) { //Create case, check all by default ?>
                                    <script>$('input[type=checkbox][value!=TXT]').prop('checked',true);</script>
                                <?php } else { 
                                    foreach($accept as $a) { ?>
                                    <script>$('input[type=checkbox][value=<?php echo $a; ?>]').prop('checked',true);</script>
                                <?php }
                                } ?>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-body">
                                <?php
                                $count = 0; 
                                if (isset($_GET['id'])) {
                                    $id = (int) $_GET['id'];
                                    $path = "../file/judge/prob/$id/";
                                    $files = glob($path . "*.{in}", GLOB_BRACE);
                                    if ($files) {
                                        ?>
                                        <div class="accordion md-accordion" id="accordionEx" role="tablist" aria-multiselectable="true">
                                            <!-- Card header -->
                                            <div role="tab" id="headingOne1">
                                                <a data-toggle="collapse" data-parent="#accordionEx" href="#collapseOne1" aria-expanded="true" aria-controls="collapseOne1">
                                                    <h5 class="font-weight-bold text-coekku">Testcase <i class="fas fa-angle-down rotate-icon"></i></h5>
                                                </a>
                                            </div>
                                            <div id="collapseOne1" class="collapse" role="tabpanel" aria-labelledby="headingOne1" data-parent="#accordionEx">
                                            <?php 
                                                echo "<ol>";
                                                foreach($files as $f) {
                                                    $count++;
                                                    $filename = str_replace($path, "", $f);
                                                    echo "<li><a href='/bucket/".bucket_encrypting(str_replace("..","",$f))."' target='_blank'>in</a> <a href='/bucket/" .bucket_encrypting(str_replace("..","",str_replace(".in",".sol",$f))). "' target='_blank'>sol</a></li>";
                                                }
                                                echo "</ol>";
                                            ?>
                                            </div>
                                        </div>
                                        <?php 
                                        echo '<small class="text-danger">*การเปลี่ยนแปลงไฟล์จะเป็นการแทนที่ด้วยไฟล์ใหม่ทั้งหมด</small>';
                                    } else { ?>
                                        <h5 class="font-weight-bold text-coekku" id="testcaseTitle">Testcase</h5>
                                    <?php }
                                } ?>
                                <input type="file" accept=".zip" name="testcase" id="testcase" <?php if ($count == 0) echo "required"; ?>/>
                                <script>
                                var uploadField = document.getElementById("testcase");

                                uploadField.onchange = function() {
                                    if (this.value.split('.').pop() != "zip") {
                                        alert("Please compress your testcase only in .zip!");
                                        this.value = "";
                                    }
                                    //64 MB = 64 * 1024 * 1024 bytes
                                    else if (this.files[0].size > 67108864) {
                                        alert("Your file is larger than 64 Megabytes!");
                                        this.value = "";
                                    }
                                    
                                };
                                </script>
                                <small class="text-muted">Accept only .zip (Max: 64 Megabytes)</small>
                                <input type="hidden" name="testcaseFile" id="testcaseFile" value="" />

                                <input type="hidden" name="hide" id="hide" value="<?php echo $hide; ?>"/>
                                <input type="hidden" name="last_hide_updated" id="last_hide_updated" value="<?php echo $last_hide_updated; ?>"/>
                                
                            </div>
                        </div>
                        <button class="btn btn-coekku btn-block" type="submit" name="problem" value="<?php if (isset($_GET['id'])) echo "edit"; else echo "create"; ?>">Save</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
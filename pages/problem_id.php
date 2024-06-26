<?php
    $id = $_GET['id'];
    if ($stmt = $conn -> prepare("SELECT * FROM `problem` WHERE id = ? LIMIT 1")) {
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            while ($row = $result->fetch_assoc()) {
                $id = $row['id']; $name = htmlspecialchars($row['name']); $codename = htmlspecialchars($row['codename']); $mem = $row['memory'] . " Megabyte"; $time = $row['time'] . " Millisecond"; $score = $row['score']; $author = $row['author']; 
                if ($row['time'] > 1) $time .= "s"; if ($row['memory'] > 1) $mem .= "s";
            
                $prop = json_decode($row['properties'],true);
                $acceptType = array_key_exists("accept", $prop) ? $prop["accept"] : null;
                $hide = array_key_exists("hide", $prop) ? $prop["hide"] : false;
                $rate = array_key_exists("rating", $prop) ? $prop["rating"] : 0;

                if ($hide && (!isLogin() || !isAdmin()))
                header("Location: ../problem/");
                
                $accept = array();
                if ($acceptType) {
                    foreach ($acceptType as $p) {
                        switch($p) {
                            case "C":
                                array_push($accept, ".c", ".i");
                                break;
                            case "Cpp":
                                array_push($accept, ".cpp", ".cc", ".cxx", ".c++", ".hpp",".hh",".hxx",".h++",".h",".ii");
                                break;
                            case "Python":
                                array_push($accept, ".py", ".rpy", ".pyw", ".cpy", ".gyp", ".gypi", ".pyi", ".ipy");
                                break;
                            case "Java":
                                array_push($accept, ".java", ".jav"); 
                                break;
                            case "TXT":
                                array_push($accept, ".txt");
                                break;
                            default:
                                array_push($accept, "ERROR");
                                break;
                        }
                    }
                } else {
                    $accept = array(".c", ".i", ".cpp", ".cc", ".cxx", ".c++", ".hpp",".hh",".hxx",".h++",".h",".ii", ".py", ".rpy", ".pyw", ".cpy", ".gyp", ".gypi", ".pyi", ".ipy", ".java", ".jav", ".txt");
                }
            
                $accept_str = implode(",", $accept);
            }
            $stmt->free_result();
            $stmt->close();  
        } else {
            header("Location: ../problem/");
        }
        
    }
?>
<div class="container mb-3" style="padding-top: 88px;" id="container">
    <div class="d-flex mb-0">
        <div class="flex-grow-1 mb-0">
            <h2 class="font-weight-bold text-coekku mb-0"><?php echo $name; ?> <span class='badge badge-coekku'><?php echo $codename; ?></span> <?php if (isAdmin()) { echo '<a href="../pages/problem_toggle_view.php?problem_id='.$id.'&hide='.$hide.'">'; if ($hide) { echo '<i class="fas fa-eye-slash"></i>'; } else { echo '<i class="fas fa-eye"></i>'; } echo '</a>'; } ?></h2>
        </div>
        <div>
            <h2 class="mb-0 text-coekku-hover" onclick="copyThisPageURL();" id="postIDforCopyBtn" style="cursor: pointer;">#<?php echo $id; ?>
                <script>
                    var countCopy = 1;
                    function copyThisPageURL() {
                        if (countCopy <= 11) {
                            var dummy = document.createElement('input'),
                                text = window.location.href;

                            document.body.appendChild(dummy);
                            dummy.value = text;
                            dummy.select();
                            document.execCommand('copy');
                            document.body.removeChild(dummy);
                            var message; 
                            switch(true) {
                                case countCopy == 2:
                                    message = "Double copied!";
                                    break;
                                case countCopy == 3:
                                    message = "Triple copied!";
                                    break;
                                case countCopy == 4:
                                    message = "Quadruple copied!";
                                    break;
                                case countCopy == 5:
                                    message = "Quintuple copied!";
                                    break;
                                case countCopy > 5 && countCopy <= 10:
                                    message = "Copied! But you should go popcat.click";
                                    break;
                                case countCopy > 10:
                                    message = "No more copy for you!";
                                    break;
                                default:
                                    message = "Copied URL to clipboard!";
                            }
                            toastr.info(message);
                            countCopy++;
                        }
                    }
                </script>
            </h2>
        </div>
    </div>
    <small class="text-muted"><?php echo $author; ?></small>
    <hr>
    <div class="row">
        <div class="col-12 col-lg-8">
            <a href="../problem/" class="float-left"><i class="fas fa-arrow-left"></i> Back</a>
            <a target="_blank" href="../doc/<?php echo $id; ?>-<?php echo $codename; ?>" class="float-right">Open PDF <i class="fas fa-external-link-alt"></i></a>
            <iframe
                src="../vendor/pdf.js/web/viewer.html?file=../../../doc/<?php echo $id; ?>-<?php echo $codename; ?>"
                width="100%" height="650" name="pdfViewer" id="pdfViewer" class="mt-2 z-depth-1 mb-3"></iframe>
        </div>
        <div class="col-12 col-lg-4">
            <div id="problemDetails">
                <div id="adminZone" class="mb-3">
                <?php if (isAdmin()) { ?>
                    <a href="../file/judge/prob/<?php echo $id; ?>/" target="_blank" class="btn btn-sm btn-success">Testcase</a>
                    <a href="../problem/edit-<?php echo $id; ?>" class="btn btn-sm btn-primary">Edit</a>
                    <a class="btn btn-sm btn-warning" onclick='swal({title: "ต้องการจะ Rejudge ข้อ <?php echo $id; ?> หรือไม่ ?",text: "การ Rejudge อาจส่งผลต่อ Database และประสิทธิภาพโดยรวม\nความเสียหายใด ๆ ที่เกิดขึ้น ผู้ Rejudge เป็นผู้รับผิดชอบเพียงผู้เดียว\n\n**โปรดใช้สติและมั่นใจก่อนกดปุ่ม Rejudge**",icon: "warning",buttons: true,dangerMode: true}).then((willDelete) => { if (willDelete) { window.location = "../pages/rejudge.php?problem_id=<?php echo $id; ?>";}});'>Rejudge</a>
                <?php } ?>
                </div>
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="card-text">
                            <h5 class="font-weight-bold text-coekku">Task</h5>
                            <b>Time Limit:</b> <?php echo $time; ?>
                            <br><b>Memory Limit:</b> <?php echo $mem; ?>
                            <br><b>Score:</b> <?php echo $score; ?> pts.
                            <br><b>Difficulty:</b> <?php echo rating($rate); ?>
                        </div>
                    </div>
                </div>
                <?php if (!isLogin()) { ?>
                    <a href="../login/" class='btn btn-coekku btn-block'>Login</a>
                <?php } else {?>
                <div class="card mb-3">
                    <div class="card-body">
                        <form method="post" action="../pages/problem_user_submit.php" enctype="multipart/form-data">
                        <h5 class="font-weight-bold text-coekku">Submission&nbsp;<a href="../static/elements/Garedami.pdf" target="_blank"><i class="fas fa-question-circle"></i></a></h5>
                            <div class="custom-file mb-2">
                                <input type="hidden" name="probID" value="<?php echo $id; ?>"/>
                                <input type="hidden" name="probCodename" value="<?php echo $codename; ?>"/>
                                <input type="file" class="custom-file-input" id="submission" name="submission" accept="<?php echo $accept_str; ?>" required>
                                <label class="custom-file-label" for="submission">Choose file</label>
                            </div>
                            <div class="form-row">
                                <div class="col-12 col-md-6">
                                    <select class="form-control mb-2" id="lang" name="lang" required>
                                        <?php if (!empty($acceptType)) {
                                            foreach($acceptType as $p) { ?>
                                                <script>$("<option>").val("<?php echo $p; ?>").text("<?php echo $p; ?>").appendTo("#lang");</script>
                                        <?php }
                                        } else { ?>
                                            <option value="C">C</option>
                                            <option value="Cpp">C++</option>
                                            <option value="Python">Python</option>
                                            <option value="Java" selected>Java</option>
                                        <?php } ?>
                                    </select>
                                    <script>
                                        var C_file = [".c",".i"];
                                        var Cpp_file = [".cpp", ".cc", ".cxx", ".c++", ".hpp",".hh",".hxx",".h++",".h",".ii"];
                                        var Java_file = [".java",".jav"];
                                        var Python_file = [".py", ".rpy", ".pyw", ".cpy", ".gyp", ".gypi", ".pyi", ".ipy"];
                                        var Text_file = [".txt"];
                                        $("#submission").on('change', function (e) {
                                            var accept = <?php echo json_encode($accept); ?>;
                                            var filename = $("#submission").val().replace("C:\\fakepath\\","").split(".");                                        
                                            var ext = "." + filename[filename.length - 1];

                                            if (accept.includes(ext)) {
                                                if (C_file.includes(ext))
                                                    $("#lang").val("C");
                                                else if (Cpp_file.includes(ext))
                                                    $("#lang").val("Cpp");
                                                else if (Java_file.includes(ext))
                                                    $("#lang").val("Java");
                                                else if (Python_file.includes(ext))
                                                    $("#lang").val("Python");
                                                else if (Text_file.includes(ext))
                                                    $("#lang").val("TXT");
                                                $("#submitbtn").removeAttr("disabled");
                                            } else {
                                                $("#submission").val("");
                                                $("#submitbtn").prop("disabled","disabled");
                                                swal({
                                                    title: "พบข้อผิดพลาด",
                                                    text: "กรุณาเลือกเฉพาะไฟล์ที่รองรับเท่านั้น!",
                                                    icon: "warning"
                                                });
                                            }
                                        });
                                    </script>
                                </div>
                                <div class="col-12 col-md-6">
                                    <button type="submit" id="submitbtn" value="prob" name="submit" class="btn btn-block btn-coekku btn-md" disabled>Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="font-weight-bold text-coekku">History</h5>
                        <div class="table-responsive" style="max-height: 248px;">
                            <table class="table table-sm table-hover w-100 d-table">
                                <thead>
                                    <tr>
                                        <th scope="col">Timestamp</th>
                                        <th scope="col">Result</th>
                                    </tr>
                                </thead>
                                <tbody class="text-nowrap">
                                <?php
                                    if ($stmt = $conn -> prepare("SELECT `submission`.`id` as id,`submission`.`score` as score,`submission`.`maxScore` as maxScore,`submission`.`uploadtime` as uploadtime,`submission`.`result` as result,`problem`.`score` as probScore FROM `submission` INNER JOIN `problem` ON `problem`.`id` = `submission`.`problem` WHERE user = ? and problem = ? ORDER BY `id` DESC LIMIT 5")) {
                                        $user = $_SESSION['user']->getID();
                                        $stmt->bind_param('ii', $user, $id);
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                $subID = $row['id'];
                                                $subResult = $row['result'] != 'W' ? $row['result']: 'รอผลตรวจ...';
                                                $subScore = $row['maxScore'] != 0 ? ($row['score']/$row['maxScore'])*$row['probScore'] : "UNDEFINED";
                                                //$subRuntime = $row['runningtime']/1000;
                                                $subUploadtime = str_replace("-", "/", $row['uploadtime']); ?>
                                                <tr style="cursor: pointer;" onclick='launchSubmissionModal(<?php echo $subID; ?>)' data-toggle='modal' data-target='#modalPopup'>
                                                    <th scope='row'><?php echo $subUploadtime; ?></th>
                                                    <td <?php if ($row['result'] == 'W') echo "data-wait=true data-sub-id=" . $subID; ?>><code><?php echo "$subResult"; if ($row['result'] != "W") echo " ($subScore)"; ?></code></td>
                                                </tr>
                                            <?php }
                                            $stmt->free_result();
                                            $stmt->close();  
                                        } else {
                                            echo "<tr><td colspan='2' class='text-center'>No submission yet!</td></tr>";
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php } //End of isLogin()?>
            </div>
        </div>
    </div>
</div>
<script>
    function launchSubmissionModal(id) {
        document.getElementById("modalTitle").innerHTML = "Submission #" + id;
        document.getElementById("modalBody").innerHTML = '<div class="d-flex justify-content-center"><img class="img-fluid" align="center" src="<?php echo randomLoading(); ?>"></div>';
        
        $.ajax({
            type: 'POST',
            url: '../pages/submission_gen.php',
            data: { 'id': id, 'who': <?php echo (isLogin()) ? $_SESSION['user']->getID() : -1; ?> },
            success: function (data) { 
                document.getElementById("modalBody").innerHTML = data;
                $('pre > code').each(function() {
                    hljs.highlightBlock(this);
                });
            }
        })
    }
</script>

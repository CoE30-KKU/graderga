<?php
    $id = $_GET['id'];
    $files = glob("../file/judge/prob/$id/*.pdf");
    $latest_ctime = 0;
    $latest_filename = null;
    foreach($files as $file)
    {
            if (is_file($file) && filectime($file) > $latest_ctime)
            {
                    $latest_ctime = filectime($file);
                    $latest_filename = $file;
            }
    }
    if ($latest_filename != null) {
        header("Content-type: application/pdf");
        readfile($latest_filename);
    } else {
        echo "Not Found.";
    }
?>
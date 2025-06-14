<?php
    require_once '../static/functions/connect.php';
    if ($_FILES['file']['name']) {
        if (!$_FILES['file']['error']) {
            $sid = $_SESSION['user']->getID();
            $filename = "$$sid$".generateRandom().".".pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            if (!file_exists('../file/cache/')) {
                make_directory('../file/cache/');
            } else {
                foreach(glob("../file/cache/$$sid$*") as $file) {
                    unlink($file);
                }
            }

            $destination = '../file/cache/' . $filename; //change this directory
            $location = $_FILES["file"]["tmp_name"];
            move_uploaded_file($location, $destination);
            echo '../file/cache/' . $filename;//change this URL
        } else {
            echo  $message = 'Ooops!  Your upload triggered the following error:  '.$_FILES['file']['error'];
        }
    }

?>
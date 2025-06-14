<?php
    require_once '../static/functions/connect.php';
    require_once '../static/functions/function.php';
    $editorial_id = "";
    if (isAdmin() && isset($_GET['editorial_id'])) {
        $editorial_id = (int) $_GET['editorial_id'];
        $editorial_hide = (int) $_GET['hide'] ? 0 : 1;

        $prop = array(); $category = ""; $author = "";
        if ($stmt = $conn -> prepare("SELECT * FROM `editorial` WHERE id = ?")) {
            $stmt->bind_param('i', $editorial_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                while ($row = $result->fetch_assoc()) {
                    global $prop, $category, $author;
                    $prop = empty($row['properties']) ? array() : json_decode($row['properties'], true);

                    $prop["hide"] = (bool) $editorial_hide;
                    $prop["last_hide_updated"] = time();
                    
                }
                $stmt->free_result();
                $stmt->close();  
            } else {
                header("Location: ../editorial/");
            }
        } else {
            header("Location: ../editorial/");
        }
    
        $prop = json_encode($prop);

        $properties = json_encode(array("hide"=>$editorial_hide,"last_hide_updated"=>time()));
        if ($stmt = $conn -> prepare("UPDATE `editorial` SET properties=? WHERE id=?")) {
            $stmt->bind_param('si', $prop,$editorial_id);
            if (!$stmt->execute()) {
                $_SESSION['swal_error'] = "พบข้อผิดพลาด";
                $_SESSION['swal_error_msg'] = ErrorMessage::DATABASE_QUERY;
                die($conn->error);
            } else {
                $_SESSION['swal_success'] = "สำเร็จ!";
                if ($editorial_hide)
                    $_SESSION['swal_success_msg'] = "ปิดการมองเห็น Editorial #$editorial_id แล้ว";
                else
                    $_SESSION['swal_success_msg'] = "เปิดการมองเห็น Editorial #$editorial_id แล้ว";
                echo "Toggled";
            }
        } else {
            echo "Can't establish database";
        }
    }
    header("Location: ../editorial/$editorial_id");
?>
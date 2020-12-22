<?php 
    require '../static/functions/connect.php';
?>

<!DOCTYPE html>
<html lang="th" prefix="og:http://ogp.me/ns#">

<head>
    <?php require '../static/functions/head.php'; ?>
</head>
<?php require '../static/functions/navbar.php'; ?>
<body>
    <div class="container" style="padding-top: 88px;">
        <div class="container mb-3" id="container">
            <h1 class="display-4 font-weight-bold text-center text-coe">Problem</div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col" class="font-weight-bold text-coe">Problem ID</th>
                        <th scope="col" class="font-weight-bold text-coe">Task</th>
                        <th scope="col" class="font-weight-bold text-coe">Rate</th>
                        <th scope="col" class="font-weight-bold text-coe">Time Limit</th>
                        <th scope="col" class="font-weight-bold text-coe">Memory</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr onclick="window.location='../problem/1'">
                        <th scope="row">1</th>
                        <td>Welcome to Grader.ga</td>
                        <td class="text-secondary">Peaceful</td>
                        <td>1 Second</td>
                        <td>1 Megabyte</td>
                    </tr>
                    <tr>
                        <th scope="row">2</th>
                        <td>โรงแรมในฝัน</td>
                        <td class="text-success">Easy</td>
                        <td>1 Second</td>
                        <td>1 Megabyte</td>
                    </tr>
                    <tr>
                        <th scope="row">3</th>
                        <td>ตัวเลขที่สวยงาม</td>
                        <td class="text-warning">Normal</td>
                        <td>1 Second</td>
                        <td>4 Megabyte</td>
                    </tr>
                    <tr>
                        <th scope="row">4</th>
                        <td>พ่อสั่งน้องขอ</td>
                        <td class="text-danger">Hard</td>
                        <td>1.5 Second</td>
                        <td>4 Megabyte</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php require '../static/functions/popup.php'; ?>
    <?php require '../static/functions/footer.php'; ?>
</body>

</html>
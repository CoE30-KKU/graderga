<div class="container mb-3" style="padding-top: 88px;" id="container">
    <h1 class="display-4 font-weight-bold text-center text-coekku">Submission</h1>
    <?php if (isLogin()) { ?>
    <div class="switch switch-danger mb-1">
        <label>
            <input type="checkbox" name="onlyme" id="onlyme">
            <span class="lever"></span>Only My Submission
        </label>
    </div>
    <?php } ?>
    <div class="table-responsive">
        <table class="table table-sm table-hover w-100 d-block d-md-table" id="submissionTable">
            <thead>
                <tr class="text-nowrap me">
                    <th scope="col" class="font-weight-bold text-coekku">ID</th>
                    <th scope="col" class="font-weight-bold text-coekku">Timestamp</th>
                    <th scope="col" class="font-weight-bold text-coekku">User</th>
                    <th scope="col" class="font-weight-bold text-coekku">Problem ID</th>
                    <th scope="col" class="font-weight-bold text-coekku">Lang</th>
                    <th scope="col" class="font-weight-bold text-coekku">Result</th>
                </tr>
            </thead>
            <tbody class="text-nowrap" id="loadMoreZone">
                <?php
                    $_GET['page'] = 1;
                    include 'submission_load.php';
                ?>
            </tbody>
        </table>
        <div class="d-flex justify-content-center">
            <a onclick="loadMore();" class="btn btn-success text-center" id="loadMoreButton">Load More</a>
        </div>
        <script>
            if ($('#EOF').length > 0) { 
                $("#loadMoreButton").remove();
            }
        </script>
        <script>
            var currentPage = 1;
            var OnlyMe = false;

            $(window).scroll(function() {
                if(($(window).scrollTop() == $(document).height() - $(window).height()) && $("#loadMoreButton").length > 0) {
                    loadMore();
                }
            });

            var loadMoreStack = 0;
            function loadMore() {
                loadMoreStack++;
                if (loadMoreStack <= 10) {
                    $.ajax({
                        type: 'GET',
                        url: '../pages/submission_load.php',
                        data: {
                            'page': ++currentPage
                        },
                        success: function (data) {
                            if ($('#EOF').length > 0)
                                $("#loadMoreButton").remove();
                            else {
                                $('#loadMoreZone').append(data);
                                if (OnlyMe) {
                                    $(".ThisIsNotMine").each(function() {
                                        $(this).hide();
                                    });
                                }
                                if ($('#EOF').length > 0) $("#loadMoreButton").remove();
                                else if (OnlyMe) loadMore();
                                else loadMoreStack = 0;
                            }
                        }
                    });
                } else {
                    loadMoreStack = 0;
                }
            }

            $("#onlyme").change(function() {
                if ($(this).is(':checked')) {
                    $(".ThisIsNotMine").each(function() {
                        OnlyMe = true;
                        $(this).hide();
                    });
                    if ($(document).height() <= $(window).height()) loadMore();
                } else {
                    $(".ThisIsNotMine").each(function() {
                        OnlyMe = false;
                        $(this).show();
                    });
                }
            });

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
    </div>
</div>

<script src="<?php echo JS_URI ?>fullcalendar/moment.min.js"></script>

<!-- jQuery -->
<script src="<?php echo JS_URI ?>jquery.min.js"></script>
<script src="<?php echo JS_URI ?>jquery-ui.min.js"></script>

<!-- Bootstrap Core JavaScript -->
<script src="<?php echo JS_URI ?>bootstrap.min.js"></script>

<!-- Metis Menu Plugin JavaScript -->
<script src="<?php echo JS_URI ?>metisMenu/metisMenu.min.js"></script>

<!-- Morris Charts JavaScript -->
<script src="<?php echo JS_URI ?>raphael/raphael.min.js"></script>

<?php if(strpos(ADMIN_PAGE_ID, 'updatePost') !== false || strpos(ADMIN_PAGE_ID, 'updateProduct') !== false || strpos(ADMIN_PAGE_ID, 'newsletter') !== false) {
    echo '<script src="' . JS_URI . 'tinymce/tinymce.min.js"></script>';
} ?>

<?php if (strpos(ADMIN_PAGE_ID, 'morris') !== false) {
//    || strpos(ADMIN_PAGE_ID, 'tables') !== false
    echo '<script src="' . JS_URI . 'morrisjs/morris.min.js"></script>';
    echo '<script src="' . JS_URI . 'morris-data.js"></script>';
} ?>

<?php if (strpos(ADMIN_PAGE_ID, 'flot') !== false) {
    echo '<!-- Flot Charts JavaScript -->';
    echo '<script src="' . JS_URI . 'flot/excanvas.min.js"></script>';
    echo '<script src="' . JS_URI . 'flot/jquery.flot.js"></script>';
    echo '<script src="' . JS_URI . 'flot/jquery.flot.pie.js"></script>';
    echo '<script src="' . JS_URI . 'flot/jquery.flot.resize.js"></script>';
    echo '<script src="' . JS_URI . 'flot/jquery.flot.time.js"></script>';
    echo '<script src="' . JS_URI . 'flot-tooltip/jquery.flot.tooltip.min.js"></script>';
    echo '<script src="' . JS_URI . 'flot-data.js"></script>';
} ?>

<!-- Morris Charts JavaScript -->
<script src="<?php echo JS_URI ?>datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo JS_URI ?>datatables-plugins/dataTables.bootstrap.min.js"></script>
<script src="<?php echo JS_URI ?>datatables-plugins/dataTables.select.min.js"></script>
<script src="<?php echo JS_URI ?>datatables-responsive/dataTables.responsive.js"></script>

<script src="<?php echo JS_URI ?>bootstrap-toggle/bootstrap-toggle.min.js"></script>

<!-- Custom Theme JavaScript -->
<script src="<?php echo JS_URI ?>adminCustom.js"></script>
<script src="<?php echo JS_URI ?>bootstrap-datetimepicker/moment.min.js"></script>
<script src="<?php echo JS_URI ?>bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script>

<script src="<?php echo JS_URI ?>fullcalendar/fullcalendar.min.js"></script>
<script src="<?php echo JS_URI ?>fullcalendar/scheduler.min.js"></script>
<script src="<?php echo JS_URI ?>fullcalendar/program.js"></script>

<?php
if ((isEmpty($_GET["page"]) || $_GET["page"] == PageSections::DASHBOARD) && isLoggedIn() && !forceUserChangePassword()){
    $accessTokenIsNotSetOrHasExpired = checkIfAccessTokenIsNotSetOrHasExpired();
    if ($accessTokenIsNotSetOrHasExpired){
        issueOrRefreshTokenForGACharts();
    }
    ?>

    <!--GA Admin Charts-->
    <script>
        (function(w,d,s,g,js,fs){
            g=w.gapi||(w.gapi={});g.analytics={q:[],ready:function(f){this.q.push(f);}};
            js=d.createElement(s);fs=d.getElementsByTagName(s)[0];
            js.src='https://apis.google.com/js/platform.js';
            fs.parentNode.insertBefore(js,fs);js.onload=function(){g.load('analytics');};
        }(window,document,'script'));
    </script>

    <script src="<?php echo JS_URI ?>google-analytics/google-analytics-plugins.min.js"></script>

    <script>
        var accessToken = '<?php echo $_SESSION['access_token'];?>';
    </script>

    <script src="<?php echo JS_URI ?>google-analytics/google-analytics-initialization.min.js"></script>
    <?php
}
?>

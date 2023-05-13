<footer class="main-footer">
    <div class="pull-right hidden-xs">
        <b>Version</b> 3.1.0
    </div>
    <strong>Copyright@Alolyani Company <?= date("Y") ?>.</strong> All rights reserved.
</footer>
<!-- Add the sidebar's background. This div must be placed
     immediately after the control sidebar -->
<div class="control-sidebar-bg"></div>
</div><!-- ./wrapper -->

<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script type="text/javascript">
    $.widget.bridge('uibutton', $.ui.button);
</script>
<script src="<?= CP_PATH ?>/assets/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?= CP_PATH ?>/assets/plugins/select2/select2.full.min.js" type="text/javascript"></script>
<script src="<?= CP_PATH ?>/assets/plugins/sparkline/jquery.sparkline.min.js" type="text/javascript"></script>
<script src="<?= CP_PATH ?>/assets/plugins/input-mask/jquery.inputmask.js" type="text/javascript"></script>
<script src="<?= CP_PATH ?>/assets/plugins/input-mask/jquery.inputmask.date.extensions.js" type="text/javascript"></script>
<script src="<?= CP_PATH ?>/assets/plugins/input-mask/jquery.inputmask.extensions.js" type="text/javascript"></script>
<script src="<?= CP_PATH ?>/assets/js/moment.min.js" type="text/javascript"></script>
<script src="<?= CP_PATH ?>/assets/plugins/daterangepicker/daterangepicker.js" type="text/javascript"></script>
<script src="<?= CP_PATH ?>/assets/plugins/datepicker/bootstrap-datepicker.js" type="text/javascript"></script>
<script src="<?= CP_PATH ?>/assets/plugins/timepicker/bootstrap-timepicker.min.js" type="text/javascript"></script>
<script src="<?= CP_PATH ?>/assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?= CP_PATH ?>/assets/plugins/DataTables2/datatables.min.js"></script>
<script src="<?= CP_PATH ?>/assets/plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="<?= CP_PATH ?>/assets/plugins/iCheck/icheck.min.js" type="text/javascript"></script>
<script src="<?= CP_PATH ?>/assets/plugins/fastclick/fastclick.min.js" type="text/javascript"></script>
<?php
    global $js_file;
?>
<script src="<?= CP_PATH ?>/assets/js/<?= $js_file ?>" type="text/javascript"></script>
<script type="text/javascript" src="<?= CP_PATH ?>/assets/plugins/sticky/jquery.plugin.js"></script>
<script type="text/javascript" src="<?= CP_PATH ?>/assets/plugins/sticky/jquery.sticky.js"></script>
<script src="<?= CP_PATH ?>/assets/js/fileinput.js" type="text/javascript"></script>
<script src="<?= CP_PATH ?>/assets/js/bootstrap-colorpicker.js" type="text/javascript"></script>
<script src="<?= CP_PATH ?>/assets/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.print.min.js"></script>


<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.min.js"></script>
<script type="text/javascript" src="https://html2canvas.hertzen.com/dist/html2canvas.js"></script>

<?php
    global $lang;
    
    $dt_ar = '';
    
    if ($lang === 'ar') {
        
        $dt_ar = ',"oLanguage": {
        "sInfo": "عرض من _START_ إلى _END_ (يوجد _TOTAL_)",
        "sLengthMenu": "عرض _MENU_",
        "sZeroRecords": "لا يوجد بيانات",
       "sSearch": "بحث",
       "oPaginate": {
         "sNext": "التالي",
         "sPrevious": "السابق"
       }
     }';
     
    }
?>
<script type="text/javascript">
    $(function () {
        $('.main-sidebar').animate({
            scrollTop: $('#active').offset()?.top - 100
        }, 500);
        
        $(".datatable-table").DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": false,
            "ordering": false,
            "info": true,
            "autoWidth": false<?=$dt_ar?>,
        });


        $(".datatable-table2").DataTable({
            "iDisplayLength": 25<?=$dt_ar?>
        });

        $(".datatable-table3").DataTable({
            "order": [[0, "desc"]]<?=$dt_ar?>
        });

        $(".datatable-table4").DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": false,
            "info": true,
            "autoWidth": false<?=$dt_ar?>
        });


        $('#myTable').dataTable({
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: ':not(:last-child)',
                    }
                }
            ],


        });


        $('#myTable2').dataTable({
            dom: 'Blfrtip',
            buttons: [],
            "lengthMenu": [[-1, 10, 25, 50], ["All", 10, 25, 50]],
            "autoWidth": false,
            "paging": false,
            "bInfo": false,
        });


        $('#example2').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "info": true,
            "autoWidth": false<?=$dt_ar?>
        });
    });

    $(".select2").select2();
    $('#expirydate').datepicker();


    //Create PDf from HTML...
    function pdfsuites() {
        $(".dt-buttons").hide();
        $(".dataTables_filter").hide();


        var HTML_Width = $(".html-content").width();
        var HTML_Height = $(".html-content").height();
        var top_left_margin = 50;
        var PDF_Width = HTML_Width + (top_left_margin * 2);
        var PDF_Height = (PDF_Width) + (top_left_margin * 2) + 3;
        var canvas_image_width = HTML_Width;
        var canvas_image_height = HTML_Height;

        var totalPDFPages = Math.ceil(HTML_Height / PDF_Height) - 1;

        console.log(PDF_Width);

        html2canvas($(".html-content")[0]).then(function (canvas) {
            var imgData = canvas.toDataURL("image/jpeg", 1.0);
            var pdf = new jsPDF('p', 'pt', [PDF_Width, PDF_Height]);
            pdf.addImage(imgData, 'JPG', top_left_margin, top_left_margin, canvas_image_width, canvas_image_height);
            for (var i = 1; i <= totalPDFPages; i++) {
                pdf.addPage(PDF_Width, PDF_Height);
                pdf.addImage(imgData, 'JPG', top_left_margin, -(PDF_Height * i) + (top_left_margin * 2), canvas_image_width, canvas_image_height);
            }
            pdf.save(" <?= $title??'suites_pdf_export' ?>");
            // $(".html-content").hide();

            $(".dt-buttons").show();
            $(".dataTables_filter").show();
        });
    }

    function pdfbuses() {
        $(".dt-buttons").hide();
        $(".dataTables_filter").hide();
        var HTML_Width = $(".html-content").width();
        var HTML_Height = $(".html-content").height();
        var top_left_margin = 15;
        var PDF_Width = HTML_Width + (top_left_margin * 2);
        var PDF_Height = (PDF_Width * 1.5) + (top_left_margin * 2);
        var canvas_image_width = HTML_Width;
        var canvas_image_height = HTML_Height;

        var totalPDFPages = Math.ceil(HTML_Height / PDF_Height) - 1;
        
        html2canvas($(".html-content")[0]).then(function (canvas) {
            var imgData = canvas.toDataURL("image/jpeg", 1.0);
            var pdf = new jsPDF('p', 'pt', [PDF_Width, PDF_Height]);
            pdf.addImage(imgData, 'JPG', top_left_margin, top_left_margin, canvas_image_width, canvas_image_height);
            pdf.save(" <?=$title??''?>");
            
            $(".dt-buttons").show();
            $(".dataTables_filter").show();
        });
    }


</script>
</body>
</html>
<?php ob_end_flush(); ?>

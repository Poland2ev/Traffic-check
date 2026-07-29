<?php
  // require_once('sess_auth.php');
  
?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
  	<title><?php echo $_settings->info('title') != false ? $_settings->info('title').' | ' : '' ?><?php echo $_settings->info('name') ?></title>
    <link rel="icon" href="<?php echo validate_image($_settings->info('logo')) ?>" />
    <!-- Google Font: Source Sans Pro -->
    <!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&amp;display=fallback"> -->
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo base_url ?>plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <!-- <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css"> -->
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="<?php echo base_url ?>plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
      <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo base_url ?>plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="<?php echo base_url ?>plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="<?php echo base_url ?>plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
   <!-- Select2 -->
  <link rel="stylesheet" href="<?php echo base_url ?>plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="<?php echo base_url ?>plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="<?php echo base_url ?>plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- JQVMap -->
    <link rel="stylesheet" href="<?php echo base_url ?>plugins/jqvmap/jqvmap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo base_url ?>dist/css/adminlte.css">
    <link rel="stylesheet" href="<?php echo base_url ?>dist/css/custom.css">
    <link rel="stylesheet" href="<?php echo base_url ?>assets/css/styles.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="<?php echo base_url ?>plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="<?php echo base_url ?>plugins/daterangepicker/daterangepicker.css">
    <!-- summernote -->
    <link rel="stylesheet" href="<?php echo base_url ?>plugins/summernote/summernote-bs4.min.css">
     <!-- SweetAlert2 -->
  <link rel="stylesheet" href="<?php echo base_url ?>plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <style type="text/css">/* Chart.js */
      @keyframes chartjs-render-animation{from{opacity:.99}to{opacity:1}}.chartjs-render-monitor{animation:chartjs-render-animation 1ms}.chartjs-size-monitor,.chartjs-size-monitor-expand,.chartjs-size-monitor-shrink{position:absolute;direction:ltr;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1}.chartjs-size-monitor-expand>div{position:absolute;width:1000000px;height:1000000px;left:0;top:0}.chartjs-size-monitor-shrink>div{position:absolute;width:200%;height:200%;left:0;top:0}
    </style>

     <!-- jQuery -->
    <script src="<?php echo base_url ?>plugins/jquery/jquery.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="<?php echo base_url ?>plugins/jquery-ui/jquery-ui.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="<?php echo base_url ?>plugins/sweetalert2/sweetalert2.min.js"></script>
    <!-- Toastr -->
    <script src="<?php echo base_url ?>plugins/toastr/toastr.min.js"></script>
    <script>
        var _base_url_ = '<?php echo base_url ?>';
    </script>
    <script src="<?php echo base_url ?>dist/js/script.js"></script>
    <script src="<?php echo base_url ?>assets/js/scripts.js"></script>

    <style>
    body{
        font-family: "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        color:#17202a;
        background:#f6f8fa;
        text-rendering: optimizeLegibility;
        -webkit-font-smoothing: antialiased;
    }
    body > footer.py-5.bg-dark{
        display:none !important;
    }
    .public-nav{
        min-height:72px;
        padding:12px clamp(18px,4vw,70px);
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:24px;
        background:rgba(255,255,255,.96);
        border-bottom:1px solid #d7e1e8;
        position:relative;
        z-index:5;
    }
    .public-brand{
        display:inline-flex;
        align-items:center;
        gap:12px;
        color:#17202a;
        text-decoration:none;
    }
    .public-brand:hover{
        color:#17202a;
        text-decoration:none;
    }
    .public-brand-mark{
        width:42px;
        height:42px;
        display:grid;
        place-items:center;
        border-radius:50%;
        background:#0b6fb3;
        color:white;
        font-weight:800;
    }
    .public-brand strong{
        display:block;
        font-size:18px;
    }
    .public-brand small{
        display:block;
        color:#5d6b78;
        font-size:13px;
        margin-top:1px;
    }
    .login-link{
        min-height:40px;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        padding:0 18px;
        border-radius:8px;
        background:#0b6fb3;
        color:white;
        font-weight:700;
        text-decoration:none;
    }
    .login-link:hover{
        background:#084f83;
        color:white;
        text-decoration:none;
    }
    #main-header.dynamic-lookup-hero{
        position:relative;
        min-height:calc(100dvh - 72px);
        display:grid;
        place-items:center;
        padding:72px 18px 92px;
        overflow:hidden;
        color:#fff;
        background:
            linear-gradient(90deg, rgba(8,36,55,.92), rgba(8,36,55,.58)),
            url('<?php echo validate_image($_settings->info('cover')) ?>') center/cover no-repeat !important;
    }
    .dynamic-lookup-inner{
        width:min(980px,100%);
        position:relative;
        z-index:2;
    }
    .dynamic-lookup-inner h1{
        max-width:850px;
        margin:0 0 18px;
        font-size:clamp(42px,7vw,78px);
        line-height:1.02;
        font-weight:800;
        letter-spacing:0;
        color:white;
    }
    .dynamic-lookup-inner p{
        max-width:780px;
        margin:0;
        color:rgba(255,255,255,.88);
        font-size:clamp(18px,2vw,25px);
        line-height:1.55;
    }
    .dynamic-search-panel{
        margin-top:34px;
        padding:18px;
        border:1px solid rgba(255,255,255,.32);
        border-radius:8px;
        background:rgba(255,255,255,.14);
        box-shadow:0 18px 50px rgba(20,41,58,.16);
    }
    .dynamic-search-panel label{
        display:block;
        margin-bottom:10px;
        color:white;
        font-size:18px;
        font-weight:700;
    }
    .dynamic-search-row{
        display:grid;
        grid-template-columns:1fr 170px;
        gap:12px;
    }
    .dynamic-search-row input{
        min-height:58px;
        border:1px solid #d7e1e8;
        border-radius:8px;
        padding:0 20px;
        color:#17202a;
        font-size:21px;
        outline:none;
    }
    .dynamic-search-row input:focus{
        border-color:#0b6fb3;
        box-shadow:0 0 0 3px rgba(11,111,179,.18);
    }
    .dynamic-search-row button{
        min-height:58px;
        border:0;
        border-radius:8px;
        background:#0b6fb3;
        color:white;
        font-size:20px;
        font-weight:800;
    }
    .dynamic-search-row button:hover{
        background:#084f83;
    }
    .dynamic-samples{
        display:flex;
        flex-wrap:wrap;
        gap:8px;
        margin-top:14px;
    }
    .dynamic-samples a{
        min-height:54px;
        display:inline-flex;
        flex-direction:column;
        justify-content:center;
        padding:0 14px;
        border:1px solid rgba(255,255,255,.4);
        border-radius:8px;
        color:white;
        background:rgba(255,255,255,.12);
        text-decoration:none;
    }
    .dynamic-samples a span{
        display:block;
        color:rgba(255,255,255,.76);
        font-size:12px;
        font-weight:600;
        line-height:1.1;
        margin-bottom:4px;
    }
    .dynamic-samples a strong{
        display:block;
        color:white;
        font-size:16px;
        font-weight:800;
        line-height:1.1;
    }
    .dynamic-samples a:hover{
        color:white;
        background:rgba(255,255,255,.22);
        text-decoration:none;
    }
    @media(max-width:768px){
        .public-nav{
            align-items:flex-start;
            flex-direction:column;
        }
        .dynamic-search-row{
            grid-template-columns:1fr;
        }
        .dynamic-lookup-inner h1{
            font-size:42px;
        }
    }

 </style>
  </head>
  
<?php if($_settings->chk_flashdata('success')): ?>
<script>
  $(function(){
    alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
  })
</script>
<?php endif;?>

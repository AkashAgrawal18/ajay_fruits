<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= get_settings('m_app_name') ?> </title>
    <!--bootstrap-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous" />
    <!-- favicon -->
    <!-- <link rel="icon" href="<? // base_url('uploads/') . get_settings('m_app_icon') ?>" /> -->
    <!-- icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!--custom css-->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/lib/sweet-alerts/css/sweetalert.css') ?>" />
    <link href="<?= base_url('assets/lib/select2/dist/css/select2.min.css') ?>" rel="stylesheet" />
</head>
<style>
    .select2-container {
        box-sizing: border-box;
        display: inline-block;
        margin: 0;
        position: relative;
        width: 100%;
        vertical-align: middle;
    }

    @media print {
        .no-print {
            display: none !important;
        }

    }
</style>

<body>
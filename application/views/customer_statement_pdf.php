<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Customer Statement</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
        }
        .header {
            width: 100%;
            margin-bottom: 10px;
        }
        .header td {
            vertical-align: top;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
        }
        .right {
            text-align: right;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
        }
        th {
            background: #f2f2f2;
        }
        .no-border td {
            border: none;
        }
        .footer {
            margin-top: 10px;
        }
    </style>
</head>
<body>

<!-- Header -->
<table class="header">
    <tr>
        <td>
            <div class="title"><?php echo $cust_dtl->m_cust_name; ?></div>
            <div><?php echo $cust_dtl->m_city_name; ?></div>
        </td>
        <td class="right">
            <div><strong>
                <?php echo date('d/m/Y', strtotime($from_date)); ?> 
                TO 
                <?php echo date('d/m/Y', strtotime($todate)); ?>
            </strong></div>
        </td>
    </tr>
</table>

<!-- Table -->
<table>
    <thead>
        <tr>
            <th>Sno</th>
            <th>Date</th>
            <th>Particular</th>
            <th>Debit</th>
            <th>Credit</th>
            <th>Balance</th>
        </tr>
    </thead>
    <tbody>
        <?php echo $Mainarray; ?>
    </tbody>
</table>

<!-- Footer -->
<div class="footer">
    <table class="no-border">
        <tr>
            <td>
                <p>Balance Crate: <?php echo $closing_balance['balance_crate']; ?></p>
                <p><?php echo $crtabi; ?></p>
            </td>
            <td class="right">
                <h3>CLOSING BALANCE: <?php echo $closing_balance['balance_amount']; ?></h3>
            </td>
        </tr>
    </table>
</div>

</body>
</html>
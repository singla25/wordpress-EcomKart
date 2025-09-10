<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <title>Experience Performance Score Certificate</title>
    <style>
    @page {
        margin: 0;
    }

    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }

    .page {
        width: 100%;
        height: 100%;
        box-sizing: border-box;
        position: relative;
    }

    .page-bg {
        background: #000;
        text-align: center;
    }

    .page-break {
        page-break-before: always;
    }

    /* .flex-box {
        padding-top: 21%;
        align-items: center;
        flex-direction: column;
        justify-content: center;
    } */
    .flex-box {
        padding-top: 40px;
        align-items: center;
        flex-direction: column;
        justify-content: center;
    }

    /* .first-page h1 {
        color: #fff;
        font-size: 32px;
        padding: 0px;
        margin: 0px;
    }

    .first-page h3 {
        font-size: 30px;
        color: #fff;
        margin: 10px;
        font-weight: normal
    }

    .first-page img {
        width: 60%;
        margin-top: 80px
    } */
    .first-page h3 {
        font-size: 30px;
        color: #fff;
        margin: 10px;
        font-weight: 300;
        line-height: 25px;
        margin-bottom: 20px;
    }

    .footer {
        position: absolute;
        bottom: 20px;
        width: 100%;
        font-size: 14px;
    }

    .footer-inner {
        margin: 0px 40px;
        padding-top: 15px;
        color: #fff;
        border-top: 2px solid #63636380;
    }

    /* Micro clearfix */
    .clearfix::before,
    .clearfix::after {
        content: "";
        display: table;
    }

    .clearfix::after {
        clear: both;
    }

    /* optional: for old IE (if you ever need it) */
    .clearfix {
        *zoom: 1;
    }

    .footer-inner img {
        width: 80px
    }

    .fl-right {
        float: right;
        font-weight: bold;
        padding-top: 50px
    }

    .fl-left {
        float: left;
    }

    .second-page {
        padding: 80px 150px;
    }

    .colored-table {
        width: 500px;
    }

    .colored-table td,
    .colored-table th {
        padding: 15px 12px;
        text-align: left
    }

    .colored-table {
        border: none;
        border-collapse: collapse;
        /* makes sure inner borders also collapse */
    }

    .colored-table th,
    .colored-table td {
        border: none;
    }

    .colored-table th {
        background: #B6D7A8
    }

    .colored-table td {
        background: #D9D9D9
    }

    .table-pages {
        padding: 60px 40px 40px 40px;
    }


    .table-pages table {
        border: 1px solid black;
        /* outer border */
        border-collapse: collapse;
        /* makes a single border instead of double */
    }

    .table-pages thead {
        background: #B6D7A8
    }

    .table-pages table {
        width: 100%;
    }

    .table-pages table th,
    .table-pages table td {
        border: 1px solid black;
        /* cell borders */
        padding: 8px;
        /* spacing inside cells */
    }

    .grey-bg {
        background: #D9D9D9;
        text-align: center;
        font-size: 20px;
    }

    .normal-table {
        padding: 120px 40px 40px 40px;
        text-align: center;
        margin: 0px auto;
    }

    .normal-table table {
        margin: 0px auto;
        width: 90%;
        border: 1px solid black;
        /* outer border */
        border-collapse: collapse;
    }

    .normal-table table th,
    .normal-table table td {
        border: 1px solid black;
        /* cell borders */
        padding: 8px;
        /* spacing inside cells */
    }
    .rel-logo {
        position: relative;
    }
    .lgo {
        width: 470px;
        margin-top: 170px;
    }
    .plan-outer {
        position: absolute;
        top: 378px;
        z-index: 1000;
        left: 298px
    }

    .plan-outer ul {
        margin: 0px;
        padding: 0px;
        list-style: none;
    }

    .plan-outer ul li {
        display: inline-block;
    }

    .plan-outer .plan {
        font-size: 32px;
        font-weight: 600;
        line-height: 20px;
        padding-top: 10px;
        padding-bottom: 15px
    }

    .plan-outer ul li img {
        width: 35px;
    }
    .date {
        color: #fff;
        padding-top: 30px;
    }

    </style>
</head>

<body>
<?php //echo "<pre>"; print_r($other_fields);?>
    <!-- PAGE 1 -->
    <div class="page page-bg">
        <div class="flex-box first-page">
            <h3>End-of-Trip Facility Assessment</h3>
            <h3>SMME</h3>
             <!--<img src="<?php // echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/logo-2.png'; ?>" /> -->
            <!-- <h3><?php // echo $client_name; ?><br> '<?php // echo $Type; ?>'<br> End-of-Trip Facility Assessment</h3>
            <div class="heading-top">
                <h1><?php // echo $state; ?></h1>
            </div> -->
            <div class="rel-logo">
                <img class="lgo" src="<?php echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/logo-2.png'; ?>" />
                <div class="plan-outer">
                    <div class="plan"><?php echo $medal; ?></div>
                    <ul>
                        <!-- <li><img src="<?php //echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/red-star.png'; ?>" /></li>
                        <li><img src="<?php //echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/red-star.png'; ?>" /></li>
                        <li><img src="<?php //echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/red-star.png'; ?>" /></li>
                        <li><img src="<?php //echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/red-star.png'; ?>" /></li>
                        <li><img src="<?php //echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/red-star.png'; ?>" /></li> -->
                        <?php for ($i = 0; $i < 5; $i++) : ?>
                                        <?php if ($i < $stars) : ?>
                                            <li><img src="<?php echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/red-star.png'; ?>" /></li>
                                        <?php else : ?>
                                            <!-- <li><img src="<?php // echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/gray-star.png'; ?>" /></li> -->
                                            <li><img src="<?php echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/black-star.png'; ?>" /></li>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                    </ul>
                    <p class="date"><?php echo $formatted_date; ?></p>
                </div>

            </div>
        </div>
        <div class="footer">
            <div class="footer-inner clearfix">
                <img class="fl-left" src="<?php echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/Five-at-Heart-Logo.jpg'; ?>" alt="Logo">

                <div class="fl-right">fiveatheart.com</div>
            </div>
        </div>
    </div>
 
    <div class="page ">
        <div class="second-page">
            <table class="colored-table">
                <tbody>
                <?php foreach($pre_fields as $key => $field){ ?>
                    <tr>
                        <th><?= ucwords(str_replace('_', ' ', $key)); ?>:</th>
                        <td><?= esc_html($field['value']); ?></td>
                    </tr>
                <?php } ?>

                </tbody>
            </table>
        </div>

    </div>

    <div class="page ">
        <div class="table-pages">
            <table>
                <thead>
                    <tr>
                        <th width="55%">Description</th>
                        <th width="15%">Achieved Score</th>
                        <th>Selected option</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($other_fields as $cat_id => $fields): 
                    // Get category name
                    $category_name = ($cat_id === 'no_category') 
                        ? 'Uncategorized' 
                        : (get_term($cat_id)->name ?? 'Unknown Category');
                    ?>
                    <tr>
                        <td class="grey-bg" colspan="3"><strong><?= esc_html($category_name); ?></strong></td>
                    </tr>
                    
                    <?php foreach ($fields as $key => $field): 
                        // Only show rows if q_id is not empty/zero
                        if (!empty($field['q_id'])): 
                            // Optional: skip NA fields
                            if (!empty($field['is_na']) && $field['is_na'] == 1) continue;
                            ?>
                            <tr>
                                <th><?= ucwords(str_replace('_', ' ', $key)); ?>:</th>
                                <td><?= esc_html($field['points']); ?></td>
                                <td><?= esc_html($field['value']); ?></td>
                            </tr>
                        <?php endif;
                    endforeach; ?>
                <?php endforeach; ?>


            
  
                </tbody>
            </table>
        </div>

    </div>


    <div class="page ">
        <div class="normal-table">
            <table>
                <thead>
                    <tr>
                        <th></th>
                        <th>COMBINED OVERALL SCORE</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($meta as $key => $field){ 
                     if ($key == "steps_data") {
                        continue; // skip first and last
                    }
                    ?>
                        <tr>
                            <th><?= ucwords(str_replace('_', ' ', $key)); ?>:</th>
                            <td><?= esc_html($field); ?></td>
                           
                        </tr>
                    <?php } ?>
    
                </tbody>
            </table>
        </div>
        <div>
</body>

</html>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Experience Performance Score Certificate</title>
    <style>
    /* Poppins font registration */

    @font-face {
        font-family: 'Poppins';
        font-weight: 300;
        src: url("<?php echo QUESTIONNAIRE_PLUGIN_URL . 'assets/fonts/Poppins-Light.ttf'; ?>") format('truetype');
    }

    @font-face {
        font-family: 'Poppins';
        font-weight: 400;
        src: url("<?php echo QUESTIONNAIRE_PLUGIN_URL . 'assets/fonts/Poppins-Regular.ttf'; ?>") format('truetype');
    }


    @font-face {
        font-family: 'Poppins';
        font-weight: 600;
        src: url("<?php echo QUESTIONNAIRE_PLUGIN_URL . 'assets/fonts/Poppins-SemiBold.ttf'; ?>") format('truetype');
    }


    @font-face {
        font-family: 'Poppins';
        font-weight: 700;
        src: url("    <?php echo QUESTIONNAIRE_PLUGIN_URL . 'assets/fonts/Poppins-Bold.ttf'; ?>") format('truetype');
    }






    @page {
        margin: 0;
    }

    body {
        font-family: 'Poppins', Arial, sans-serif;
        font-weight: 300;
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

    .flex-box {
        padding-top: 40px;
        align-items: center;
        flex-direction: column;
        justify-content: center;
    }



    .first-page h3 {
        font-size: 30px;
        color: #fff;
        margin: 10px;
        font-weight: 300;
        line-height: 25px;
        margin-bottom: 20px;
    }

    .lgo {
        width: 470px;
        margin-top: 170px;
    }

    .heading-top {
        margin: 0px 100px;
        padding: 0px;
    }

    .heading-top h1 {
        color: #fff;
        font-weight: 600;
        font-size: 34px;
        border-bottom: 1px solid #fff;
        border-top: 1px solid #fff;
        margin: 0px;
        padding: 10px 0px 30px;
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

    .rel-logo {
        position: relative;
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


    .internal-second-page{padding: 10px 60px 10px 60px;   font-family: 'Poppins', Arial, sans-serif;}
    h3, p{font-family: 'Poppins', Arial, sans-serif;}
    .header-sc h3{font-weight:600;   font-family: 'Poppins', Arial, sans-serif; text-align:center; border-bottom:1px solid #333333ff; margin-bottom:0px;}
    .Performance h3{font-weight:600;}
    .rat-bar ul{  margin: 0px;
        padding: 0px;
        list-style: none;}

        .rat-bar ul{float:left}
         .rat-bar p{float:left; padding-left:17px; margin-top:-5px; font-size:14px; }
        .rat-bar ul li{
           display: inline-block;
        }
         .rat-bar ul li img{width:30px;}
         .sty-para{font-size:13px;  line-height:15px;}

         .widget-block{ margin: 5px 0 0 0px;
        padding:0px 0 0 0px;
        list-style: none; border-top:1px solid #333333ff }
         .widget-block > li {
          border-bottom:1px solid #333333ff; padding:10px 0px;
         }
          .pull-left{float:left; width:70%;}
          .pull-left h3{margin:0px ;  font-family: 'Poppins', Arial, sans-serif; font-weight:600; font-size:18px; }
          .pull-left h3 span{font-weight:300;}
          .pull-left p{margin:0px; padding:0px; font-size:13px; line-height:13px;}
          .pull-right{float:right}

          .badge-rating{margin: -15px 0 0 0px;
        padding:0px 0 0 0px;
        list-style: none;}
         .badge-rating li{display: inline-block;}
          .badge-rating li img{width:20px;}
           .badge-block{position: relative; margin-top:30px; margin-bottom:0px;}
           .badge-block strong{position: absolute; font-family: 'Poppins', Arial, sans-serif; font-weight:600; top:0px; left:44px; font-size:20px}
          .badge-block img{width: 100px; margin-left:15px;}
    </style>
</head>

<body>

    <!-- PAGE 1 -->
    <div class="page page-bg">

        <div class="flex-box first-page">
            <h3><?php echo $client_name; ?><br> '<?php echo $Type; ?>'<br> End-of-Trip Facility Assessment</h3>
            <div class="heading-top">
                <h1><?php echo $state; ?></h1>
            </div>
            <div class="rel-logo">
                <img class="lgo" src="<?php echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/logo-2.png'; ?>" />
                <div class="plan-outer">
                    <div class="plan"><?php echo $medal; ?></div>
                    <ul>
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
                <img class="fl-left"
                    src="<?php echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/Five-at-Heart-Logo.jpg'; ?>" />
                <div class="fl-right">fiveatheart.com</div>
            </div>
        </div>
    </div>


    <div class="page">
        <div class="internal-second-page">
            <div class="header-sc">
              <h3><?php echo $state; ?></h3>
            </div>

            <div class="Performance">
              <h3>Your	Guide	to	the	Experience	Performance	Score:</h3>
              <div class="rat-bar clearfix">
                  <ul>
                        <li><img src="<?php echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/red-star.png'; ?>" /></li>
                        <li><img src="<?php echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/red-star.png'; ?>" /></li>
                        <li><img src="<?php echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/black-star.png'; ?>" /></li>
                        <li><img src="<?php echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/black-star.png'; ?>" /></li>
                        <li><img src="<?php echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/black-star.png'; ?>" /></li>
                    </ul>
                <p>2	Stars	-	Bronze	(Below	64%)</p>
              </div>

                <div class="rat-bar clearfix">
                  <ul>
                        <li><img src="<?php echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/red-star.png'; ?>" /></li>
                        <li><img src="<?php echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/red-star.png'; ?>" /></li>
                        <li><img src="<?php echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/red-star.png'; ?>" /></li>
                        <li><img src="<?php echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/black-star.png'; ?>" /></li>
                        <li><img src="<?php echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/black-star.png'; ?>" /></li>
                    </ul>
                <p>3	Stars	-	Silver	(64-79%)</p>
              </div>


                <div class="rat-bar clearfix">
                  <ul>
                        <li><img src="<?php echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/red-star.png'; ?>" /></li>
                        <li><img src="<?php echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/red-star.png'; ?>" /></li>
                        <li><img src="<?php echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/red-star.png'; ?>" /></li>
                        <li><img src="<?php echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/red-star.png'; ?>" /></li>
                        <li><img src="<?php echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/black-star.png'; ?>" /></li>
                    
                    </ul>
                <p>4	Stars	-	Gold	(80-89%)</p>
              </div>


                <div class="rat-bar clearfix">
                  <ul>
                        <li><img src="<?php echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/red-star.png'; ?>" /></li>
                        <li><img src="<?php echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/red-star.png'; ?>" /></li>
                        <li><img src="<?php echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/red-star.png'; ?>" /></li>
                        <li><img src="<?php echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/red-star.png'; ?>" /></li>
                        <li><img src="<?php echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/red-star.png'; ?>" /></li>
                        
                    </ul>
                <p>5	Stars	-	Platinum	(90-100%)</p>
              </div>

              <p class="sty-para">Our	comprehensive	assessment	is	based	on	36	experience	factors.	We	evaluate	across	two	
core	pillars:	Space	Design,	which	assesses	how	well	the	facility	is	planned,	and	
Product	Performance,	which	examines	the	quality	of	the	products	used	within.	Both	of	which	
play	a	key	role	in	delivering	a	superior	user	experience.	Below	is	the	breakdown	of	scores	for	
each	pillar,	which	together	determine	your	building’s	overall	score...</p>
            </div>



            <ul class="widget-block ">
            <?php
     
                  $steps_title = [];
                    foreach ($processed_steps as $index => $processed_step) {
                    
                        $steps_title[] = $processed_step['step_title'];
                        ?>
                        <li class="clearfix">
                            <div class="pull-left">
                                <img style="height:60px" src="<?php echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/icon-1.jpg'; ?>"/>
                                <h3>
                                    <?= $processed_step['step_title']; ?> 
                                    Score: <span><?= $processed_step['medal']; ?></span>
                                    <p><?= $processed_step['step_discriptions']; ?></p>
                                    
                                </h3>
                            </div>
                            <div class="pull-right">
                                <div class="badge-block">
                                    <img src="<?= $processed_step['image']; ?>"/>
                                    <strong><?= round($processed_step['step_percentage'], 1); ?>%</strong>
                                </div>
                                <ul class="badge-rating">
                                    <?php for ($i = 0; $i < 5; $i++) : ?>
                                        <?php if ($i < $processed_step['stars']) : ?>
                                            <li><img src="<?php echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/red-star.png'; ?>" /></li>
                                        <?php else : ?>
                                            <!-- <li><img src="<?php // echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/gray-star.png'; ?>" /></li> -->
                                            <li><img src="<?php echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/black-star.png'; ?>" /></li>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </ul>
                            </div>
                        </li>
                        <?php
                    }

                    $steps_title_str = implode( ', ', $steps_title );
                    ?>

                       <li class="clearfix">
                            <div class="pull-left">
                                <img style="height:60px" src="<?php echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/icon-1.jpg'; ?>"/>
                                <h3>
                                
                                    Overall Score: <span><?= $medal; ?></span>
                                </h3>
                                <p>We	combine <?php echo esc_html( $steps_title_str ); ?>	to	generate	
                                your	overall	building	score.	If	your	looking	for	ways	to	elevate	your	
                                facility,	connect	with	our	team	to	discuss	practical	ways	to	enhance	
                                your	design	performance	score.</p>
                            </div>
                            <div class="pull-right">
                                <div class="badge-block">
                                    <img src="<?= $badge_image; ?>"/>
                                    <strong><?= round($percentage, 1); ?>%</strong>
                                </div>
                                <ul class="badge-rating">
                                    <?php for ($i = 0; $i < 5; $i++) : ?>
                                        <?php if ($i < $stars) : ?>
                                            <li><img src="<?php echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/red-star.png'; ?>" /></li>
                                        <?php else : ?>
                                            <!-- <li><img src="<?php // echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/gray-star.png'; ?>" /></li> -->
                                            <li><img src="<?php echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/black-star.png'; ?>" /></li>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </ul>
                            </div>
                        </li>

           

           </ul>

            

        </div>

         <div class="footer-end">
            <div class="footer-inner-end clearfix">
                <img class="fl-left"
                    src="<?php echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/ftr-logo.jpg'; ?>" />
               
            </div>
        </div>

    </div>

<style>
      .footer-end {
        position: absolute;
        bottom: 20px;
        width: 100%;
        font-size: 14px;
    }

    .footer-inner-end {
        margin: 0px 70px;
        padding-top: 15px;
        color: #fff;       
    }
     .footer-inner-end img{width: 80px;}

</style>

</body>

</html>
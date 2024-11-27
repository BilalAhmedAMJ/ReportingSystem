<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />

		<title>AMJ Canada Reporting System</title>

		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

		<link rel = 'shortcut icon' 'type' = 'image/vnd.microsoft.icon' 'href' ='/img/favicon.ico' />

		<!-- bootstrap & fontawesome -->
		<link rel="stylesheet" href='<?php echo "" . "/assets/css/bootstrap.min.css" ?>' />
		<link rel="stylesheet" href='<?php echo "" . "/assets/css/font-awesome.min.css" ?>' />
		<!-- text fonts -->
		<link rel="stylesheet" href='<?php echo "" . "/assets/css/ace-fonts.css"  ?>'/>

		<!-- JQuery UI must be before Bootstrap -->
		<link type="text/css" rel="stylesheet" class="ajax-stylesheet" href='<?php echo "" . "/assets//css/jquery-ui.custom.min.css" ?>'>

		<link rel="stylesheet" href='<?php echo "" . "/assets/css/ace.min.css" ?>' id="main-ace-style" />

<style>
	td {
		font-family: Verdana, Arial, Helvetica, sans-serif;
		font-size: 9pt;
		color: #333333;
	}
	.newstyletable {
		border: 1px solid #CEE3F6;
		border-radius: 15px;
		BACKGROUND-COLOR: #CEE3F6;
        padding-top:5px;
	}
	.newstylemaintable {
		border: 1px solid #EEEEEE;
		border-radius: 10px;
		BACKGROUND-COLOR: #EEEEEE;
	}

	.newstyletxt {
		color: #336699;
		font-size: 12pt;
	}

	.newstyletxtinput {
		color: #333333 !important;
		background-color: #FCFCFC !important;
		border: 0px solid #336699 !important;
		border-radius: 5px !important;
		font-size: 12pt !important;
		height: 25px !important;
		line-height: 15px !important;
		padding: 5px 0px 5px 5px !important;
		margin-bottom: 6px !important;
		margin-right: 6px !important;
		margin-top: 2px !important;
	}

	input[type=text], input[type=password] {
		border-radius: 5px !important;
		height: 30px !important;
		
	}
	.newstylebutton {
		border-radius: 5px;
		BACKGROUND-COLOR: #336699;
		BORDER: 0px solid #FACC2E;
		WIDTH: 80px;
		font-size: 12pt;
		FONT-WEIGHT: bold;
		cursor: hand;
		margin-bottom: 6px;
		margin-top: 6px;
		COLOR: #fcfcfc;
	}

	.newstylebutton:hover {
		color: black;
		border-radius: 5px;
		BACKGROUND-COLOR: #151B54;
		BORDER: 0px solid #808080;
		WIDTH: 80px;
		font-size: 12pt;
		FONT-WEIGHT: bold;
		cursor: hand;
		margin-bottom: 6px;
		margin-top: 6px;
		COLOR: #fcfcfc;
	}

    .l4ablue_banner{
        display:none!important;
        width:0px!important;
    }
    .fields_td{
        width:75%;
    }
    .logo_td{
        width:25%;
    }
	.newstyleselect {
		color: #336699;
		font-size: 12pt;
		border-radius: 5px;
		background-color: #FCFCFC;
		border: 1px solid #336699;
	}

	.newstyletxtarea {
		color: #336699;
		background-color: #EBFAFF;
		border: 1px solid #336699;
		border-radius: 5px;
		font-size: 10pt;
		line-height: 15px;
		padding: 5px 0px 5px 5px;
		margin-bottom: 6px;
		margin-right: 6px;
		margin-top: 2px;
	}

	/* do not group these rules */
	*::-webkit-input-placeholder {
		color: #336699 !important;
	}
	*:-moz-placeholder {
		/* FF 4-18 */
		color: #336699 !important;
	}
	*::-moz-placeholder {
		/* FF 19+ */
		color: #336699 !important;
	}
	*:-ms-input-placeholder {
		/* IE 10+ */
		color: #336699 !important;
	}

	table {
		border-collapse: separate;
		border: 0px;
		border-spacing: 0;
	}
	@charset "utf-8";
	img, object, embed, video {
		max-width: 100%;
	}
	.ie6 img {
		width: 100%;
	}
	.gridContainer {
		margin-left: auto;
		margin-right: auto;
        margin-top:5%!important;
        margin-bottom:5%!important;
		width: 70.36%;
		padding-left: 1.82%;
		padding-right: 1.82%;
	}
	#banner {
		clear: both;
		float: center;
		width: 100%;
		display: block;
		color: #000;
		background-color: #CEE3F6;
		font-size: 18px;
	}
	#navigation {
		clear: both;
		float: left;
		margin-left: 0;
		width: 100%;
		display: block;
	}
	#body {
		clear: both;
		margin-left: auto;
		margin-right: auto;
		width: 100%;
		display: block;
		background-color: #CEE3F6;
		color: #000;
		font-size: 18px;
	}

	/* Tablet Layout: 481px to 768px. Inherits styles from: Mobile Layout. */

	@media only screen and (min-width: 481px) {
		.gridContainer {
			width: 70.675%;
			padding-left: 1.1625%;
			padding-right: 1.1625%;
		}
		#banner {
			clear: both;
			float: center;
			width: 100%;
			display: block;
			font-size: 24px;
			background-color: #CEE3F6;
		}
		#navigation {
			clear: both;
			float: left;
			margin-left: 0;
			width: 100%;
			display: block;
		}
		#body {
			clear: both;
            margin-left: auto;
            margin-right: auto;
			width: 100%;
			display: block;
			font-size: 24px;
			background-color: #CEE3F6;
		}
	}


	@media only screen and (max-width: 480px) {
		.gridContainer {
			width: 95%;
			padding-left: 1.1625%;
			padding-right: 1.1625%;
		}
        .fields_td{
            width:100%;
        }
        .logo_td{
            width:0%;
            display:none;
        }
        .l4ablue_banner{
            display:inline-block!important;
            width:12%!important;
            margin-left:-10%!important;
            margin-top:-20%!important;
        }
		#banner {
			clear: both;
			float: center;
			width: 100%;
			display: block;
			font-size: 24px;
			background-color: #CEE3F6;
		}
		#navigation {
			clear: both;
			float: left;
			margin-left: 0;
			width: 100%;
			display: block;
		}
		#body {
			clear: both;
            margin-left: auto;
            margin-right: auto;
			width: 100%;
			display: block;
			font-size: 24px;
			background-color: #CEE3F6;
		}
	}

	/* Desktop Layout: 769px to a max of 1232px.  Inherits styles from: Mobile Layout and Tablet Layout. */

	@media only screen and (min-width: 769px) {
		.gridContainer {
			width: 50.2%;
			max-width: 1232px;
			padding-left: 0.9%;
			padding-right: 0.9%;
			margin: auto;
		}
		#banner {
			clear: both;
			float: center;
			width: 100%;
			display: block;
			font-size: 36px;
			background-color: #CEE3F6;
		}
		#navigation {
			clear: both;
			float: left;
			margin-left: 0;
			width: 100%;
			display: block;
		}
		#body {
			clear: both;
            margin-left: auto;
            margin-right: auto;
			width: 100%;
			display: block;
			font-size: 36px;
			background-color: #CEE3F6;
		}
	}


.large-label {
    font-size: 20px;
    font-weight: bold;
    height: 50px;
    border-radius: 5px;
    padding-top: 15px;
    margin-top: 20px;
    margin-bottom: 5px;
    width:98%;
}
.med-label {
    font-size: 16px;
    height: 50px;
    border-radius: 5px;
    padding-top: 15px;
    margin-top: 5px;
    margin-bottom: 15px;
    width:98%;
}
</style>

	</head>
	<body bgcolor="#808080" style="line-height:normal;background-color:#808080" >
		<div class="col-xs-12 col-md-12 col-lg-12 no-padding no-margin ajax-contents pagination-centered" style="">
			<!-- FLASH Messages -->
			<div class="col-xs-12 col-md-12 col-lg-12 no-padding no-margin ajax-contents">
			</div>
			</br>
			<div class="gridContainer clearfix newstyletable ">
				<div id="banner" align="center">
					<img src="/img/amjbanner_2013a.png" />
                    <img class="l4ablue_banner" width="70%" src="/img/l4ablue.png" border="0">
				</div>
				<div align="center" class="" id="">
					<table width="95%">
						<tr>
							<td class="fields_td" align="center">  
								
								<span class="label label-danger large-label">Site is currently down for maintenance</span>
								<span class="label label-info med-label">Please try back after Saturday Jan 6th, 08:00 AM EST</span>
								<br/>
								<br/>
								<br/>
							</td>
							<td class="logo_td" valign="middle" align="right">
                                    <img width="70%" src="/img/l4ablue.png" border="0">
                            </td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		</div>

		<!-- Inline Scripts -->
		<!-- / Inline Scripts -->
		<script></script>
	</body>
</html>

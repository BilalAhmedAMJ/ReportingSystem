<? include ("protected.php");
include("../incl/dbcon.php");
$showdocs=1;
?>
<html>
<head>
<title><? echo $amjwho; ?></title>
<link href="../style.css" rel="stylesheet" type="text/css">
<?php include '../incl/headscript.inc'; ?>
</head>
<body bgcolor="#ffffff">
<?php include '../incl/topbar.inc'; ?>
<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td align="right" valign="top">
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="100" valign=top>
        <?php include 'menu.php'; ?></td>
          <td valign="top" align=center><table width="100%" border="0" cellpadding="0" cellspacing="0">

              <tr valign="top">
                <td valign="top" align="center">
                                <?php
                                $HideNew="Y";
                                 ?>
                                        <table width=100% border=0 >
                                                <tr>
                                                        <td align=center>
                                                        <table border="0" width=80% class=newstyletable>
                                                <tr>
                                                        <th ><span >Documents</span><br></th>
                                                </tr>
                                                        <tr>
                                                        <td align=center>
                                                         <?     $test_php='Y';
                                                                include 'incl/downloads.inc'; ?>
                                                        </td>
                                                        </tr>
                                                        </table>
                                                </td>
                                                </tr>
                                                <tr>
                                                  <td align="center"><br></td>
                                                </tr>
                                        </table>
                                </td>
                <td width="160" >
                  <?    include 'taskbar.php'; ?>
                </td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
</table>
<?php include 'incl/bottombar.inc'; ?>
</body>
</html>

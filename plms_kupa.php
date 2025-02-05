<?php
/* Definition File For Oracle Functions                      OS:
* ==================================== * ======================================== *
|                                      | Program ID   : 
|  Global  *  *         ******         | Program Name : plms.php
|         **  *        *      *        | Program No.  : 
|        * *  *        *      *        *---------------------------------------- *
|       *  *  *        *               | Language     : PHP Version 4.3.7
|      *****  *        *               | Project File :
|     *    *  *        *      *        | Make File    :
|    *     *  *        *      *        | Archives     :
|   *      *  ********  ******  System | Remark       :
|                                      |
* -- < History     > ----------------- * ---------------------------------------- *
| 
| Making  : 2004/11/27 TPCA) D.Bukvald
| Update1 : 2007/11/08 C. Socha - Changes on Paint - UBC to ED Insp, move PFC to new position
|                                add OK Vehicles, Change source for T/C and Final offline
|                                 move Process to new position.
| Update2 : 2008/09/14 TPCA L.Kunc - Changes on ASSY LOAD - changed period of error status, added blinking
| Update3 : 2008/10/27 TPCA L.Kunc - ASSY LOAD redirected to kunc.assy_load - for testing 
| Update4 : 2009/04/08 TPCA L.Kunc - ASSY LOAD directed to T_LM_ASSEMBLY
| Update5 : 2009/04/30 TPCA C.Socha - Create WD empty
| Update6 : 2009/08/09 TPCA C.Socha - Create VLT check for CCR
| Update7 : 2011/03/29 TPCA C.Socha - Add Body Rep. for CCR
| Update8 : 2011/06/29 TPCA C.Socha - Paint offlines from ALC
| Update9 : 2011/11/20 TPCA J.Drazan - add pokayoke printers for CCR
| Update10: 2012/10/12 TPCA J. Drazan - Redirect data source from ALC2 to CVQS
| Update11: 2013/03/27 TPCA C.Socha - Remove $def = 581 related with Safety Breaks change
| Update12: 2013/04/09 TPCA J.Drazan - add Emergancy parking 
| Update13: 2014/05/14 TPCA L.Kunc - added HR style ticket # 63169  
| Update14: 2014/06/30 TPCA C.Socha - Canvas Top vizualization
| Update15: 2015/03/23 TPCA L.Kunc - PAINT layout + UBC and SEALER + FINAL DRR
| Update16: 2015/03/23 TPCA J.Drazan - added MASK row table:T_LM_PAINT SPARE_8 1785,4217
|                           and update TC to TC_NG andchange source table from T_LM_GALC to T_LM_PAINT - SPARE_9
| Update17: 2015/11/25 TPCA L.Kunc - added functionality for SCP stop alerting
| Update18: 2015/12/09 TPCA C.Socha - added pokayoke and instructions for CCR in case of low TA Buffer, movement of VLT pokayoke
| Update19: 2016/01/11 TPCA C.Socha - added values for Paint stock T/C, CSS, Prm, Before Buffer
| Update20: 2016/05/27 TPCA C.Socha - count additional values into welding F/B TACK Short line stops
| Update21: 2016/10/26 TPCA M.Kislinger - Migration to new Intranet webserver with new PHP version. Commented out all ocurences of "session_register", because of deprecated in PHP 5.3.0, removed in PHP 5.4.0
| Update22: 2023/01/05 TMMCZ C.Socha - add sound notification for TA buffer max level crossing
| Update23 2023/06/20 TMMCZ L.Miratsky - added values for QC Accesso. actual and plan
| Update23 2024/01/03 TMMCZ P.Kunrt - move warning "Low T/A Buffer" , "Low B/S data"
* -- < Explanation > ------------------------------------------------------------ *
|
|
* -- < Usage       > ------------------------------------------------------------ *
|
|
* =============================================================================== *
*/

 // if ($_get["switch"] == 'yes') { 
   // echo $switch;	
 // } else {
 //   echo $switch;
 //   echo "NO switch";
  //}
//exit;

// odradkovani pro vypis pripadne chyby az pod PLMS obrazovku
//echo '<BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR>';
if (! isset($history)) { 
    $history = 'N'; 
}

//echo $history;

?>
<?php
  session_start();
  
  define("MAX_TIME", 2000000000);
  define("BLK_DELAY", 300);
  define("BLK_DELAY_1S", 1);         //LK - okamzite spusteni houkani
  define("BLK_DELAY_1M", 60);
  define("BLK_DELAY_3M", 180);
  define("BLK_DELAY_15M", 900);
  //define("BLK_DELAY", 10);
  define("BLK_TAKT", 500);
/*
  define("BODY_TT", 56.96);
  define("ASSY_TT", 55.95);
  define("PAINT_TT", 49);
  define("QC_TT", 55.95);
*/  
  if (!IsSet($_SESSION["BLANKING"])) {
    $_SESSION["BLANKING"] = MAX_TIME;
  };
  if (!IsSet($_SESSION["ST1A"])) {
    $_SESSION["ST1A"] = MAX_TIME;
  };
  if (!IsSet($_SESSION["ST2A"])) {
    $_SESSION["ST2A"] = MAX_TIME;
  };
  if (!IsSet($_SESSION["SM_R"])) {
    $_SESSION["SM_R"] = MAX_TIME;
  };
  if (!IsSet($_SESSION["FB_T"])) {
    $_SESSION["FB_T"] = MAX_TIME;
  };
  if (!IsSet($_SESSION["SM_L"])) {
    $_SESSION["SM_L"] = MAX_TIME;
  };
  if (!IsSet($_SESSION["FB_R"])) {
    $_SESSION["FB_R"] = MAX_TIME;
  };
  if (!IsSet($_SESSION["SB_1"])) {
    $_SESSION["SB_1"] = MAX_TIME;
  };
  if (!IsSet($_SESSION["SB_2"])) {
    $_SESSION["SB_2"] = MAX_TIME;
  };
  if (!IsSet($_SESSION["UB"])) {
    $_SESSION["UB"] = MAX_TIME;
  };
  if (!IsSet($_SESSION["TRI"])) {
    $_SESSION["TRI"] = MAX_TIME;
  };
  if (!IsSet($_SESSION["CHASS"])) {
      $_SESSION["CHASS"] = MAX_TIME;
  };
  if (!IsSet($_SESSION["FIN1"])) {
      $_SESSION["FIN1"] = MAX_TIME;
  };
  if (!IsSet($_SESSION["FIN2"])) {
      $_SESSION["FIN2"] = MAX_TIME;
  };
  if (!IsSet($_SESSION["ED_S"])) {
      $_SESSION["ED_S"] = MAX_TIME;
  };
  if (!IsSet($_SESSION["PRIM"])) {
      $_SESSION["PRIM"] = MAX_TIME;
  };
  if (!IsSet($_SESSION["TC_A"])) {
      $_SESSION["TC_A"] = MAX_TIME;
  };
  if (!IsSet($_SESSION["TC_B"])) {
      $_SESSION["TC_B"] = MAX_TIME;
  };
  if (!IsSet($_SESSION["FINAL"])) {
      $_SESSION["FINAL"] = MAX_TIME;
  };
  if (!IsSet($_SESSION["WAX"])) {
      $_SESSION["WAX"] = MAX_TIME;
  };
  if (!IsSet($_SESSION["SKID_C"])) {
      $_SESSION["SKID_C"] = MAX_TIME;
  };
  if (!IsSet($_SESSION["ASS_I"])) {
      $_SESSION["ASS_I"] = MAX_TIME;
  };
  if (!IsSet($_SESSION["TCINSP"])) {
      $_SESSION["TCINSP"] = MAX_TIME;
  };
  if (!IsSet($_SESSION["ASS_L"])) {
      $_SESSION["ASS_L"] = MAX_TIME;
  };
  if (!IsSet($_SESSION["UBC"])) {
      $_SESSION["UBC"] = MAX_TIME;
  };
    if (!IsSet($_SESSION["SEAL"])) {
      $_SESSION["SEAL"] = MAX_TIME;
  };
    if (!IsSet($_SESSION["SEALR"])) {
      $_SESSION["SEALR"] = MAX_TIME;
  };
    if (!IsSet($_SESSION["WEMPTY"])) {
        $_SESSION["WEMPTY"] = MAX_TIME;
	};
	if (!IsSet($_SESSION["TA"])) {
        $_SESSION["TA"] = MAX_TIME;
    };
  if (!IsSet($_SESSION["BLANKING_SND"])) {
    $_SESSION["BLANKING_SND"] = 0;
  };
  if (!IsSet($_SESSION["ST1A_SND"])) {
    $_SESSION["ST1A_SND"] = 0;
  };
  if (!IsSet($_SESSION["ST2A_SND"])) {
    $_SESSION["ST2A_SND"] = 0;
  };
  if (!IsSet($_SESSION["SM_R_SND"])) {
    $_SESSION["SM_R_SND"] = 0;
  };
  if (!IsSet($_SESSION["FB_T_SND"])) {
    $_SESSION["FB_T_SND"] = 0;
  };
  if (!IsSet($_SESSION["SM_L_SND"])) {
    $_SESSION["SM_L_SND"] = 0;
  };
  if (!IsSet($_SESSION["FB_R_SND"])) {
    $_SESSION["FB_R_SND"] = 0;
  };
  if (!IsSet($_SESSION["SB_1_SND"])) {
    $_SESSION["SB_1_SND"] = 0;
  };
  if (!IsSet($_SESSION["SB_2_SND"])) {
    $_SESSION["SB_2_SND"] = 0;
  };
  if (!IsSet($_SESSION["UB_SND"])) {
    $_SESSION["UB_SND"] = 0;
  };
  if (!IsSet($_SESSION["TRI_SND"])) {
    $_SESSION["TRI_SND"] = 0;
  };
  if (!IsSet($_SESSION["CHASS_SND"])) {
    $_SESSION["CHASS_SND"] = 0;
  };
  if (!IsSet($_SESSION["FIN1_SND"])) {
    $_SESSION["FIN1_SND"] = 0;
  };
  if (!IsSet($_SESSION["FIN2_SND"])) {
    $_SESSION["FIN2_SND"] = 0;
  };
  if (!IsSet($_SESSION["ED_S_SND"])) {
    $_SESSION["ED_S_SND"] = 0;
  };
  if (!IsSet($_SESSION["PRIM_SND"])) {
    $_SESSION["PRIM_SND"] = 0;
  };
  if (!IsSet($_SESSION["TC_A_SND"])) {
    $_SESSION["TC_A_SND"] = 0;
  };
  if (!IsSet($_SESSION["TC_B_SND"])) {
    $_SESSION["TC_B_SND"] = 0;
  };
  if (!IsSet($_SESSION["FINAL_SND"])) {
    $_SESSION["FINAL_SND"] = 0;
  };
  if (!IsSet($_SESSION["WAX_SND"])) {
    $_SESSION["WAX_SND"] = 0;
  };
  if (!IsSet($_SESSION["SKID_C_SND"])) {
    $_SESSION["SKID_C_SND"] = 0;
  };
  if (!IsSet($_SESSION["ASS_I_SND"])) {
    $_SESSION["ASS_I_SND"] = 0;
  };
  if (!IsSet($_SESSION["TCINSP_SND"])) {
    $_SESSION["TCINSP_SND"] = 0;
  };
  if (!IsSet($_SESSION["ASS_L_SND"])) {
    $_SESSION["ASS_L_SND"] = 0;
  };
  if (!IsSet($_SESSION["UBC_SND"])) {
    $_SESSION["UBC_SND"] = 0;
  };
   if (!IsSet($_SESSION["SEAL_SND"])) {
    $_SESSION["SEAL_SND"] = 0;
  };
   if (!IsSet($_SESSION["SEALR_SND"])) {
    $_SESSION["SEALR_SND"] = 0;
  };
if (!IsSet($_SESSION["WEMPTY_SND"])) {
    $_SESSION["WEMPTY_SND"] = 0;
};
if (!IsSet($_SESSION["TA_SND"])) {
    $_SESSION["TA_SND"] = 0;
};

if ($history === 'Y') {
    // ******* time setup *******
    if (IsSet($_POST["fTime"])) {
        $fTime = $_POST["fTime"];
    }
    if (IsSet($_POST["fYear"])) {
        $fYear = $_POST["fYear"];
    }
    if (IsSet($_POST["fMonth"])) {
        $fMonth = $_POST["fMonth"];
    }
    if (IsSet($_POST["fDay"])) {
        $fDay = $_POST["fDay"];
    }



    if (isset($fTime)) {
        $fWhere = " where to_char(DATE_STAMP,'YYYYMMDDHH24MISS') = " . $fTime . "";
    } else if (isset($fYear) && isset($fMonth) && isset($fDay)) {
        $fWhere = " order by DATE_STAMP desc";
    } else {
        $fWhere = " order by DATE_STAMP desc";
    }

    // ******* end time setup *******
}


  
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<title>PLMS</title>

<?php        
    if ($history === 'N') {
     echo "<meta http-equiv=\"refresh\" content=\"20\"> ";
    }
?>
  <meta http-equiv="X-UA-Compatible" content="IE=10">
  <meta http-equiv="Page-Enter" content="RevealTrans (Duration=2, Transition=12)">
  <link rel="stylesheet" type="text/css" media="all" href="css/styles.css">
  <script name="hodiny" src="javascript/time.js"></script>
  <!-- *** PLAY SOUND *** -->
  <script name="sound" src="javascript/sound.js"></script>
  
</head>

<?php 

//bof refreshing testing
//include ('log-test.php');
//eof refreshing testing

	$mtime	= time();
	$bl 	= (($mtime-$_SESSION["BLANKING"]) > BLK_DELAY_15M) ? "true" : "false";
	$st1a 	= (($mtime-$_SESSION["ST1A"]) > BLK_DELAY_15M) ? "true" : "false";
	$st2a	= (($mtime-$_SESSION["ST2A"]) > BLK_DELAY_15M) ? "true" : "false";
	$sm_r	= (($mtime-$_SESSION["SM_R"]) > BLK_DELAY) ? "true" : "false";
	$fb_t	= (($mtime-$_SESSION["FB_T"]) > BLK_DELAY) ? "true" : "false";
	$sm_l	= (($mtime-$_SESSION["SM_L"]) > BLK_DELAY) ? "true" : "false";
	$fb_r	= (($mtime-$_SESSION["FB_R"]) > BLK_DELAY) ? "true" : "false";
	$sb_1	= (($mtime-$_SESSION["SB_1"]) > BLK_DELAY) ? "true" : "false";
	$sb_2	= (($mtime-$_SESSION["SB_2"]) > BLK_DELAY_1S) ? "true" : "false";
	$ub	    = (($mtime-$_SESSION["UB"]) > BLK_DELAY) ? "true" : "false";
	$tri	= (($mtime-$_SESSION["TRI"]) > BLK_DELAY_3M) ? "true" : "false";
	$chass	= (($mtime-$_SESSION["CHASS"]) > BLK_DELAY_3M) ? "true" : "false";
	$fin1	= (($mtime-$_SESSION["FIN1"]) > BLK_DELAY_1M) ? "true" : "false";
	$fin2	= (($mtime-$_SESSION["FIN2"]) > BLK_DELAY_1M) ? "true" : "false";
	$ed_s	= (($mtime-$_SESSION["ED_S"]) > BLK_DELAY_1M) ? "true" : "false";
	$prim	= (($mtime-$_SESSION["PRIM"]) > BLK_DELAY) ? "true" : "false";
	$tc_a	= (($mtime-$_SESSION["TC_A"]) > BLK_DELAY) ? "true" : "false";
	$tc_b	= (($mtime-$_SESSION["TC_B"]) > BLK_DELAY) ? "true" : "false";
	$final	= (($mtime-$_SESSION["FINAL"]) > BLK_DELAY) ? "true" : "false";
	$wax	= (($mtime-$_SESSION["WAX"]) > BLK_DELAY_1S) ? "true" : "false";
	$SKID_C	= (($mtime-$_SESSION["SKID_C"]) > BLK_DELAY_1M) ? "true" : "false";
	$ass_i	= (($mtime-$_SESSION["ASS_I"]) > BLK_DELAY) ? "true" : "false";	
	$tcinsp	= (($mtime-$_SESSION["TCINSP"]) > BLK_DELAY) ? "true" : "false";	
	$ass_l	= (($mtime-$_SESSION["ASS_L"]) > BLK_DELAY_1S) ? "true" : "false";  //LK
	//***********************************************
	$ubc	= (($mtime-$_SESSION["UBC"]) > BLK_DELAY) ? "true" : "false";  //LK20150414
	$seal	= (($mtime-$_SESSION["SEAL"]) > BLK_DELAY) ? "true" : "false";  //LK20150414
	$sealr	= (($mtime-$_SESSION["SEALR"]) > BLK_DELAY) ? "true" : "false";  //LK20150414
	$wempty = (($mtime-$_SESSION["WEMPTY"]) > BLK_DELAY_1S) ? "true" : "false";
	$tasnd = (($mtime-$_SESSION["TA"]) > BLK_DELAY_1S) ? "true" : "false";
	
	$fPlaySound = "";
	$fSoundSTR = "playSound();";
        
	//***BLANKING SOUND***
	if ($bl == "true"){
		if ($_SESSION["BLANKING_SND"] == 0){
			$fPlaySound = $fSoundSTR;
			$_SESSION["BLANKING_SND"] = 1;
		}
	} else $_SESSION["BLANKING_SND"] = 0;
	//******
	if ($st1a == "true"){
		if ($_SESSION["ST1A_SND"] == 0){
			$fPlaySound = $fSoundSTR;
			$_SESSION["ST1A_SND"] = 1;
		}
	} else $_SESSION["ST1A_SND"] = 0;
	//*****
	if ($st2a == "true"){
		if ($_SESSION["ST2A_SND"] == 0){
			$fPlaySound = $fSoundSTR;
			$_SESSION["ST2A_SND"] = 1;
		}
	} else $_SESSION["ST2A_SND"] = 0;
	//*****
	if ($sm_r == "true"){
		if ($_SESSION["SM_R_SND"] == 0){
			$fPlaySound = $fSoundSTR;
			$_SESSION["SM_R_SND"] = 1;
		}
	} else $_SESSION["SM_R_SND"] = 0;
	//*****
	if ($fb_t == "true"){
		if ($_SESSION["FB_T_SND"] == 0){
			$fPlaySound = $fSoundSTR;
			$_SESSION["FB_T_SND"] = 1;
		}
	} else $_SESSION["FB_T_SND"] = 0;
	//*****
	if ($sm_l == "true"){
		if ($_SESSION["SM_L_SND"] == 0){
			$fPlaySound = $fSoundSTR;
			$_SESSION["SM_L_SND"] = 1;
		}
	} else $_SESSION["SM_L_SND"] = 0;
	//*****
	if ($fb_r == "true"){
		if ($_SESSION["FB_R_SND"] == 0){
			$fPlaySound = $fSoundSTR;
			$_SESSION["FB_R_SND"] = 1;
		}
	} else $_SESSION["FB_R_SND"] = 0;
	//*****
	if ($sb_1 == "true"){
		if ($_SESSION["SB_1_SND"] == 0){
			$fPlaySound = $fSoundSTR;
			$_SESSION["SB_1_SND"] = 1;
		}
	} else $_SESSION["SB_1_SND"] = 0;
	//*****
	if ($sb_2 == "true"){
		if ($_SESSION["SB_2_SND"] == 0){
			$fPlaySound = $fSoundSTR;
			$_SESSION["SB_2_SND"] = 1;
		}
	} else $_SESSION["SB_2_SND"] = 0;
	//*****
	if ($ub == "true"){
		if ($_SESSION["UB_SND"] == 0){
			$fPlaySound = $fSoundSTR;
			$_SESSION["UB_SND"] = 1;
		}
	} else $_SESSION["UB_SND"] = 0;
	//*****
	if ($tri == "true"){
		if ($_SESSION["TRI_SND"] == 0){
			$fPlaySound = $fSoundSTR;
			$_SESSION["TRI_SND"] = 1;
		}
	} else $_SESSION["TRI_SND"] = 0;
	//*****
	if ($chass == "true"){
		if ($_SESSION["CHASS_SND"] == 0){
			$fPlaySound = $fSoundSTR;
			$_SESSION["CHASS_SND"] = 1;
		}
	} else $_SESSION["CHASS_SND"] = 0;
	//*****
	if ($fin1 == "true"){
		if ($_SESSION["FIN1_SND"] == 0){
			$fPlaySound = $fSoundSTR;
			$_SESSION["FIN1_SND"] = 1;
		}
	} else $_SESSION["FIN1_SND"] = 0;
	//*****
	if ($fin2 == "true"){
		if ($_SESSION["FIN2_SND"] == 0){
			$fPlaySound = $fSoundSTR;
			$_SESSION["FIN2_SND"] = 1;
		}
	} else $_SESSION["FIN2_SND"] = 0;
	//*****
	if ($ed_s == "true"){
		if ($_SESSION["ED_S_SND"] == 0){
			$fPlaySound = $fSoundSTR;
			$_SESSION["ED_S_SND"] = 1;
		}
	} else $_SESSION["ED_S_SND"] = 0;
	//*****
	if ($prim == "true"){
		if ($_SESSION["PRIM_SND"] == 0){
			$fPlaySound = $fSoundSTR;
			$_SESSION["PRIM_SND"] = 1;
		}
	} else $_SESSION["PRIM_SND"] = 0;
	//*****
	if ($tc_a == "true"){
		if ($_SESSION["TC_A_SND"] == 0){
			$fPlaySound = $fSoundSTR;
			$_SESSION["TC_A_SND"] = 1;
		}
	} else $_SESSION["TC_A_SND"] = 0;
	//*****
	if ($tc_b == "true"){
		if ($_SESSION["TC_B_SND"] == 0){
			$fPlaySound = $fSoundSTR;
			$_SESSION["TC_B_SND"] = 1;
		}
	} else $_SESSION["TC_B_SND"] = 0;
	//*****
	if ($final == "true"){
		if ($_SESSION["FINAL_SND"] == 0){
			$fPlaySound = $fSoundSTR;
			$_SESSION["FINAL_SND"] = 1;
		}
	} else $_SESSION["FINAL_SND"] = 0;
	//*****
	if ($wax == "true"){
		if ($_SESSION["WAX_SND"] == 0){
			$fPlaySound = $fSoundSTR;
			$_SESSION["WAX_SND"] = 1;
		}
	} else $_SESSION["WAX_SND"] = 0;
	//*****
	if ($SKID_C == "true"){
		if ($_SESSION["SKID_C_SND"] == 0){
			$fPlaySound = $fSoundSTR;
			$_SESSION["SKID_C_SND"] = 1;
		}
	} else $_SESSION["SKID_C_SND"] = 0;
	//*****
	if ($ass_i == "true"){
		if ($_SESSION["ASS_I_SND"] == 0){
			$fPlaySound = $fSoundSTR;
			$_SESSION["ASS_I_SND"] = 1;
		}
	} else $_SESSION["ASS_I_SND"] = 0;
	//*****
	if ($tcinsp == "true"){
		if ($_SESSION["TCINSP_SND"] == 0){
			$fPlaySound = $fSoundSTR;
			$_SESSION["TCINSP_SND"] = 1;
		}
	} else $_SESSION["TCINSP_SND"] = 0;
	//*****
        if ($ass_l == "true"){
		if ($_SESSION["ASS_L_SND"] == 0){
			$fPlaySound = $fSoundSTR;
			$_SESSION["ASS_L_SND"] = 1;
		}
	} else $_SESSION["ASS_L_SND"] = 0;
	//*****
	  if ($ubc == "true"){
		if ($_SESSION["UBC_SND"] == 0){
			$fPlaySound = $fSoundSTR;
			$_SESSION["UBC_SND"] = 1;
		}
	} else $_SESSION["UBC_SND"] = 0;
	//*****
        if ($seal == "true"){
		if ($_SESSION["SEAL_SND"] == 0){
			$fPlaySound = $fSoundSTR;
			$_SESSION["SEAL_SND"] = 1;
		}
	} else $_SESSION["SEAL_SND"] = 0;
	//*****
	if ($sealr == "true"){
		if ($_SESSION["SEALR_SND"] == 0){
			$fPlaySound = $fSoundSTR;
			$_SESSION["SEALR_SND"] = 1;
		}
	} else $_SESSION["SEALR_SND"] = 0;
    //*****
    if ($wempty == "true"){
        if ($_SESSION["WEMPTY_SND"] == 0){
            $fPlaySound = $fSoundSTR;
            $_SESSION["WEMPTY_SND"] = 1;
        }
	} else $_SESSION["WEMPTY_SND"] = 0;
	
	if ($tasnd == "true"){
        if ($_SESSION["TA_SND"] == 0){
            $fPlaySound = $fSoundSTR;
            $_SESSION["TA_SND"] = 1;
        }
	} else $_SESSION["TA_SND"] = 0;

?>

<?php
// pro test zapnuti zvuku pri kazde strance
//$fPlaySound = "playSound();";
//<audio src="sound/ERROR.wav"></audio>
if ((strpos($_SERVER["HTTP_USER_AGENT"], 'Windows NT 5.0')) ||(strpos($_SERVER["HTTP_USER_AGENT"], 'Windows NT 5.1'))||(strpos($_SERVER["HTTP_USER_AGENT"], 'Windows NT 6.1'))||(strpos($_SERVER["HTTP_USER_AGENT"], 'Windows NT 10.0'))) {
?>

<body BGCOLOR="#AAAAAA" onLoad="blink(<?php echo $bl.",".$st1a.",".$st2a.",".$sm_r.",".$fb_t.",".$sm_l.",".$fb_r.",".$sb_1.",".$sb_2.",".$ub.",".$tri.",".$chass.",".$fin1.",".$fin2.",".$ed_s.",".$prim.",".$tc_a.",".$tc_b.",".$final.",".$wax.",".$SKID_C.",".$tcinsp.",".$ass_i.",".$ass_l.",".$ubc.",".$seal.",".$sealr.",".$wempty.",".$tasnd;?>);runDate();<?php echo $fPlaySound ?>" > <!-- LK20150414 *** -->
<!-- *** PLAY SOUND *** -->
<BGSOUND id="BGSOUND_ID" LOOP=1 SRC="sound/jsilence.mid">
<?php
	}else{
?>
<body BGCOLOR="#AAAAAA" onLoad="blink(<?php echo $bl.",".$st1a.",".$st2a.",".$sm_r.",".$fb_t.",".$sm_l.",".$fb_r.",".$sb_1.",".$sb_2.",".$ub.",".$tri.",".$chass.",".$fin1.",".$fin2.",".$ed_s.",".$prim.",".$tc_a.",".$tc_b.",".$final.",".$wax.",".$SKID_C.",".$tcinsp.",".$ass_i.",".$ass_l.",".$ubc.",".$seal.",".$sealr.",".$wempty.",".$tasnd;?>);runDate();" > <!-- LK20150414 *** -->
<?php
	}
?>

<script language="JavaScript"> 
var a=0; 
var b=0; 
var c=0; 
var d=0; 
var e=0; 
var f=0; 
var g=0; 
var h=0; 
var i=0; 
var j=0; 
var k=0; 
var l=0; 
var m=0; 
var n=0; 
var o=0; 
var p=0; 
var q=0;  //LK20150414
var r=0; 
var s=0; 
var t=0; 
var u=0; 
var v=0; 
var w=0; 
var x=0; 
var y=0;  //LK
var z=0;  //LK20150414
var za=0;
var zb=0;
var xwe=0;
var xta=0;

var ok=true;
//var snd = 0; 
function blink(bl, st1a, st2a, sm_r, fb_t, sm_l, fb_r, sb_1, sb_2, ub, tri, chass, fin1, fin2, ed_s, prim, tc_a, tc_b, finall, wax, SKID_C, tcinsp, ass_i,ass_l,ubc,seal,sealr, wempty, tasnd) { //LK20150414
  ok=true;
  if (bl) { 
    if (a==0 && ok==true) { document.all.blanking.style.visibility="hidden";ok=false;a=1 } 
    if (a==1 && ok==true) { document.all.blanking.style.visibility="visible";ok=false;a=0 } 
  }
  
  
  ok=true;  
  if (st1a) { 
    if (b==0 && ok==true) { document.all.ST1A.style.visibility="hidden";ok=false;b=1 } 
    if (b==1 && ok==true) { document.all.ST1A.style.visibility="visible";ok=false;b=0 } 
  }
  
  ok=true;
  if (st2a) { 
    if (c==0 && ok==true) { document.all.ST2A.style.visibility="hidden";ok=false;c=1 } 
    if (c==1 && ok==true) { document.all.ST2A.style.visibility="visible";ok=false;c=0 } 
  }
  ok=true;
  if (sm_r) { 
    if (d==0 && ok==true) { document.all.SM_R.style.visibility="hidden";ok=false;d=1 } 
    if (d==1 && ok==true) { document.all.SM_R.style.visibility="visible";ok=false;d=0 } 
  }
  ok=true;
  if (fb_t) { 
    if (e==0 && ok==true) { document.all.FB_T.style.visibility="hidden";ok=false;e=1 } 
    if (e==1 && ok==true) { document.all.FB_T.style.visibility="visible";ok=false;e=0 } 
  }
  ok=true;
  if (sm_l) { 
    if (f==0 && ok==true) { document.all.SM_L.style.visibility="hidden";ok=false;f=1 } 
    if (f==1 && ok==true) { document.all.SM_L.style.visibility="visible";ok=false;f=0 } 
  }
  ok=true;
  if (fb_r) { 
    if (g==0 && ok==true) { document.all.FB_R.style.visibility="hidden";ok=false;g=1 } 
    if (g==1 && ok==true) { document.all.FB_R.style.visibility="visible";ok=false;g=0 } 
  }
  ok=true;
  if (sb_1) { 
    if (h==0 && ok==true) { document.all.SB_1.style.visibility="hidden";ok=false;h=1 } 
    if (h==1 && ok==true) { document.all.SB_1.style.visibility="visible";ok=false;h=0 } 
  }
  ok=true;
  if (sb_2) { 
    if (i==0 && ok==true) { document.all.SB_2.style.visibility="hidden";ok=false;i=1 } 
    if (i==1 && ok==true) { document.all.SB_2.style.visibility="visible";ok=false;i=0 } 
  }
  ok=true;
  if (ub) { 
    if (j==0 && ok==true) { document.all.UB.style.visibility="hidden";ok=false;j=1 } 
    if (j==1 && ok==true) { document.all.UB.style.visibility="visible";ok=false;j=0 } 
  }
  ok=true;
  if (tri) { 
    if (k==0 && ok==true) { document.all.TRI.style.visibility="hidden";ok=false;k=1 } 
    if (k==1 && ok==true) { document.all.TRI.style.visibility="visible";ok=false;k=0 } 
  }
  ok=true;
  if (chass) { 
    if (l==0 && ok==true) { document.all.CHASS.style.visibility="hidden";ok=false;l=1 } 
    if (l==1 && ok==true) { document.all.CHASS.style.visibility="visible";ok=false;l=0 } 
  }
  ok=true;
  if (fin1) { 
    if (m==0 && ok==true) { document.all.FIN1.style.visibility="hidden";ok=false;m=1 } 
    if (m==1 && ok==true) { document.all.FIN1.style.visibility="visible";ok=false;m=0 } 
  }
  ok=true;
  if (fin2) { 
    if (n==0 && ok==true) { document.all.FIN2.style.visibility="hidden";ok=false;n=1 } 
    if (n==1 && ok==true) { document.all.FIN2.style.visibility="visible";ok=false;n=0 } 
  }
  ok=true;
  if (ed_s) { 
    if (o==0 && ok==true) { document.all.ED_S.style.visibility="hidden";ok=false;o=1 } 
    if (o==1 && ok==true) { document.all.ED_S.style.visibility="visible";ok=false;o=0 } 
  }
  ok=true;
  if (prim) { 
    if (p==0 && ok==true) { document.all.PRIM.style.visibility="hidden";ok=false;p=1 } 
    if (p==1 && ok==true) { document.all.PRIM.style.visibility="visible";ok=false;p=0 } 
  }
  //******************************************************************  LK20150414
  ok=true;
  if (ubc) { 
    if (q==0 && ok==true) { document.all.UBC.style.visibility="hidden";ok=false;q=1 } 
    if (q==1 && ok==true) { document.all.UBC.style.visibility="visible";ok=false;q=0 } 
  }
  //*******************************************************************  LK20150414
  ok=true;  
  if (tc_a) { 
    if (r==0 && ok==true) { document.all.TC_A.style.visibility="hidden";ok=false;r=1 } 
    if (r==1 && ok==true) { document.all.TC_A.style.visibility="visible";ok=false;r=0 } 
  }
  ok=true;
  if (tc_b) { 
    if (s==0 && ok==true) { document.all.TC_B.style.visibility="hidden";ok=false;s=1 } 
    if (s==1 && ok==true) { document.all.TC_B.style.visibility="visible";ok=false;s=0 } 
  }
  ok=true;
  if (finall) { 
    if (t==0 && ok==true) { document.all.FINAL.style.visibility="hidden";ok=false;t=1 } 
    if (t==1 && ok==true) { document.all.FINAL.style.visibility="visible";ok=false;t=0 } 
  }
  ok=true;
  if (SKID_C) {
    if (u==0 && ok==true) { document.all.SKID_C.style.visibility="hidden";ok=false;u=1 }
    if (u==1 && ok==true) { document.all.SKID_C.style.visibility="visible";ok=false;u=0 }
  }
  ok=true;
  if (ass_i) { 
    if (v==0 && ok==true) { document.all.ASS_I.style.visibility="hidden";ok=false;v=1 } 
    if (v==1 && ok==true) { document.all.ASS_I.style.visibility="visible";ok=false;v=0 } 
  }
  ok=true;
  if (tcinsp) { 
    if (w==0 && ok==true) { document.all.TCINSP.style.visibility="hidden";ok=false;w=1 } 
    if (w==1 && ok==true) { document.all.TCINSP.style.visibility="visible";ok=false;w=0 } 
  }
  ok=true;
  if (wax) { 
    if (x==0 && ok==true) { document.all.WAX.style.visibility="hidden";ok=false;x=1 } 
    if (x==1 && ok==true) { document.all.WAX.style.visibility="visible";ok=false;x=0 } 
  }
  ok=true;                                                                              //LK
  if (ass_l) {                                                                          //LK
    if (y==0 && ok==true) { document.all.ASS_L.style.visibility="hidden";ok=false;y=1 } //LK
    if (y==1 && ok==true) { document.all.ASS_L.style.visibility="visible";ok=false;y=0 } //LK
  }                                                                                      //LK
  
    //******************************************************************  LK20150414
  ok=true;
  if (seal) { 
    if (z==0 && ok==true) { document.all.SEAL.style.visibility="hidden";ok=false;z=1 } 
    if (z==1 && ok==true) { document.all.SEAL.style.visibility="visible";ok=false;z=0 } 
  }
    ok=true;
  if (sealr) { 
    if (za==0 && ok==true) { document.all.SEALR.style.visibility="hidden";ok=false;za=1 } 
    if (za==1 && ok==true) { document.all.SEALR.style.visibility="visible";ok=false;za=0 } 
  }
    ok=true;
    if (wempty) {
        if (xwe==0 && ok==true) { document.all.WEMPTY.style.visibility="hidden";ok=false;xwe=1 }
        if (xwe==1 && ok==true) { document.all.WEMPTY.style.visibility="visible";ok=false;xwe=0 }
    }
	ok=true;
    if (tasnd) {
        if (xta==0 && ok==true) { document.all.TASND.style.visibility="hidden";ok=false;xta=1 }
        if (xta==1 && ok==true) { document.all.TASND.style.visibility="visible";ok=false;xta=0 }
    }

  //*******************************************************************  LK20150414

 setTimeout('blink(<?php echo $bl.",".$st1a.",".$st2a.",".$sm_r.",".$fb_t.",".$sm_l.",".$fb_r.",".$sb_1.",".$sb_2.",".$ub.",".$tri.",".$chass.",".$fin1.",".$fin2.",".$ed_s.",".$prim.",".$tc_a.",".$tc_b.",".$final.",".$wax.",".$SKID_C.",".$tcinsp.",".$ass_i.",".$ass_l.",".$ubc.",".$seal.",".$sealr.",".$wempty.",".$tasnd;?>)',<?php echo BLK_TAKT;?>); //LK 20150414
 //setTimeout('blink(bl,st1a,st2a)',<?php echo BLK_TAKT;?>);
} 
</script>     
    
<?php
  $x = 0.63;
 // $x = 1;
//$x = 0.5;
?>
<STYLE>
  .fTable_1_1{
    padding-top:1px;
    padding-right:1px;
    padding-left:1px;mso-ignore:
    padding;color:Black;
    font-weight:700;
    font-style:normal;
    text-decoration:none;
    font-family:Tahoma, sans-serif;
    mso-font-charset:0;
    mso-number-format:General;
    text-align:center;
    vertical-align:middle;mso-pattern:auto none;
    white-space:nowrap;	    
    <?php
    $f = 14 * $x;
    echo "font-size: {$f}pt;";
    ?>
	border-top:none;
	border-right:none;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	background:white;
  }	
  .fTable_1_2{
    padding-top:1px;
    padding-right:1px;
    padding-left:1px;
    mso-ignore:padding;
    color:Black;
    font-weight:700;
    font-style:normal;
    text-decoration:none;
    font-family:Tahoma, sans-serif;
    mso-font-charset:0;
    mso-number-format:General;
    text-align:center;
    vertical-align:middle;
    mso-pattern:auto none;
    white-space:nowrap;    
    <?php
    $f = 12 * $x;
    echo "font-size: {$f}pt;";
    ?>
	border-top:1.0pt solid Black;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid Black;
	background:#CCFFCC;
  }  
  .fTable_1_3{
    padding-top:1px;
    padding-right:1px;
    padding-left:1px;
    mso-ignore:padding;
    color:Black;
    font-weight:700;
    font-style:normal;
    text-decoration:none;
    font-family:Tahoma, sans-serif;
    mso-font-charset:0;
    mso-number-format:General;
    text-align:center;
    vertical-align:middle;
    mso-pattern:auto none;
    white-space:nowrap;
    
    <?php
    $f = 20 * $x;
    echo "font-size: {$f}pt;";
    ?>
	border-top:1.0pt solid Black;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid Black;
  }
  .fTable_1_1_h{
    padding-top:1px;
    padding-right:1px;
    padding-left:1px;mso-ignore:
    padding;color:Black;
    font-weight:700;
    font-style:normal;
    text-decoration:none;
    font-family:Tahoma, sans-serif;
    mso-font-charset:0;
    mso-number-format:General;
    text-align:center;
    vertical-align:middle;mso-pattern:auto none;
    white-space:nowrap;	    
    <?php
    $f = 20 * $x;
    echo "font-size: {$f}pt;";
    ?>
	border-top:none;
	border-right:none;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	background:white;
  }	
  .fTable_1_2_h{
    padding-top:1px;
    padding-right:1px;
    padding-left:1px;
    mso-ignore:padding;
    color:Black;
    font-weight:700;
    font-style:normal;
    text-decoration:none;
    font-family:Tahoma, sans-serif;
    mso-font-charset:0;
    mso-number-format:General;
    text-align:center;
    vertical-align:middle;
    mso-pattern:auto none;
    white-space:nowrap;    
    <?php
    $f = 16 * $x;
    echo "font-size: {$f}pt;";
    ?>
	border-top:1.0pt solid Black;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid Black;
	background:#CCFFCC;
  }  
  .fTable_1_3_h{
    padding-top:1px;
    padding-right:1px;
    padding-left:1px;
    mso-ignore:padding;
    color:Black;
    font-weight:700;
    font-style:normal;
    text-decoration:none;
    font-family:Tahoma, sans-serif;
    mso-font-charset:0;
    mso-number-format:General;
    text-align:center;
    vertical-align:middle;
    mso-pattern:auto none;
    white-space:nowrap;
    
    <?php
    $f = 40 * $x;
    echo "font-size: {$f}pt;";
    ?>
	border-top:1.0pt solid Black;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid Black;
  }
  .fTable_1_2_t{
    padding-top:1px;
    padding-right:1px;
    padding-left:1px;
    mso-ignore:padding;
    color:Black;
    font-weight:700;
    font-style:normal;
    text-decoration:none;
    font-family:Tahoma, sans-serif;
    mso-font-charset:0;
    mso-number-format:General;
    text-align:center;
    vertical-align:middle;
    mso-pattern:auto none;
    white-space:nowrap;    
    <?php
    $f = 12 * $x;
    echo "font-size: {$f}pt;";
    ?>
	border-top:none;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid Black;
	background:#CCFFCC;
  }  
  .fTable_1_3_t{
    padding-top:1px;
    padding-right:1px;
    padding-left:1px;
    mso-ignore:padding;
    color:Black;
    font-weight:700;
    font-style:normal;
    text-decoration:none;
    font-family:Tahoma, sans-serif;
    mso-font-charset:0;
    mso-number-format:General;
    text-align:center;
    vertical-align:middle;
    mso-pattern:auto none;
    white-space:nowrap;
    
    <?php
    $f = 20 * $x;
    echo "font-size: {$f}pt;";
    ?>
	border-top:1.0pt solid Black;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid Black;
  }  
  .fTable_1_3_t2{
    padding-top:1px;
    padding-right:1px;
    padding-left:1px;
    mso-ignore:padding;
    color:Black;
    font-weight:700;
    font-style:normal;
    text-decoration:none;
    font-family:Tahoma, sans-serif;
    mso-font-charset:0;
    mso-number-format:General;
    text-align:center;
    vertical-align:middle;
    mso-pattern:auto none;
    white-space:nowrap;
    
    <?php
    $f = 25 * $x;
    echo "font-size: {$f}pt;";
    ?>
	border-top:1.0pt solid Black;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid Black;
  }  
  .fTable_2_1{
    padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:Black;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Tahoma, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	mso-pattern:auto none;
	white-space:nowrap;
	
    <?php
    $f = 17 * $x;
    echo "font-size: {$f}px;";
    ?>
	border-top:1.0px solid Black;
	border-right:1.0px solid Black;
	border-bottom:1.0px solid Black;
	border-left:1.0px solid Black;
  }
  .fTable_2_1_pai{
    padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:Black;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Tahoma, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	mso-pattern:auto none;
	white-space:nowrap;
	
    <?php
    $f = 17 * $x;
    echo "font-size: {$f}px;";
    ?>
	border-top:0px solid Black;
	border-right:0px solid Black;
	border-bottom:0px solid Black;
	border-left:0px solid Black;
  }
  .fTable_2_2{
    padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:Black;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Tahoma, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	mso-pattern:auto none;
	white-space:nowrap;
		
    <?php
    $f = 11 * $x;
    echo "font-size: {$f}pt;";
    ?>
	border-top:1.0px solid black;
	border-right:1.0px solid black;
	border-bottom:none;
	border-left:none;
    <?php
    $f = 50 * $x;
    echo "width: {$f}px;";
    ?>
    <?php
    $f = 10 * $x;
    echo "height: {$f}px;";
    ?>
  }
  .fTable_2_3{
    padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:Black;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Tahoma, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	mso-pattern:auto none;
	white-space:nowrap;
	
    <?php
    $f = 16 * $x;
    echo "font-size: {$f}pt;";
    ?>
	border-top:1.0px solid black;
	border-right:1.0px solid black;
	border-bottom:1.0px solid black;
	border-left:none;
  }
  .fTable_2_4{
    padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:Black;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Tahoma, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	mso-pattern:auto none;
	white-space:nowrap;
	
    <?php
    $f = 11 * $x;
    echo "font-size: {$f}pt;";
    ?>
	border-top:1.0px solid black;
	border-right:none;
	border-bottom:none;
	border-left:1.0px solid black;
    <?php
    $f = 50 * $x;
    echo "width: {$f}px;";
    ?>
    <?php
    $f = 10 * $x;
    echo "height: {$f}px;";
    ?>
  }
  .fTable_2_5{
    padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:Black;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Tahoma, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	mso-pattern:auto none;
	white-space:nowrap;
	
    <?php
    $f = 16 * $x;
    echo "font-size: {$f}pt;";
    ?>
	border-top:1.0px solid black;
	border-right:none;
	border-bottom:1.0px solid black;
	border-left:1.0px solid black;
  }
  .fTable_2_6{
    padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:Black;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Tahoma, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	mso-pattern:auto none;
	white-space:nowrap;
	
    <?php
    $f = 11 * $x;
    echo "font-size: {$f}pt;";
    ?>
	border-top:none;
	border-right:1.0px solid black;
	border-bottom:1.0px solid black;
	border-left:1.0px solid black;
    <?php
    $f = 30 * $x;
    echo "width: {$f}px;";
    ?>
    <?php
    $f = 10 * $x;
    echo "height: {$f}px;";
    ?>
  }
  .fTable_2_7{
    padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:Black;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Tahoma, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	mso-pattern:auto none;
	white-space:nowrap;
	
    <?php
    $f = 16 * $x;
    echo "font-size: {$f}pt;";
    ?>
	border-top:none;
	border-right:1.0px solid black;
	border-bottom:1.0px solid black;
	border-left:1.0px solid black;
    <?php
    $f = 30 * $x;
    echo "height: {$f}px;";
    ?>
  }
  .fTable_2_8{
    padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:Black;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Tahoma, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	mso-pattern:auto none;
	white-space:nowrap;
	
    <?php
    $f = 11 * $x;
    echo "font-size: {$f}pt;";
    ?>
	border-top:1.0px solid black;
	border-right:1.0px solid black;
	border-bottom:none;
	border-left:1.0px solid black;
    <?php
    $f = 30 * $x;
    echo "width: {$f}px;";
    ?>
    <?php
    $f = 10 * $x;
    echo "height: {$f}px;";
    ?>
  }
  .fTable_2_9{
    padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:Black;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Tahoma, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	mso-pattern:auto none;
	white-space:nowrap;
	
    <?php
    $f = 16 * $x;
    echo "font-size: {$f}pt;";
    ?>
	border-top:1.0px solid black;
	border-right:1.0px solid black;
	border-bottom:none;
	border-left:1.0px solid black;
    <?php
    $f = 30 * $x;
    echo "height: {$f}px;";
    ?>
  }
  .fTable_3_1{
    padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:Black;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Tahoma, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	mso-pattern:auto none;
	white-space:nowrap;
	
    <?php
    $f = 12 * $x;
    echo "font-size: {$f}pt;";
    ?>
    <?php
    $f = 2 * $x;
    echo "border: {$f}pt solid white;";
    ?>
    
	background:silver;
  }
  .fTable_3_2{
    padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:Black;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Tahoma, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	mso-pattern:auto none;
	white-space:nowrap;
	
    <?php
    $f = 25 * $x;
    echo "font-size: {$f}pt;";
    ?>
	border-top:none;
    <?php
    $f = 2 * $x;
    echo "border-right: {$f}pt solid white;";
    echo "border-bottom: {$f}pt solid white;";
    echo "border-left: {$f}pt solid white;";
    ?>
  }
.fTable_3_3{
    padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:Black;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Tahoma, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	mso-pattern:auto none;
	white-space:nowrap;
	
    <?php
    $f = 12 * $x;
    echo "font-size: {$f}pt;";
    ?>
	border-top:none;
    <?php
    $f = 2 * $x;
    echo "border-right: {$f}pt solid white;";
    echo "border-bottom: {$f}pt solid white;";
    ?>
	border-left:none;
	background:silver;
    <?php
    $f = 30 * $x;
    echo "height: {$f}px;";
    ?>
  }
  .fTable_4_1{
    padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:Black;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Tahoma, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	mso-pattern:auto none;
	white-space:nowrap;
	
    <?php
    $f = 12 * $x;
    echo "font-size: {$f}pt;";
    ?>
	border-top:1.0pt solid Black;
	border-right:1.0pt solid Black;
	border-bottom:none;
	border-left:1.0pt solid Black;
	background:silver;
  }
  .fTable_4_2{
    padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:Black;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Tahoma, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	mso-pattern:auto none;
	white-space:nowrap;
	
    <?php
    $f = 24 * $x;
    echo "font-size: {$f}pt;";
    ?>
	border-top:1.0pt solid black;
	border-right:1.0pt solid black;
	border-bottom:1.0pt solid black;
	border-left:1.0pt solid black;
  }  
  .fTable_4_3{
    padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:Black;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Tahoma, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	mso-pattern:auto none;
	white-space:nowrap;
	
    <?php
    $f = 12 * $x;
    echo "font-size: {$f}pt;";
    ?>
	border-top:none;
	border-right:1.0pt solid black;
	border-bottom:1.0pt solid black;
	border-left:none;
	background:silver;
  }  
  .fTable_5_1{
    padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:Black;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Tahoma, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	mso-pattern:auto none;
	white-space:nowrap;
	
    <?php
    $f = 12 * $x;
    echo "font-size: {$f}pt;";
    ?>
	border-top:1.0pt solid white;
	border-right:1.0pt solid white;
	border-bottom:1.0pt solid white;
	border-left:1.0pt solid white;
	background:#00CCFF;
  }
  .fTable_5_2{
    padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Tahoma, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:right;
	vertical-align:middle;
	mso-pattern:auto none;
	white-space:nowrap;
	
    <?php
    $f = 22 * $x;
    echo "font-size: {$f}pt;";
    ?>
	border-top:none;
	border-right:none;
	border-bottom:1.0pt solid white;
	border-left:none;
  }
  .fTable_5_3{
    padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:Black;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Tahoma, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:right;
	vertical-align:bottom;
	mso-pattern:auto none;
	white-space:nowrap;
	
    <?php
    $f = 12 * $x;
    echo "font-size: {$f}pt;";
    ?>
	border-top:none;
	border-right:1.0pt solid white;
	border-bottom:1.0pt solid white;
	
  }
  .fTable_6_1{
    padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:Black;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Tahoma, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	mso-pattern:auto none;
	white-space:nowrap;
	
    <?php
    $f = 20 * $x;
    echo "font-size: {$f}pt;";
    ?>
	border:1.0pt solid white;
  }
  .fTable_6_2{
    padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:Black;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Tahoma, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	mso-pattern:auto none;
	white-space:nowrap;
	
    <?php
    $f = 12 * $x;
    echo "font-size: {$f}pt;";
    ?>
	border:1.0pt solid white;
	border-top:none;
	border-right:1.0pt solid white;
	border-bottom:1.0pt solid white;
	border-left:none;
	background:silver;
    <?php
    $f = 20 * $x;
    echo "height:{$f}px;";
    ?>
  }
.fTable_6_3{
    padding-top:1px;
    padding-right:1px;
    padding-left:1px;
    mso-ignore:padding;
    color:white;
    font-weight:700;
    font-style:normal;
    text-decoration:none;
    font-family:Tahoma, sans-serif;
    mso-font-charset:0;
    mso-number-format:General;
    text-align:center;
    vertical-align:middle;
    mso-pattern:auto none;
    white-space:nowrap;
    
    <?php
    $f = 20 * $x;
    echo "font-size: {$f}pt;";
    ?>
	border-top:1.0pt solid Black;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid Black;
  }
  hr {
   margin: 0; 
   padding: 0;
   border: 0;
   }

  .fTable_7_1{
    padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:Black;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Tahoma, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	mso-pattern:auto none;
	white-space:nowrap;
	
    <?php
    $f = 12 * $x;
    echo "font-size: {$f}pt;";
    ?>
	border-top:1.0pt solid Black;
	border-right:1.0pt solid Black;
	border-bottom:none;
	border-left:1.0pt solid Black;
	background:silver;
  }
  .fTable_7_2{
    padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:Black;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Tahoma, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	mso-pattern:auto none;
	white-space:nowrap;
	
    <?php
    $f = 16 * $x;
    echo "font-size: {$f}pt;";
    ?>
	border-top:1.0pt solid black;
	border-right:1.0pt solid black;
	border-bottom:1.0pt solid black;
	border-left:1.0pt solid black;
  }  
  .fTable_7_3{
    padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:Black;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Tahoma, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	mso-pattern:auto none;
	white-space:nowrap;
	
    <?php
    $f = 10 * $x;
    echo "font-size: {$f}pt;";
    ?>
	border-top:none;
	border-right:1.0pt solid black;
	border-bottom:1.0pt solid black;
	border-left:none;
	background:silver;
  }  


.fccrnote{
    padding-top:1px;
    padding-right:1px;
    padding-left:1px;
    mso-ignore:padding;
    color:Black;
    font-weight:500;
    font-style:normal;
    text-decoration:none;
    font-family:Tahoma, sans-serif;
    mso-font-charset:0;
    mso-number-format:General;
    text-align:center;
    vertical-align:middle;
    mso-pattern:auto none;
    white-space:nowrap;
    
    <?php
    $f = 15 * $x;
    echo "font-size: {$f}pt;";
    ?>
	border-top:1.0pt solid Black;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid Black;
  }

.blink {
 -webkit-animation: BLINK-ANIM 1s infinite; /* Safari 4+ */
  -moz-animation:    BLINK-ANIM 1s infinite; /* Fx 5+ */
  -o-animation:      BLINK-ANIM 1s infinite; /* Opera 12+ */
  animation:         BLINK-ANIM 1s infinite; /* IE 10+, Fx 29+ */
}

@-webkit-keyframes BLINK-ANIM {
0%, 49% {
    background-color: white;
}
50%, 100% {
    background-color: red;
}
}

</STYLE>

<?php

if ($history === 'Y') {
    $query_substr1 = " to_char(DATE_STAMP,'YYYY/MM/DD HH24:MI:SS') as TIME, ";
    $query_substr2 = "HIS";
} else {
    $query_substr1 = "";
    $query_substr2 = "LM";
    $fWhere = "";
}

// ******* ORACLE LOAD DATA BEGIN *******

            require("dbconnect/oracle.php");

            // log on to the database
//            oracle_logon($conn, "linemon", "linemon", "CVQSB.TPCA.CZ");
oracle_logon($conn, "LIN", "LINN", "CVQ");

//select all Stamp 1A data
            /* if ($mkldebug==1) { $starttime=microtime_float(); $startmemory=memory_get_usage(); }
              $fquery = "select to_char(DATE_STAMP,'YYYY/MM/DD HH24:MI:SS') as TIME, s1.CUREXSOT_ST1, s1.ACTSOT_ST1, s1.ACTGSPH_ST1, s1.OPEAVIL_ST1, s1.WT_1A, "
              ."s1.OVERTIME_ST1, s1.LINESTS_ST1, s1.STPTIMO_ST1, s1.STPTIME_ST1, s1.STPSTSO_ST1 from T_HIS_STAMP_ST1 s1".$fWhere;
              $farray = array();
              $frows = 0;
              oracle_select($conn, $fquery, $farray, $frows);
              if ($mkldebug==1) { $stoptime=microtime_float(); $stopmemory = memory_get_usage();echo 'MKLDEBUG:Select: '.$fquery.'<br>'.'MKLDEBUG:CAS: '.($stoptime-$starttime).'<br>'.'MKLDEBUG:Vet: '.$frows.'<br>'.'MKLDEBUG:Velikost: '.number_format($stopmemory-$startmemory).'&nbsp;B<br>';}
             */
            /* puvodni verze
              MKLDEBUG:Select: select to_char(DATE_STAMP,'YYYY/MM/DD HH24:MI:SS') as TIME, s1.CUREXSOT_ST1, s1.ACTSOT_ST1, s1.ACTGSPH_ST1, s1.OPEAVIL_ST1, s1.WT_1A, s1.OVERTIME_ST1, s1.LINESTS_ST1, s1.STPTIMO_ST1, s1.STPTIME_ST1, s1.STPSTSO_ST1 from T_HIS_STAMP_ST1 s1 order by DATE_STAMP desc
              MKLDEBUG:CAS: 0.18720102310181
              MKLDEBUG:Vet: 9044
              MKLDEBUG:Velikost: 10,418,728 B
             */

//            if ($mkldebug == 1) {
//                $starttime = microtime_float();
//                $startmemory = memory_get_usage();
//            }
            $fquery = "select * from ("
                    . "select "
                    . $query_substr1
                    . " s1.CUREXSOT_ST1"
                    . ",s1.ACTSOT_ST1"
                    . ",s1.ACTGSPH_ST1"
                    . ",s1.OPEAVIL_ST1"
                    . ",s1.WT_1A"
                    . ",s1.OVERTIME_ST1"
                    . ",s1.LINESTS_ST1"
                    . ",s1.STPTIMO_ST1"
                    . ",s1.STPTIME_ST1"
                    . ",s1.STPSTSO_ST1"
                    . " from T_".$query_substr2."_STAMP_ST1 s1 "
                    . $fWhere
                    //." order by DATE_STAMP desc"
                    . ")"
                    . "where rownum=1"
            ;
            
//            echo $fquery;
            $farray = array();
            $frows = 0;
            oracle_select($conn, $fquery, $farray, $frows);
//            if ($mkldebug == 1) {
//                $stoptime = microtime_float();
//                $stopmemory = memory_get_usage();
//                echo 'MKLDEBUG:Select: ' . $fquery . '<br>' . 'MKLDEBUG:CAS: ' . ($stoptime - $starttime) . '<br>' . 'MKLDEBUG:Vet: ' . $frows . '<br>' . 'MKLDEBUG:Velikost: ' . number_format($stopmemory - $startmemory) . '&nbsp;B<br><hr>';
//            }
            /* nova verze
              MKLDEBUG:Select: select * from (select to_char(DATE_STAMP,'YYYY/MM/DD HH24:MI:SS') as TIME,s1.CUREXSOT_ST1,s1.ACTSOT_ST1,s1.ACTGSPH_ST1,s1.OPEAVIL_ST1,s1.WT_1A,s1.OVERTIME_ST1,s1.LINESTS_ST1,s1.STPTIMO_ST1,s1.STPTIME_ST1,s1.STPSTSO_ST1 from T_HIS_STAMP_ST1 s1 order by DATE_STAMP desc)where rownum=1
              MKLDEBUG:CAS: 0.015599966049194
              MKLDEBUG:Vet: 1
              MKLDEBUG:Velikost: 3,872 B
             */

//select all Stamp 2A data
            /*
              if ($mkldebug==1) { $starttime=microtime_float(); $startmemory=memory_get_usage(); }
              $fquery2 = "select s2.CUREXSOT_ST2, s2.ACTSOT_ST2, s2.ACTGSPH_ST2, s2.OPEAVIL_ST2, s2.WT_2A, "
              ."s2.OVERTIME_ST2, s2.LINESTS_ST2, s2.STPTIMO_ST2, s2.STPTIME_ST2, s2.STPSTSO_ST2 from T_HIS_STAMP_ST2 s2".$fWhere;
              $farray2 = array();
              $frows2 = 0;
              oracle_select($conn, $fquery2, $farray2, $frows2);
              if ($mkldebug==1) { $stoptime=microtime_float(); $stopmemory = memory_get_usage(); echo 'MKLDEBUG:Select: '.$fquery2.'<br>'.'MKLDEBUG:CAS: '.($stoptime-$starttime).'<br>'.'MKLDEBUG:Vet: '.$frows2.'<br>'.'MKLDEBUG:Velikost: '.number_format($stopmemory-$startmemory).'&nbsp;B<br><hr>';}
             */
            /* stara verze
              MKLDEBUG:Select: select s2.CUREXSOT_ST2, s2.ACTSOT_ST2, s2.ACTGSPH_ST2, s2.OPEAVIL_ST2, s2.WT_2A, s2.OVERTIME_ST2, s2.LINESTS_ST2, s2.STPTIMO_ST2, s2.STPTIME_ST2, s2.STPSTSO_ST2 from T_HIS_STAMP_ST2 s2 order by DATE_STAMP desc
              MKLDEBUG:CAS: 0.17160081863403
              MKLDEBUG:Vet: 9044
              MKLDEBUG:Velikost: 9,339,912 B
             */
//            if ($mkldebug == 1) {
//                $starttime = microtime_float();
//                $startmemory = memory_get_usage();
//            }
            $fquery2 = "select * from ("
                    . "select s2.CUREXSOT_ST2, s2.ACTSOT_ST2, s2.ACTGSPH_ST2, s2.OPEAVIL_ST2, s2.WT_2A, "
                    . "s2.OVERTIME_ST2, s2.LINESTS_ST2, s2.STPTIMO_ST2, s2.STPTIME_ST2, s2.STPSTSO_ST2 from T_".$query_substr2."_STAMP_ST2 s2" . $fWhere
                    . ")"
                    . "where rownum=1"
            ;
            $farray2 = array();
            $frows2 = 0;
            oracle_select($conn, $fquery2, $farray2, $frows2);

//            if ($mkldebug == 1) {
//                $stoptime = microtime_float();
//                $stopmemory = memory_get_usage();
//                echo 'MKLDEBUG:Select: ' . $fquery2 . '<br>' . 'MKLDEBUG:CAS: ' . ($stoptime - $starttime) . '<br>' . 'MKLDEBUG:Vet: ' . $frows2 . '<br>' . 'MKLDEBUG:Velikost: ' . number_format($stopmemory - $startmemory) . '&nbsp;B<br><hr>';
//            }
            /* nova verze
              MKLDEBUG:Select: select * from (select s2.CUREXSOT_ST2, s2.ACTSOT_ST2, s2.ACTGSPH_ST2, s2.OPEAVIL_ST2, s2.WT_2A, s2.OVERTIME_ST2, s2.LINESTS_ST2, s2.STPTIMO_ST2, s2.STPTIME_ST2, s2.STPSTSO_ST2 from T_HIS_STAMP_ST2 s2 order by DATE_STAMP desc)where rownum=1
              MKLDEBUG:CAS: 0
              MKLDEBUG:Vet: 1
              MKLDEBUG:Velikost: 3,344 B
             */


//select all BODY data
            /*
              if ($mkldebug==1) { $starttime=microtime_float(); $startmemory=memory_get_usage(); }
              $fquery3 = "select s3.CUREXPRD_BL, s3.ACTPRD_BL, s3.STPTIME_BDY, s3.OPEAVIL_SB2, s3.OVERTIME_BDY, "
              ."s3.LINESTS_BDY, s3.LINESTS_UB, s3.STPTIMS_UB, s3.STPTIMO_UB, s3.STPTIMF_UB, "
              ."s3.STPSTSS_UB, s3.STPSTSO_UB, s3.STPSTSF_UB, "
              ."s3.LINESTS_SML, s3.STPTIMO_SML, s3.STPTIMF_SML, s3.STPTIMS_SML, "
              ."s3.STPSTSS_SML, s3.STPSTSO_SML, s3.STPSTSF_SML, "
              ."s3.LINESTS_FBRS, s3.STPTIMO_FBRS, s3.STPTIMF_FBRS, s3.STPTIMS_FBRS, "
              ."s3.STPSTSS_FBRS, s3.STPSTSO_FBRS, s3.STPSTSF_FBRS, "
              ."s3.LINESTS_SB1, s3.STPTIMO_SB1, s3.STPTIMF_SB1, s3.STPTIMS_SB1, "
              ."s3.STPSTSS_SB1, s3.STPSTSO_SB1, s3.STPSTSF_SB1, "
              ."s3.LINESTS_SB2, s3.STPTIMO_SB2, s3.STPTIMF_SB2, s3.STPTIMS_SB2, "
              ."s3.STPSTSS_SB2, s3.STPSTSO_SB2, s3.STPSTSF_SB2, "
              ."s3.LINESTS_SMR, s3.STPTIMO_SMR, s3.STPTIMF_SMR, s3.STPTIMS_SMR, "
              ."s3.STPSTSS_SMR, s3.STPSTSO_SMR, s3.STPSTSF_SMR, s3.STPTIMS_FBTK, "
              ."s3.LINESTS_FBTK, s3.STPTIMO_FBTK, s3.STPTIMF_FBTK, s3.STPSTSS_FBTK, "
              ."s3.STPSTSO_FBTK, s3.STPSTSF_FBTK, s3.WT_D0, s3.TT_BDY, s3.PLAN_BDY from T_HIS_BODY s3".$fWhere;
              $farray3 = array();
              $frows3 = 0;
              oracle_select($conn, $fquery3, $farray3, $frows3);
              if ($mkldebug==1) { $stoptime=microtime_float(); $stopmemory = memory_get_usage(); echo 'MKLDEBUG:Select: '.$fquery3.'<br>'.'MKLDEBUG:CAS: '.($stoptime-$starttime).'<br>'.'MKLDEBUG:Vet: '.$frows3.'<br>'.'MKLDEBUG:Velikost: '.number_format($stopmemory-$startmemory).'&nbsp;B<br><hr>';}
             */
            /* stara verze
              MKLDEBUG:Select: select s3.CUREXPRD_BL, s3.ACTPRD_BL, s3.STPTIME_BDY, s3.OPEAVIL_SB2, s3.OVERTIME_BDY, s3.LINESTS_BDY, s3.LINESTS_UB, s3.STPTIMS_UB, s3.STPTIMO_UB, s3.STPTIMF_UB, s3.STPSTSS_UB, s3.STPSTSO_UB, s3.STPSTSF_UB, s3.LINESTS_SML, s3.STPTIMO_SML, s3.STPTIMF_SML, s3.STPTIMS_SML, s3.STPSTSS_SML, s3.STPSTSO_SML, s3.STPSTSF_SML, s3.LINESTS_FBRS, s3.STPTIMO_FBRS, s3.STPTIMF_FBRS, s3.STPTIMS_FBRS, s3.STPSTSS_FBRS, s3.STPSTSO_FBRS, s3.STPSTSF_FBRS, s3.LINESTS_SB1, s3.STPTIMO_SB1, s3.STPTIMF_SB1, s3.STPTIMS_SB1, s3.STPSTSS_SB1, s3.STPSTSO_SB1, s3.STPSTSF_SB1, s3.LINESTS_SB2, s3.STPTIMO_SB2, s3.STPTIMF_SB2, s3.STPTIMS_SB2, s3.STPSTSS_SB2, s3.STPSTSO_SB2, s3.STPSTSF_SB2, s3.LINESTS_SMR, s3.STPTIMO_SMR, s3.STPTIMF_SMR, s3.STPTIMS_SMR, s3.STPSTSS_SMR, s3.STPSTSO_SMR, s3.STPSTSF_SMR, s3.STPTIMS_FBTK, s3.LINESTS_FBTK, s3.STPTIMO_FBTK, s3.STPTIMF_FBTK, s3.STPSTSS_FBTK, s3.STPSTSO_FBTK, s3.STPSTSF_FBTK, s3.WT_D0, s3.TT_BDY, s3.PLAN_BDY from T_HIS_BODY s3 order by DATE_STAMP desc
              MKLDEBUG:CAS: 0.54600310325623
              MKLDEBUG:Vet: 9044
              MKLDEBUG:Velikost: 54,169,472 B
             */
//            if ($mkldebug == 1) {
//                $starttime = microtime_float();
//                $startmemory = memory_get_usage();
//            }
            $fquery3 = "select * from ("
                    . "select s3.CUREXPRD_BL, s3.ACTPRD_BL, s3.STPTIME_BDY, s3.OPEAVIL_SB2, s3.OVERTIME_BDY, "
                    . "s3.LINESTS_BDY, s3.LINESTS_UB, s3.STPTIMS_UB, s3.STPTIMO_UB, s3.STPTIMF_UB, "
                    . "s3.STPSTSS_UB, s3.STPSTSO_UB, s3.STPSTSF_UB, "
                    . "s3.LINESTS_SML, s3.STPTIMO_SML, s3.STPTIMF_SML, s3.STPTIMS_SML, "
                    . "s3.STPSTSS_SML, s3.STPSTSO_SML, s3.STPSTSF_SML, "
                    . "s3.LINESTS_FBRS, s3.STPTIMO_FBRS, s3.STPTIMF_FBRS, s3.STPTIMS_FBRS, "
                    . "s3.STPSTSS_FBRS, s3.STPSTSO_FBRS, s3.STPSTSF_FBRS, "
                    . "s3.LINESTS_SB1, s3.STPTIMO_SB1, s3.STPTIMF_SB1, s3.STPTIMS_SB1, "
                    . "s3.STPSTSS_SB1, s3.STPSTSO_SB1, s3.STPSTSF_SB1, "
                    . "s3.LINESTS_SB2, s3.STPTIMO_SB2, s3.STPTIMF_SB2, s3.STPTIMS_SB2, "
                    . "s3.STPSTSS_SB2, s3.STPSTSO_SB2, s3.STPSTSF_SB2, "
                    . "s3.LINESTS_SMR, s3.STPTIMO_SMR, s3.STPTIMF_SMR, s3.STPTIMS_SMR, "
                    . "s3.STPSTSS_SMR, s3.STPSTSO_SMR, s3.STPSTSF_SMR, s3.STPTIMS_FBTK, "
                    . "s3.LINESTS_FBTK, s3.STPTIMO_FBTK, s3.STPTIMF_FBTK, s3.STPSTSS_FBTK, "
                    . "s3.STPSTSO_FBTK, s3.STPSTSF_FBTK, s3.WT_D0, s3.TT_BDY, s3.PLAN_BDY, s3.ACTINVA1_WEL, s3.ACTINVA1 from T_".$query_substr2."_BODY s3" . $fWhere
                    . ")"
                    . "where rownum=1"
            ;
            $farray3 = array();
            $frows3 = 0;
            oracle_select($conn, $fquery3, $farray3, $frows3);
//            if ($mkldebug == 1) {
//                $stoptime = microtime_float();
//                $stopmemory = memory_get_usage();
//                echo 'MKLDEBUG:Select: ' . $fquery3 . '<br>' . 'MKLDEBUG:CAS: ' . ($stoptime - $starttime) . '<br>' . 'MKLDEBUG:Vet: ' . $frows3 . '<br>' . 'MKLDEBUG:Velikost: ' . number_format($stopmemory - $startmemory) . '&nbsp;B<br><hr>';
//            }
            /* nova verze
              MKLDEBUG:Select: select * from (select s3.CUREXPRD_BL, s3.ACTPRD_BL, s3.STPTIME_BDY, s3.OPEAVIL_SB2, s3.OVERTIME_BDY, s3.LINESTS_BDY, s3.LINESTS_UB, s3.STPTIMS_UB, s3.STPTIMO_UB, s3.STPTIMF_UB, s3.STPSTSS_UB, s3.STPSTSO_UB, s3.STPSTSF_UB, s3.LINESTS_SML, s3.STPTIMO_SML, s3.STPTIMF_SML, s3.STPTIMS_SML, s3.STPSTSS_SML, s3.STPSTSO_SML, s3.STPSTSF_SML, s3.LINESTS_FBRS, s3.STPTIMO_FBRS, s3.STPTIMF_FBRS, s3.STPTIMS_FBRS, s3.STPSTSS_FBRS, s3.STPSTSO_FBRS, s3.STPSTSF_FBRS, s3.LINESTS_SB1, s3.STPTIMO_SB1, s3.STPTIMF_SB1, s3.STPTIMS_SB1, s3.STPSTSS_SB1, s3.STPSTSO_SB1, s3.STPSTSF_SB1, s3.LINESTS_SB2, s3.STPTIMO_SB2, s3.STPTIMF_SB2, s3.STPTIMS_SB2, s3.STPSTSS_SB2, s3.STPSTSO_SB2, s3.STPSTSF_SB2, s3.LINESTS_SMR, s3.STPTIMO_SMR, s3.STPTIMF_SMR, s3.STPTIMS_SMR, s3.STPSTSS_SMR, s3.STPSTSO_SMR, s3.STPSTSF_SMR, s3.STPTIMS_FBTK, s3.LINESTS_FBTK, s3.STPTIMO_FBTK, s3.STPTIMF_FBTK, s3.STPSTSS_FBTK, s3.STPSTSO_FBTK, s3.STPSTSF_FBTK, s3.WT_D0, s3.TT_BDY, s3.PLAN_BDY from T_HIS_BODY s3 order by DATE_STAMP desc)where rownum=1
              MKLDEBUG:CAS: 0.031199932098389
              MKLDEBUG:Vet: 1
              MKLDEBUG:Velikost: 17,456 B
             */



//select all PAINT data
            /* if ($mkldebug==1) { $starttime=microtime_float(); $startmemory=memory_get_usage(); }
              $fquery4 = "select s4.CUREXPRD_PF, s4.ACTPRD_PL, s4.STPTIME_PNT, s4.OPEAVIL_PF, "
              ."s4.OVERTIME_PNT, s4.LINESTS_PNT, s4.INVCUR_BTOP, s4.LINESTS_TOPA, "
              ."s4.STPTIMS_TOPA, s4.STPSTSS_TOPA, s4.STPTIMO_TOPA, s4.STPSTSO_TOPA, s4.STPTIMF_TOPA, "
              ."s4.STPSTSF_TOPA, s4.STPTIMS_TOPB, s4.STPSTSS_TOPB, s4.STPTIMO_TOPB, s4.STPSTSO_TOPB, "
              ."s4.STPTIMF_TOPB, s4.STPSTSF_TOPB, s4.LINESTS_TOPB, s4.STPTIMS_PFIN, "
              ."s4.STPSTSS_PFIN, s4.STPTIMO_PFIN, s4.STPSTSO_PFIN, s4.STPTIMF_PFIN, s4.STPSTSF_PFIN, "
              ."s4.LINESTS_PFIN, s4.STPTIMS_PRIM, s4.STPSTSS_PRIM, s4.STPTIMO_PRIM, "
              ."s4.STPTIMF_PRIM, s4.STPSTSF_PRIM, s4.LINESTS_PRIM, s4.STPSTSO_PRIM, "
              ."s4.STPTIMS_SLCT, s4.STPSTSS_SLCT, s4.STPTIMO_SLCT, s4.STPTIMF_SLCT, "
              ."s4.STPSTSF_SLCT, s4.LINESTS_SLCT, s4.STPSTSO_SLCT, s4.STPTIMS_SEAL, "
              ."s4.STPSTSS_SEAL, s4.STPTIMO_SEAL, s4.STPTIMF_SEAL, s4.STPSTSF_SEAL, "
              //new for T/C INSP
              ."s4.LINESTS_TCINSP, s4.STPTIMS_TCINSP, s4.STPSTSS_TCINSP, s4.STPTIMO_TCINSP, "
              ."s4.STPSTSO_TCINSP, s4.STPTIMF_TCINSP, s4.STPSTSF_TCINSP, s4.STPTIME_TCINSP, s4.OPEAVIL_TCINSP, "
              //new for new PAINT
              ."s4.INVCUR_ESC02, s4.INVCUR_PTED, s4.DRR_TCINSP, "
              //			  ."s4.LINESTS_UBC, s4.STPTIMS_UBC, s4.STPSTSS_UBC, s4.STPTIMO_UBC, s4.STPSTSO_UBC, s4.STPTIMF_UBC, s4.STPSTSF_UBC, "
              ."s4.LINESTS_EDINSP, s4.STPTIMS_EDINSP, s4.STPSTSS_EDINSP, s4.STPTIMO_EDINSP, s4.STPSTSO_EDINSP, s4.STPTIMF_EDINSP, s4.STPSTSF_EDINSP, "
              ."s4.LINESTS_PRINSP, s4.STPTIMS_PRINSP, s4.STPSTSS_PRINSP, s4.STPTIMO_PRINSP, s4.STPSTSO_PRINSP, s4.STPTIMF_PRINSP, s4.STPSTSF_PRINSP, "
              ."s4.ACTPRD_EDINSP, s4.ACTPRD_PRINSP, s4.ACTPRD_TCINSP, "
              ."s4.DIFF_PTPVC, s4.DIFF_PC, s4.DIFF_TC, s4.DIFF_FINWAX, "
              //new cesta tomas
              ."s4.OK_OFF, s4.TC_OFF_LINE, s4.FINAL_OFF_LINE, s4.TT_PNT, s4.PLAN_PNT,"
              //
              //new for WAX
              ."s4.LINESTS_WAX, s4.STPTIMS_WAX, s4.STPSTSS_WAX, s4.STPTIMO_WAX, "
              ."s4.STPSTSO_WAX, s4.STPTIMF_WAX, s4.STPSTSF_WAX, s4.STPTIME_WAX, s4.OPEAVIL_WAX, "
              //new for T/C INSP
              ."s4.LINESTS_SEAL, s4.STPSTSO_SEAL, s4.WT_G3, s4.WT_H0, s4.STAT_PTED_ENTRANCE, s4.STAT_PTED_EXIT, s4.STAT_PFC_ENTRANCE, s4.STAT_PFC_EXIT from T_HIS_PAINT s4".$fWhere;
              $farray4 = array();
              $frows4 = 0;
              oracle_select($conn, $fquery4, $farray4, $frows4);
              if ($mkldebug==1) { $stoptime=microtime_float(); $stopmemory = memory_get_usage(); echo 'MKLDEBUG:Select: '.$fquery4.'<br>'.'MKLDEBUG:CAS: '.($stoptime-$starttime).'<br>'.'MKLDEBUG:Vet: '.$frows4.'<br>'.'MKLDEBUG:Velikost: '.number_format($stopmemory-$startmemory).'&nbsp;B<br><hr>';}
             */
            /* stara verze
              MKLDEBUG:Select: select s4.CUREXPRD_PF, s4.ACTPRD_PL, s4.STPTIME_PNT, s4.OPEAVIL_PF, s4.OVERTIME_PNT, s4.LINESTS_PNT, s4.INVCUR_BTOP, s4.LINESTS_TOPA, s4.STPTIMS_TOPA, s4.STPSTSS_TOPA, s4.STPTIMO_TOPA, s4.STPSTSO_TOPA, s4.STPTIMF_TOPA, s4.STPSTSF_TOPA, s4.STPTIMS_TOPB, s4.STPSTSS_TOPB, s4.STPTIMO_TOPB, s4.STPSTSO_TOPB, s4.STPTIMF_TOPB, s4.STPSTSF_TOPB, s4.LINESTS_TOPB, s4.STPTIMS_PFIN, s4.STPSTSS_PFIN, s4.STPTIMO_PFIN, s4.STPSTSO_PFIN, s4.STPTIMF_PFIN, s4.STPSTSF_PFIN, s4.LINESTS_PFIN, s4.STPTIMS_PRIM, s4.STPSTSS_PRIM, s4.STPTIMO_PRIM, s4.STPTIMF_PRIM, s4.STPSTSF_PRIM, s4.LINESTS_PRIM, s4.STPSTSO_PRIM, s4.STPTIMS_SLCT, s4.STPSTSS_SLCT, s4.STPTIMO_SLCT, s4.STPTIMF_SLCT, s4.STPSTSF_SLCT, s4.LINESTS_SLCT, s4.STPSTSO_SLCT, s4.STPTIMS_SEAL, s4.STPSTSS_SEAL, s4.STPTIMO_SEAL, s4.STPTIMF_SEAL, s4.STPSTSF_SEAL, s4.LINESTS_TCINSP, s4.STPTIMS_TCINSP, s4.STPSTSS_TCINSP, s4.STPTIMO_TCINSP, s4.STPSTSO_TCINSP, s4.STPTIMF_TCINSP, s4.STPSTSF_TCINSP, s4.STPTIME_TCINSP, s4.OPEAVIL_TCINSP, s4.INVCUR_ESC02, s4.INVCUR_PTED, s4.DRR_TCINSP, s4.LINESTS_EDINSP, s4.STPTIMS_EDINSP, s4.STPSTSS_EDINSP, s4.STPTIMO_EDINSP, s4.STPSTSO_EDINSP, s4.STPTIMF_EDINSP, s4.STPSTSF_EDINSP, s4.LINESTS_PRINSP, s4.STPTIMS_PRINSP, s4.STPSTSS_PRINSP, s4.STPTIMO_PRINSP, s4.STPSTSO_PRINSP, s4.STPTIMF_PRINSP, s4.STPSTSF_PRINSP, s4.ACTPRD_EDINSP, s4.ACTPRD_PRINSP, s4.ACTPRD_TCINSP, s4.DIFF_PTPVC, s4.DIFF_PC, s4.DIFF_TC, s4.DIFF_FINWAX, s4.OK_OFF, s4.TC_OFF_LINE, s4.FINAL_OFF_LINE, s4.TT_PNT, s4.PLAN_PNT,s4.LINESTS_WAX, s4.STPTIMS_WAX, s4.STPSTSS_WAX, s4.STPTIMO_WAX, s4.STPSTSO_WAX, s4.STPTIMF_WAX, s4.STPSTSF_WAX, s4.STPTIME_WAX, s4.OPEAVIL_WAX, s4.LINESTS_SEAL, s4.STPSTSO_SEAL, s4.WT_G3, s4.WT_H0, s4.STAT_PTED_ENTRANCE, s4.STAT_PTED_EXIT, s4.STAT_PFC_ENTRANCE, s4.STAT_PFC_EXIT from T_HIS_PAINT s4 order by DATE_STAMP desc
              MKLDEBUG:CAS: 1.1076068878174
              MKLDEBUG:Vet: 9044
              MKLDEBUG:Velikost: 95,263,880 B
             */

//            if ($mkldebug == 1) {
//                $starttime = microtime_float();
//                $startmemory = memory_get_usage();
//            }
            $fquery4 = "select * from ("
                    . "select s4.CUREXPRD_PF, s4.ACTPRD_PL, s4.STPTIME_PNT, s4.OPEAVIL_PF, "
                    ."s4.OVERTIME_PNT, s4.LINESTS_PNT, s4.INVCUR_BTOP, s4.LINESTS_TOPA, "
                    ."s4.STPTIMS_TOPA, s4.STPSTSS_TOPA, s4.STPTIMO_TOPA, s4.STPSTSO_TOPA, s4.STPTIMF_TOPA, "
	."s4.STPSTSF_TOPA, s4.STPTIMS_TOPB, s4.STPSTSS_TOPB, s4.STPTIMO_TOPB, s4.STPSTSO_TOPB, "
	."s4.STPTIMF_TOPB, s4.STPSTSF_TOPB, s4.LINESTS_TOPB, s4.STPTIMS_PFIN, "
	."s4.STPSTSS_PFIN, s4.STPTIMO_PFIN, s4.STPSTSO_PFIN, s4.STPTIMF_PFIN, s4.STPSTSF_PFIN, "
	."s4.LINESTS_PFIN, s4.STPTIMS_PRIM, s4.STPSTSS_PRIM, s4.STPTIMO_PRIM, "
	."s4.STPTIMF_PRIM, s4.STPSTSF_PRIM, s4.LINESTS_PRIM, s4.STPSTSO_PRIM, "
	."s4.STPTIMS_SLCT, s4.STPSTSS_SLCT, s4.STPTIMO_SLCT, s4.STPTIMF_SLCT, "
	."s4.STPSTSF_SLCT, s4.LINESTS_SLCT, s4.STPSTSO_SLCT, s4.STPTIMS_SEAL, "
	."s4.STPSTSS_SEAL, s4.STPTIMO_SEAL, s4.STPTIMF_SEAL, s4.STPSTSF_SEAL, "
	."s4.STPTIMS_SEALR, s4.STPSTSS_SEALR, s4.STPTIMO_SEALR, s4.STPTIMF_SEALR, s4.STPSTSF_SEALR, "
	//new for T/C INSP
	."s4.LINESTS_TCINSP, s4.STPTIMS_TCINSP, s4.STPSTSS_TCINSP, s4.STPTIMO_TCINSP, "
	."s4.STPSTSO_TCINSP, s4.STPTIMF_TCINSP, s4.STPSTSF_TCINSP, s4.STPTIME_TCINSP, s4.OPEAVIL_TCINSP, "
	."s4.INVCUR_ESC02, s4.INVCUR_PTED, s4.DRR_TCINSP, s4.DRR_FINSP, "
	."s4.LINESTS_UBC, s4.STPTIMS_UBC, s4.STPSTSS_UBC, s4.STPTIMO_UBC, s4.STPSTSO_UBC, s4.STPTIMF_UBC, s4.STPSTSF_UBC, "
	."s4.LINESTS_EDINSP, s4.STPTIMS_EDINSP, s4.STPSTSS_EDINSP, s4.STPTIMO_EDINSP, s4.STPSTSO_EDINSP, s4.STPTIMF_EDINSP, s4.STPSTSF_EDINSP, "
	."s4.LINESTS_PRINSP, s4.STPTIMS_PRINSP, s4.STPSTSS_PRINSP, s4.STPTIMO_PRINSP, s4.STPSTSO_PRINSP, s4.STPTIMF_PRINSP, s4.STPSTSF_PRINSP, "
	."s4.ACTPRD_EDINSP, s4.ACTPRD_PRINSP, s4.ACTPRD_TCINSP, "
	."s4.DIFF_PTPVC, s4.DIFF_PC, s4.DIFF_TC, s4.DIFF_FINWAX, "
  	."s4.OK_OFF, s4.TC_OFF_LINE, s4.FINAL_OFF_LINE, s4.TT_PNT, s4.PLAN_PNT,"
	//
	//new for WAX
	."s4.LINESTS_WAX, s4.STPTIMS_WAX, s4.STPSTSS_WAX, s4.STPTIMO_WAX, "
	."s4.STPSTSO_WAX, s4.STPTIMF_WAX, s4.STPSTSF_WAX, s4.STPTIME_WAX, s4.OPEAVIL_WAX, "
	//new for T/C INSP
	."s4.LINESTS_SEAL, s4.STPSTSO_SEAL, s4.LINESTS_SEALR, s4.STPSTSO_SEALR, s4.WT_G3, s4.WT_H0, s4.STAT_PTED_ENTRANCE, s4.STAT_PTED_EXIT, s4.STAT_PFC_ENTRANCE, s4.STAT_PFC_EXIT,"
        ."s4.SPARE_2 as WT, s4.SPARE_7 as CANVAS, "
        ."s4.SPARE_8 as MASK, s4.SPARE_9 as TC_NG, "
        ."s4.SPARE_10 as PRM_TOTAL, s4.SPARE_11 as PRM_BB, s4.SPARE_12 as TC_TOTAL, s4.SPARE_13 as CSS, s4.SPARE_14 as TC_TO_FI, s4.SPARE_15 as SKID_INV, s4.SPARE_16 as TC_TO_ILM from T_".$query_substr2."_PAINT s4" . $fWhere
                    . ")"
                    . "where rownum=1"
            ;
            $farray4 = array();
            $frows4 = 0;
            oracle_select($conn, $fquery4, $farray4, $frows4);
//            if ($mkldebug == 1) {
//                $stoptime = microtime_float();
//                $stopmemory = memory_get_usage();
//                echo 'MKLDEBUG:Select: ' . $fquery4 . '<br>' . 'MKLDEBUG:CAS: ' . ($stoptime - $starttime) . '<br>' . 'MKLDEBUG:Vet: ' . $frows4 . '<br>' . 'MKLDEBUG:Velikost: ' . number_format($stopmemory - $startmemory) . '&nbsp;B<br><hr>';
//            }
            /* nova verze
              MKLDEBUG:Select: select * from (select s4.CUREXPRD_PF, s4.ACTPRD_PL, s4.STPTIME_PNT, s4.OPEAVIL_PF, s4.OVERTIME_PNT, s4.LINESTS_PNT, s4.INVCUR_BTOP, s4.LINESTS_TOPA, s4.STPTIMS_TOPA, s4.STPSTSS_TOPA, s4.STPTIMO_TOPA, s4.STPSTSO_TOPA, s4.STPTIMF_TOPA, s4.STPSTSF_TOPA, s4.STPTIMS_TOPB, s4.STPSTSS_TOPB, s4.STPTIMO_TOPB, s4.STPSTSO_TOPB, s4.STPTIMF_TOPB, s4.STPSTSF_TOPB, s4.LINESTS_TOPB, s4.STPTIMS_PFIN, s4.STPSTSS_PFIN, s4.STPTIMO_PFIN, s4.STPSTSO_PFIN, s4.STPTIMF_PFIN, s4.STPSTSF_PFIN, s4.LINESTS_PFIN, s4.STPTIMS_PRIM, s4.STPSTSS_PRIM, s4.STPTIMO_PRIM, s4.STPTIMF_PRIM, s4.STPSTSF_PRIM, s4.LINESTS_PRIM, s4.STPSTSO_PRIM, s4.STPTIMS_SLCT, s4.STPSTSS_SLCT, s4.STPTIMO_SLCT, s4.STPTIMF_SLCT, s4.STPSTSF_SLCT, s4.LINESTS_SLCT, s4.STPSTSO_SLCT, s4.STPTIMS_SEAL, s4.STPSTSS_SEAL, s4.STPTIMO_SEAL, s4.STPTIMF_SEAL, s4.STPSTSF_SEAL, s4.LINESTS_TCINSP, s4.STPTIMS_TCINSP, s4.STPSTSS_TCINSP, s4.STPTIMO_TCINSP, s4.STPSTSO_TCINSP, s4.STPTIMF_TCINSP, s4.STPSTSF_TCINSP, s4.STPTIME_TCINSP, s4.OPEAVIL_TCINSP, s4.INVCUR_ESC02, s4.INVCUR_PTED, s4.DRR_TCINSP, s4.LINESTS_EDINSP, s4.STPTIMS_EDINSP, s4.STPSTSS_EDINSP, s4.STPTIMO_EDINSP, s4.STPSTSO_EDINSP, s4.STPTIMF_EDINSP, s4.STPSTSF_EDINSP, s4.LINESTS_PRINSP, s4.STPTIMS_PRINSP, s4.STPSTSS_PRINSP, s4.STPTIMO_PRINSP, s4.STPSTSO_PRINSP, s4.STPTIMF_PRINSP, s4.STPSTSF_PRINSP, s4.ACTPRD_EDINSP, s4.ACTPRD_PRINSP, s4.ACTPRD_TCINSP, s4.DIFF_PTPVC, s4.DIFF_PC, s4.DIFF_TC, s4.DIFF_FINWAX, s4.OK_OFF, s4.TC_OFF_LINE, s4.FINAL_OFF_LINE, s4.TT_PNT, s4.PLAN_PNT,s4.LINESTS_WAX, s4.STPTIMS_WAX, s4.STPSTSS_WAX, s4.STPTIMO_WAX, s4.STPSTSO_WAX, s4.STPTIMF_WAX, s4.STPSTSF_WAX, s4.STPTIME_WAX, s4.OPEAVIL_WAX, s4.LINESTS_SEAL, s4.STPSTSO_SEAL, s4.WT_G3, s4.WT_H0, s4.STAT_PTED_ENTRANCE, s4.STAT_PTED_EXIT, s4.STAT_PFC_ENTRANCE, s4.STAT_PFC_EXIT from T_HIS_PAINT s4 order by DATE_STAMP desc)where rownum=1
              MKLDEBUG:CAS: 0.078001022338867
              MKLDEBUG:Vet: 1
              MKLDEBUG:Velikost: 30,984 B
             */



//select all Assembly data
            $fquery5 = "select * from ("
                    . "select s5.CUREXPRD_LO, s5.CUREXPRD_BO, s5.ACTPRD_LO, s5.STPTIME_ASY, s5.OPEAVIL_F2, "
                    . "s5.OVERTIME_ASY, s5.LINESTS_ASY, s5.STPTIMS_CHAS, s5.STPSTSS_CHAS, "
                    . "s5.STPTIMO_CHAS, s5.STPSTSO_CHAS, s5.STPTIMF_CHAS, s5.STPSTSF_CHAS, s5.LINESTS_CHAS "
// uz to jednou vyse je ."s5.STPSTSS_CHAS"
// uz to jednou vyse je .", s5.STPTIMS_CHAS"
                    . ", s5.STPTIMO_TRIM"
                    . ", s5.STPSTSO_TRIM"
                    . ", s5.STPTIMS_TRIM"
                    . ",s5.STPSTSS_TRIM, s5.STPTIMF_TRIM, s5.STPSTSF_TRIM, s5.LINESTS_TRIM, "
                    . "s5.STPTIMO_FIN2, s5.STPSTSO_FIN2, s5.STPTIMS_FIN2, s5.STPSTSS_FIN2, "
                    . "s5.STPTIMF_FIN2, s5.STPSTSF_FIN2, s5.LINESTS_FIN2, s5.STPTIMO_FIN1, "
                    . "s5.STPSTSO_FIN1, s5.STPTIMS_FIN1, s5.STPSTSS_FIN1, s5.STPTIMF_FIN1, "
                    . "s5.STPSTSF_FIN1, s5.LINESTS_FIN1, s5.INVCUR_CH_FI, s5.STPTIMO_AINS, "
                    . "s5.STPSTSO_AINS, s5.STPTIMS_AINS, s5.STPSTSS_AINS, s5.STPTIMF_AINS, "
                    . "s5.ASSY_LOAD, "
                    . "s5.STPSTSF_AINS, s5.LINESTS_AINS, s5.WT_N0, s5.WT_R0, s5.TT_ASY, s5.PLAN_ASY, s5.INVCUR_N0INSP from T_".$query_substr2."_ASSEMBLY s5" . $fWhere
                    . ")"
                    . " where rownum=1"
            ;
            $farray5 = array();
            $frows5 = 0;
            oracle_select($conn, $fquery5, $farray5, $frows5);

//select all QC data
            $fquery6 = "select * from ("
                    . "select s6.CUREXPRD_BO, s6.OVERTIME_QC, s6.TT_QC, s6.PLAN_QC from T_".$query_substr2."_ASSEMBLY s6" . $fWhere
                    . ")"
                    . "where rownum=1"
            ;
            $farray6 = array();
            $frows6 = 0;
            oracle_select($conn, $fquery6, $farray6, $frows6);

//select all master data
            $fquery7 = "select * from ("
                    . "select s7.PLNSOTSH_ST1, s7.PLNSOTSH_ST2, s7.PLNPRDSH_BL, s7.PLNPRDSH_PL, "
                    . "s7.PLNPRDSH_LO, s7.PLNPRDSH_BO from T_".$query_substr2."_MASTER s7" . $fWhere
                    . ")"
                    . "where rownum=1"
            ;
            $farray7 = array();
            $frows7 = 0;
            oracle_select($conn, $fquery7, $farray7, $frows7);

//select all master data
            $fquery8 = "select * from ("
                    . "select s8.INVCUR_BODY, s8.INVCUR_PAINT, s8.INVCUR_PTOA, s8.INVCUR_ASSY, "
                    . "s8.INVCUR_QCSAL, s8.INVCUR_BDRP, s8.INVCUR_PRPAL, s8.INVCUR_QRPAL, "
                    . "s8.INVCUR_TOPCT, s8.INVCUR_TCRP, s8.INVCUR_PRIMR, s8.INVCUR_EDSLR, "
                    . "s8.INVCUR_PRMRP, s8.INVCUR_EDRP, s8.INVCUR_PFIN, s8.INVCUR_PFRP, "
                    . "s8.INVCUR_SELEC, s8.ACTPRD_LO, s8.ACTPRD_VC, s8.ACTPRD_BL, s8.ACTPRD_PF, s8.ACTPRD_G3, "
                    . "s8.INVCUR_MAJRP, s8.INVCUR_INVES, s8.INVCUR_ASYRP, s8.INVCUR_BDYRP, s8.INVCUR_BDATA, "
                    . "s8.INVCUR_PNTRP, s8.INVCUR_CONF, s8.INVCUR_WAIT, s8.INVCUR_AUDIT, s8.INVCUR_BTOP, s8.INVCUR_BEDREP, "
                    . "s8.INVCUR_AUDRP, s8.INVCUR_REPVEH,s8.INVCUR_X1,s8.INVCUR_WELD_OK, s8.INVCUR_WELD_REPAIRED, s8.INVCUR_Z0, s8.INVCUR_BSEQ, s8.INVCUR_Z8, s8.INVCUR_Z9, s8.INVCUR_Z1, s8.INVCUR_Z2,"
                    . "s8.INVCUR_F8, s8.INVCUR_F9, s8.INVCUR_PRMRP as V3, s8.G1B_ratio, s8.INVCUR_INVES_G1B, s8.INVCUR_MAJRP_G1B, s8.INVCUR_ASYRP_G1B, s8.INVCUR_BDYRP_G1B, s8.INVCUR_PNTRP_G1B,"
                    . "s8.INVCUR_CONF_G1B, s8.INVCUR_WAIT_G1B, s8.INVCUR_Z0_G1B, s8.INVCUR_TCRP_G1B, s8.INVCUR_PFRP_G1B, s8.INVCUR_REPVEH_G1B, s8.G1B_PNTRP_RATIO, s8.G1B_TA_RATIO, s8.G1B_RATIO_PFC_FNL,"
                    . "s8.INVCUR_AUDIT_G1B, s8.INVCUR_AUDRP_G1B, s8.INVCUR_X1_G1B, s8.INVCUR_Z1_G1B, s8.INVCUR_Z2_G1B, s8.INVCUR_ACCESS_PLAN, s8.INVCUR_ACCESS_REAL  from T_".$query_substr2."_GALC s8" . $fWhere
                    . ")"
                    . "where rownum=1"
            ;
            $farray8 = array();
            $frows8 = 0;
            oracle_select($conn, $fquery8, $farray8, $frows8);

//select all master data
            $fquery9 = "select * from ("
                    . "select s9.INVMIN_BODY, s9.INVMAX_BODY, s9.INVSTD_BODY, "
                    . "s9.COLMIN_BODY, s9.COLMAX_BODY,"
                    . "s9.INVMIN_BTOP, s9.INVMAX_BTOP, s9.INVSTD_BTOP, "
                    . "s9.COLMIN_BTOP, s9.COLMAX_BTOP,"
                    . "s9.INVMIN_BTOP2, s9.INVMAX_BTOP2, s9.INVSTD_BTOP2, "
                    . "s9.COLMIN_BTOP2, s9.COLMAX_BTOP2,"
                    . "s9.INVMIN_PAINT, s9.INVMAX_PAINT, s9.INVSTD_PAINT, "
                    . "s9.COLMIN_PAINT, s9.COLMAX_PAINT,"
                    . "s9.INVMIN_EDSLR, s9.INVMAX_EDSLR, s9.INVSTD_EDSLR, "
                    . "s9.COLMIN_EDSLR, s9.COLMAX_EDSLR,"
                    . "s9.INVMIN_PRIMR, s9.INVMAX_PRIMR, s9.INVSTD_PRIMR, "
                    . "s9.COLMIN_PRIMR, s9.COLMAX_PRIMR,"
                    . "s9.INVMIN_TOPCT, s9.INVMAX_TOPCT, s9.INVSTD_TOPCT,"
                    . "s9.COLMIN_TOPCT, s9.COLMAX_TOPCT,"
                    . "s9.INVMIN_PFIN, s9.INVMAX_PFIN, s9.INVSTD_PFIN, "
                    . "s9.COLMIN_PFIN, s9.COLMAX_PFIN,"
                    . "s9.INVMIN_SELEC, s9.INVMAX_SELEC, s9.INVSTD_SELEC, "
                    . "s9.COLMIN_SELEC, s9.COLMAX_SELEC,"
                    . "s9.INVMIN_PTOA, s9.INVMAX_PTOA, s9.INVSTD_PTOA, "
                    . "s9.COLMIN_PTOA, s9.COLMAX_PTOA,"
                    . "s9.INVMIN_ASSY, s9.INVMAX_ASSY, s9.INVSTD_ASSY, "
                    . "s9.COLMIN_ASSY, s9.COLMAX_ASSY,"
                    . "s9.INVMIN_CH_FI, s9.INVMAX_CH_FI, s9.INVSTD_CH_FI, "
                    . "s9.COLMIN_CH_FI, s9.COLMAX_CH_FI,"
                    . "s9.INVMIN_QCSAL, s9.INVMAX_QCSAL, s9.INVSTD_QCSAL, "
                    . "s9.COLMIN_QCSAL, s9.COLMAX_QCSAL,"
                    . "s9.INVMIN_N0INSP, s9.INVMAX_N0INSP, s9.INVSTD_N0INSP, "
                    . "s9.COLMIN_N0INSP, s9.COLMAX_N0INSP,"
                    . "s9.INVMAX_BDRP, s9.INVMAX_PRPAL, s9.INVMAX_QRPAL, "
                    ."s9.INVMAX_TCRP, s9.INVMAX_PRMRP, s9.INVMAX_EDRP, s9.INVMAX_PFRP, s9.INVMAX_MAJRP, "
                    ."s9.INVMAX_INVES, s9.INVMAX_ASYRP, s9.INVMAX_BDYRP, s9.INVMAX_PNTRP, "
                    ."s9.INVMAX_CONF, s9.INVMAX_WAIT, s9.INVMAX_AUDIT_X0, s9.INVMAX_AUDIT_X9,"
                    ."s9.COLMAX_BDRP, s9.COLMAX_PRPAL, s9.COLMAX_QRPAL, "
                    ."s9.COLMAX_TCRP, s9.COLMAX_PRMRP, s9.COLMAX_EDRP, s9.COLMAX_PFRP, s9.COLMAX_MAJRP, "
                    ."s9.COLMAX_INVES, s9.COLMAX_ASYRP, s9.COLMAX_BDYRP, s9.COLMAX_PNTRP, "
                    ."s9.COLMAX_CONF, s9.COLMAX_WAIT, s9.COLMAX_AUDIT_X0, s9.COLMAX_AUDIT_X9, s9.INVMAX_QRPAL2, s9.COLMAX_QRPAL2, s9.TA_BUFF_ACTION, "
                    ."s9.COLMAX_BB, s9.COLMIN_BB, s9.INVMAX_BB, s9.INVMIN_BB, s9.INVSTD_BB, "
                    ."s9.COLMAX_CSS, s9.COLMIN_CSS, s9.INVMAX_CSS, s9.INVMIN_CSS, s9.INVSTD_CSS, "
                    ."s9.COLMAX_WEMPTY, s9.COLMIN_WEMPTY, s9.INVMAX_WEMPTY, s9.INVMIN_WEMPTY, s9.INVSTD_WEMPTY, s9.BSEQ_ACTION, "
                    ."s9.INVMAX_Z8, s9.COLMAX_Z8, s9.INVMAX_Z9, s9.COLMAX_Z9, s9.INVMAX_Z0, s9.COLMAX_Z0, s9.BUYOFF_STATUS, s9.BUYOFF_STOP_FROM, s9.G1B_RATIO_PLAN, s9.G1B_RATIO_DIFF_CLR "
                    . "from T_".$query_substr2."_MASTER s9" . $fWhere
                    . ")"
                    . "where rownum=1"
            ;

            $farray9 = array();
            $frows9 = 0;
            oracle_select($conn, $fquery9, $farray9, $frows9);

//select all master data
            $fquery10 = "select * from ("
                    . "select s10.STPTIMO_BLKG, s10.STPTIME_BLKG, s10.STPSTSO_BLKG, s10.LINESTS_BLKG from T_".$query_substr2."_STAMP_BLKG s10" . $fWhere
                    . ")"
                    . "where rownum=1"
            ;
            $farray10 = array();
            $frows10 = 0;
            oracle_select($conn, $fquery10, $farray10, $frows10);

//select UTIL
            $fquery11 = "select * from ("
                    . "select s11.WORKTIME, s11.WORKTIME_ASY, s11.WORKTIME_QC, s11.WORKTIME_BDY, s11.WORKTIME_PNT, s11.MAX_LO_DATE, s11.VLT_MAX_DATE"
                    . " from T_".$query_substr2."_UTIL s11" . $fWhere
                    . ")"
                    . "where rownum=1"
            ;
            $farray11 = array();
            $frows11 = 0;
            oracle_select($conn, $fquery11, $farray11, $frows11);

            /* //select newplan
              $fquery12 = "select s12.PLAN_BDY, PLAN_PNT, PLAN_ASY, PLAN_QC from T_LM_OVERTIME_TEST s12";
              $farray12 = array();
              $frows12 = 0;
              oracle_select($conn, $fquery12, $farray12, $frows12); */

            //select data pro SCP
            $fquery14 = "select s14.SCP_TOTAL,s14.SCP_STATUS from SCP s14";	
            $farray14 = array();
            $frows14 = 0;
            oracle_select($conn, $fquery14, $farray14, $frows14);

            oracle_logoff($conn);
// ******* ORACLE LOAD DATA END *******
  
//parametry
  
  $BODY_TT = $farray3["TT_BDY"][0];
  $PAINT_TT = $farray4["TT_PNT"][0];
  $ASSY_TT = $farray5["TT_ASY"][0];
  $QC_TT = $farray6["TT_QC"][0];
      
  //$fDef = 581;
  $WT_D0 = $farray3["WT_D0"][0];
  $WT_G3 = $farray4["WT_G3"][0];
  $WT_H0 = $farray4["WT_H0"][0];
  $WT_N0 = $farray5["WT_N0"][0];
  
  $fPlanB = abs($farray3["PLAN_BDY"][0]);
  $fPlanP = abs($farray4["PLAN_PNT"][0]);
  $fPlanA = abs($farray5["PLAN_ASY"][0]);
  $fPlanQ = abs($farray6["PLAN_QC"][0]);
  
    $fWorkTime = abs($farray11["WORKTIME"][0]);
//    $fWorkTimeB = $fWorkTimeP = $fWorkTimeA = $fWorkTime;
		$fWorkTimeB = abs($farray11["WORKTIME_BDY"][0]);
		$fWorkTimeP = abs($farray11["WORKTIME_PNT"][0]);
		$fWorkTimeA = abs($farray11["WORKTIME_ASY"][0]);
    $fWorkTimeQ = abs($farray11["WORKTIME_QC"][0]);

/*  if ($fWorkTime >= 581) {
  	$fShift = (time() > strtotime("17:11")) ? strtotime("17:11") : strtotime("05:11");
  	$fOver = round((time() - $fShift)/60);
  	$fWorkTimeB += ($farray3["OVERTIME_BDY"][0] >= $fOver) ? $fOver : $farray3["OVERTIME_BDY"][0];
  	$fWorkTimeP += ($farray4["OVERTIME_PNT"][0] >= $fOver) ? $fOver : $farray4["OVERTIME_PNT"][0];
  	$fWorkTimeA += ($farray5["OVERTIME_ASY"][0] >= $fOver) ? $fOver : $farray5["OVERTIME_ASY"][0];
  }
*/
?>	

		
<?php
$CT1_L = 5 * $x;
$CT1_T = 5 * $x;
$CT1_H = 550 * $x;
$CT1_W = 290 * $x;

$CT2_L = 310 * $x;
$CT2_T = 5 * $x;
$CT2_H = 550 * $x;
$CT2_W = 620 * $x;

$CT3_L = 945 * $x;
$CT3_T = 5 * $x;
$CT3_H = 550 * $x;
$CT3_W = 650 * $x;

$CT4_L = 5 * $x;
$CT4_T = 568 * $x;
$CT4_H = 572 * $x;
$CT4_W = 1080 * $x;

$CT5_L = 1100 * $x;
$CT5_T = 568 * $x;
$CT5_H = 572 * $x;
$CT5_W = 495 * $x;

?>
<!-- DATE TIME -->
<?php

//$HOD_L = 920 * $x;
//$HOD_T = 1060 * $x;
//$HOD_H = 50 * $x;
//$HOD_W = 170 * $x;

$HOD_L = 1300 * $x;
$HOD_T = 165 * $x;
$HOD_H = 75 * $x;
$HOD_W = 250 * $x;

echo "<div style=\"background:gray; position:absolute; left: {$CT1_L}px; top: {$CT1_T}px; height: {$CT1_H}px; width: {$CT1_W}px;\"></div>";
echo "<div style=\"background:gray; position:absolute; left: {$CT2_L}px; top: {$CT2_T}px; height: {$CT2_H}px; width: {$CT2_W}px;\"></div>";
echo "<div style=\"background:gray; position:absolute; left: {$CT3_L}px; top: {$CT3_T}px; height: {$CT3_H}px; width: {$CT3_W}px;\"></div>";

echo "<div style=\"background:gray; position:absolute; left: {$CT4_L}px; top: {$CT4_T}px; height: {$CT4_H}px; width: {$CT4_W}px;\"></div>";
echo "<div style=\"background:gray; position:absolute; left: {$CT5_L}px; top: {$CT5_T}px; height: {$CT5_H}px; width: {$CT5_W}px;\"></div>";

//echo "<div id=note1 style=\"background:#AAAAAA; font-weight:bold; border:1px solid black; text-align:center; position:absolute; left: $HOD_L px; top: $HOD_T px; height: $HOD_H px; width: $HOD_W px;\">".Date("j F Y")."</div>";
echo "<div id=note1 style=\"background:#AAAAAA; font-weight:bold; border:1px solid black; text-align:center; position:absolute; left: {$HOD_L}px; top: {$HOD_T}px; height: {$HOD_H}px; width: {$HOD_W}px;\"></div>";
$HOD_T = $HOD_T + 40;
$HOD_H = 35 * $x;
echo "<div id=note2 style=\"background:#AAAAAA; font-weight:bold; border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black; text-align:center; position:absolute; left: {$HOD_L}px; top: {$HOD_T}px; height: {$HOD_H}px; width: {$HOD_W}px;\">".Date("g:i a")."</div>";
?>
	
<!-- WINDOW 1 -->
<!-- STAMPING 1A -->
<?php
//$ST1A_L = 25 * $x;
//$ST1A_T = 15 * $x;
//$ST1A_H = 120 * $x;
//$ST1A_W = 250 * $x;
$ST1A_L = 25 * $x;
$ST1A_T = 400 * $x;
$ST1A_H = 120 * $x;
$ST1A_W = 250 * $x;
echo "<TABLE style=\"position:absolute; left: {$ST1A_L}px; top: {$ST1A_T}px; height: {$ST1A_H}px; width: {$ST1A_W}px; border:solid Black 1px;\" cellpadding=\"0\" cellspacing=\"0\">"
?>
  <TR height=10>
    <TD class=fTable_1_1 colspan=3>STAMPING 1A</TD>
  </TR>
  <TR height=10>
    <TD class=fTable_1_2 width=33% style="border-left:none;">Plan Shift</TD>
    <TD class=fTable_1_2 width=33%>Plan</TD>
    <TD class=fTable_1_2>Actual</TD>
  </TR>
  <TR>
    <TD class=fTable_1_3 style="border-left:none;" bgcolor="#CCFFCC"><?php $test = @$farray7["PLNSOTSH_ST1"][0]; if ($test == "") { echo 0;} else {echo $farray7["PLNSOTSH_ST1"][0]; } ?></TD>
    <TD class=fTable_1_3 bgcolor="#CCFFCC"><?php $test = @$farray["CUREXSOT_ST1"][0]; if ($test == "") { echo 0;} else {echo $farray["CUREXSOT_ST1"][0]; } ?></TD>
<?php
    $test = @$farray["ACTSOT_ST1"][0];
		$test2 = @$farray["CUREXSOT_ST1"][0];
		if (($test == "") && ($test2 == "")) {
      echo "<TD class=fTable_1_3 bgcolor=red>0</TD>";
    }	else {
		  if ($farray["ACTSOT_ST1"][0] < $farray["CUREXSOT_ST1"][0]) {
			  echo "<TD class=fTable_1_3 bgcolor='#CCFFCC'>" . $farray["ACTSOT_ST1"][0] . "</TD>";
//  			echo "<TD class=fTable_1_3 bgcolor=red>" . $farray["ACTSOT_ST1"][0] . "</TD>";
			} else {
			  echo "<TD class=fTable_1_3 bgcolor='#CCFFCC'>" . $farray["ACTSOT_ST1"][0] . "</TD>";
			}
		}
?>
  </TR>
  <TR height=10>
    <TD class=fTable_1_2 style="border-left:none;">GSPH</TD>
    <TD class=fTable_1_2>L/A (%)</TD>
    <TD class=fTable_1_2>OT(min)</TD>
  </TR>
  <TR>
      <TD class=fTable_1_3 style="border-left:none;" bgcolor="#CCFFCC"><?php $test = @$farray["ACTGSPH_ST1"][0]; if ($test == "") { echo 0;} else {echo $farray["ACTGSPH_ST1"][0]; } ?></TD>
    <TD class=fTable_1_3 bgcolor="#CCFFCC"><?php $test = @$farray["OPEAVIL_ST1"][0]; if ($test == "") { echo 0;} else {echo number_format($farray["OPEAVIL_ST1"][0],1); } ?></TD>
    <TD class=fTable_1_3 bgcolor="#CCFFCC"><?php $test = @$farray["OVERTIME_ST1"][0]; if ($test == "") { echo 0;} else {echo $farray["OVERTIME_ST1"][0]; } ?></TD>
  </TR>
</TABLE>
<!-- BLANKING -->
<?php
$STBL_L = 25 * $x;
$STBL_T = 220 * $x;
$STBL_H = 40 * $x;
$STBL_W = 110 * $x;
echo "<TABLE onLoad=\"blink();\" style=\"position:absolute; left: {$STBL_L}px; top: {$STBL_T}px; height: {$STBL_H}px; width: {$STBL_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
<?php  
    $test = @$farray10["LINESTS_BLKG"][0]; 
  	switch($test) {
	  case "RUNNING": 
	   	  echo "<TD id=blanking class=fTable_2_1 rowspan=2 bgcolor=00C131>BL</TD>";
		  $_SESSION["BLANKING"] = MAX_TIME;
	      break;
  	  case "PLNSTOP": 
	  	  echo "<TD id=blanking class=fTable_2_1 rowspan=2 bgcolor=white>BL</TD>";
		  $_SESSION["BLANKING"] = MAX_TIME;
	      break;
  	  default: 
	  	  echo "<TD id=blanking class=fTable_2_1 rowspan=2 bgcolor=red>BL</TD>";
		  if ($_SESSION["BLANKING"] == MAX_TIME) $_SESSION["BLANKING"] = time();
	}

    $test = @$farray10["STPSTSO_BLKG"][0]; 
  	switch($test) {
      case "RUNNING": 
        echo "<TD class=fTable_2_2 bgcolor=white>Self</TD>";
        echo "</TR>";
        echo "<TR>";
				$test2 = @$farray10["STPTIME_BLKG"][0];
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=white>" . $farray10["STPTIME_BLKG"][0] . "</TD>";
				}
        echo "</TR>";
	    break;
	  case "PLNSTOP":
        echo "<TD class=fTable_2_2 bgcolor=white>Self</TD>";
        echo "</TR>";
        echo "<TR>";
				$test2 = @$farray10["STPTIME_BLKG"][0];
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=white>" . $farray10["STPTIME_BLKG"][0] . "</TD>";
				}
        echo "</TR>";
	    break;
  	  default: 
        echo "<TD class=fTable_2_2 bgcolor=yellow>Self</TD>";
        echo "</TR>";
        echo "<TR>";
				$test2 = @$farray10["STPTIME_BLKG"][0];
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=yellow>" . $farray10["STPTIME_BLKG"][0] . "</TD>";
				}
        echo "</TR>";
	    break;				
    }
?>
</TABLE>
<!-- STAMPING 1A -->
<?php
//$ST1_L = 165 * $x;
//$ST1_T = 220 * $x;
//$ST1_H = 40 * $x;
//$ST1_W = 110 * $x;
$ST1_L = 165 * $x;
$ST1_T = 320 * $x;
$ST1_H = 40 * $x;
$ST1_W = 110 * $x;
echo "<TABLE style=\"position:absolute; left:{$ST1_L}px; top:{$ST1_T}px; height:{$ST1_H}px; width:{$ST1_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
<?php  
    $test = @$farray["LINESTS_ST1"][0];

    switch($test) {
    		case "RUNNING":
	   	  echo "<TD id=ST1A class=fTable_2_1 rowspan=2 bgcolor=00C131>1A</TD>";
	   	  $_SESSION["ST1A"] = MAX_TIME;
	      break;
  		case "PLNSTOP": 
	  	  echo "<TD id=ST1A class=fTable_2_1 rowspan=2 bgcolor=white>1A</TD>";
	  	  $_SESSION["ST1A"] = MAX_TIME;
		    break;
  		default: 
	  	  echo "<TD id=ST1A class=fTable_2_1 rowspan=2 bgcolor=red>1A</TD>";
		  if ($_SESSION["ST1A"] == MAX_TIME) $_SESSION["ST1A"] = time();
		}
//$_SESSION["ST1A"] = time();

    $test = @$farray["STPSTSO_ST1"][0]; 
  	switch($test) {
	  case "RUNNING": 
        echo "<TD class=fTable_2_2 bgcolor=white>Self</TD>";
        echo "</TR>";
        echo "<TR>";
				$test2 = @$farray["STPTIME_ST1"][0];
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=white>" . $farray["STPTIME_ST1"][0] . "</TD>";
				}
        echo "</TR>";
	 	break;
	  case "PLNSTOP": 
        echo "<TD class=fTable_2_2 bgcolor=white>Self</TD>";
        echo "</TR>";
        echo "<TR>";
				$test2 = @$farray["STPTIME_ST1"][0];
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=white>" . $farray["STPTIME_ST1"][0] . "</TD>";
				}
        echo "</TR>";
		break;	  
  	  default: 
        echo "<TD class=fTable_2_2 bgcolor=yellow>Self</TD>";
        echo "</TR>";
        echo "<TR>";
				$test2 = @$farray["STPTIME_ST1"][0];
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=yellow>" . $farray["STPTIME_ST1"][0] . "</TD>";
				}
        echo "</TR>";
	    break;				
	}//switch
?>			
</TABLE>
<!-- STAMPING 2A -->
<?php
//$ST2_L = 165 * $x;
//$ST2_T = 320 * $x;
//$ST2_H = 40 * $x;
//$ST2_W = 110 * $x;
$ST2_L = 165 * $x;
$ST2_T = 220 * $x;
$ST2_H = 40 * $x;
$ST2_W = 110 * $x;
echo "<TABLE style=\"position:absolute; left: {$ST2_L}px; top: {$ST2_T}px; height: {$ST2_H}px; width: {$ST2_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
<?php  
    $test = @$farray2["LINESTS_ST2"][0]; 
  	switch($test) {
		case "RUNNING": 
	   	  echo "<TD id=ST2A class=fTable_2_1 rowspan=2 bgcolor=00C131>2A</TD>";
	   	  $_SESSION["ST2A"] = MAX_TIME;
	      break;
  		case "PLNSTOP": 
	  	  echo "<TD id=ST2A class=fTable_2_1 rowspan=2 bgcolor=white>2A</TD>";
	  	  $_SESSION["ST2A"] = MAX_TIME;
		  break;
  		default: 
	  	  echo "<TD id=ST2A class=fTable_2_1 rowspan=2 bgcolor=red>2A</TD>";
		  if ($_SESSION["ST2A"] == MAX_TIME) $_SESSION["ST2A"] = time();
	}
	
    $test = @$farray2["STPSTSO_ST2"][0]; 
  	switch($test) {
	  case "RUNNING": 
        echo "<TD class=fTable_2_2 bgcolor=white>Self</TD>";
        echo "</TR>";
        echo "<TR>";
				$test2 = @$farray2["STPTIME_ST2"][0];
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=white>" . $farray2["STPTIME_ST2"][0] . "</TD>";
				}
        echo "</TR>";
	    break;
      case "PLNSTOP":
        echo "<TD class=fTable_2_2 bgcolor=white>Self</TD>";
        echo "</TR>";
        echo "<TR>";
				$test2 = @$farray2["STPTIME_ST2"][0];
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=white>" . $farray2["STPTIME_ST2"][0] . "</TD>";
				}
        echo "</TR>";
	    break;      	  
  	  default:
        echo "<TD class=fTable_2_2 bgcolor=yellow>Self</TD>";
        echo "</TR>";
        echo "<TR>";
				$test2 = @$farray2["STPTIME_ST2"][0];
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=yellow>" . $farray2["STPTIME_ST2"][0] . "</TD>";
				}
        echo "</TR>";
	    break;				
    }
?>			
</TABLE>
<!-- STAMPING 2A -->
<?php
//$ST2A_L = 25 * $x;
//$ST2A_T = 400 * $x;
//$ST2A_H = 120 * $x;
//$ST2A_W = 250 * $x;
$ST2A_L = 25 * $x;
$ST2A_T = 15 * $x;
$ST2A_H = 120 * $x;
$ST2A_W = 250 * $x;
echo "<TABLE style=\"position:absolute; left: {$ST2A_L}px; top: {$ST2A_T}px; height:{$ST2A_H}px; width: {$ST2A_W}px; border:solid Black 1px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR height=10>
    <TD class=fTable_1_1 colspan=3>STAMPING 2A</TD>
  </TR>
  <TR height=10>
    <TD class=fTable_1_2 width=33% style="border-left:none;">Plan Shift</TD>
    <TD class=fTable_1_2 width=33%>Plan</TD>
    <TD class=fTable_1_2>Actual</TD>
  </TR>
  <TR>
    <TD class=fTable_1_3 style="border-left:none;" bgcolor="#CCFFCC"><?php $test = @$farray7["PLNSOTSH_ST2"][0]; if ($test == "") { echo 0;} else {echo $farray7["PLNSOTSH_ST2"][0]; } ?></TD>
    <TD class=fTable_1_3 bgcolor="#CCFFCC"><?php $test = @$farray2["CUREXSOT_ST2"][0]; if ($test == "") { echo 0;} else {echo $farray2["CUREXSOT_ST2"][0]; } ?></TD>
<?php
    $test = @$farray2["ACTSOT_ST2"][0];
		$test2 = @$farray2["CUREXSOT_ST2"][0];
		if (($test == "") && ($test2 == "")) {
      echo "<TD class=fTable_1_3 bgcolor=red>0</TD>";
    }	else {
		  if ($farray2["ACTSOT_ST2"][0] < $farray2["CUREXSOT_ST2"][0]) {
			  echo "<TD class=fTable_1_3 bgcolor='#CCFFCC'>" . $farray2["ACTSOT_ST2"][0] . "</TD>";
//  			echo "<TD class=fTable_1_3 bgcolor=red>" . $farray2["ACTSOT_ST2"][0] . "</TD>";
			} else {
			  echo "<TD class=fTable_1_3 bgcolor='#CCFFCC'>" . $farray2["ACTSOT_ST2"][0] . "</TD>";
			}
		}
?>
  </TR>
  <TR height=10>
    <TD class=fTable_1_2 style="border-left:none;">GSPH</TD>
    <TD class=fTable_1_2>L/A (%)</TD>
    <TD class=fTable_1_2>OT(min)</TD>
  </TR>
  <TR>
    <TD class=fTable_1_3 style="border-left:none;" bgcolor="#CCFFCC"><?php $test = @$farray2["ACTGSPH_ST2"][0]; if ($test == "") { echo 0;} else {echo $farray2["ACTGSPH_ST2"][0]; } ?></TD>
    <TD class=fTable_1_3 bgcolor="#CCFFCC"><?php $test = @$farray2["OPEAVIL_ST2"][0]; if ($test == "") { echo 0;} else {echo number_format($farray2["OPEAVIL_ST2"][0],1); } ?></TD>
    <TD class=fTable_1_3 bgcolor="#CCFFCC"><?php $test = @$farray2["OVERTIME_ST2"][0]; if ($test == "") { echo 0;} else {echo $farray2["OVERTIME_ST2"][0]; } ?></TD>
  </TR>
</TABLE>
<!--<div style="background:black; position:absolute; left:130px; top:240px; height:2px; width: 45;"></div>
<div style="background:black; position:absolute; left:150px; top:240px; height:100; width: 2;"></div>
<div style="background:black; position:absolute; left:150px; top:340px; height:2px; width: 25;"></div>-->
<?php
$STL1_L = 135 * $x;
$STL1_T = 240 * $x;
$STL1_H = 2 * $x;
$STL1_W = 30 * $x;

$STL2_L = 150 * $x;
$STL2_T = 240 * $x;
$STL2_H = 100 * $x;
$STL2_W = 2 * $x;

$STL3_L = 150 * $x;
$STL3_T = 340 * $x;
$STL3_H = 2 * $x;
$STL3_W = 15 * $x;

echo "<hr style=\"position:absolute; left: {$STL1_L}px; top: {$STL1_T}px; height: {$STL1_H}px; width: {$STL1_W}px;\" noshade color=black></hr>";
echo "<hr style=\"position:absolute; left: {$STL2_L}px; top: {$STL2_T}px; height: {$STL2_H}px; width: {$STL2_W}px;\" noshade color=black></hr>";
echo "<hr style=\"position:absolute; left: {$STL3_L}px; top: {$STL3_T}px; height: {$STL3_H}px; width: {$STL3_W}px;\" noshade color=black></hr>";
?>
<!-- WINDOW 1 FINISH-->

<!-- WINDOW 2 -->
<!-- BODY -->
<?php
$BO_L = 720 * $x;
$BO_T = 170 * $x;
$BO_H = 100 * $x;
$BO_W = 140 * $x;
$BO_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: ${BO_L}px; top: {$BO_T}px; height: {$BO_H}px; width: {$BO_W}px; \" cellpadding=\"0\" cellspacing=\"0\">";
  echo "<TR height=".$BO_height.">";
?>  	  
    <TD class=fTable_3_1 colspan=3>BODY</TD>
  </TR>
  <TR height=10>
<?php
//        $test = @$farray8["INVCUR_BODY"][0];
        $test = @$farray3["ACTINVA1"][0];
		$test2 = @$farray9["COLMIN_BODY"][0];
		$test3 = @$farray9["COLMAX_BODY"][0];
		if ($test2 == "") {
		  $test2 = 0;
		}
		if ($test3 == "") {
		  $test3 = 0;
		}
    if ($test == "") {
  		echo "<TD class=fTable_3_2 colspan=3 bgcolor=red>0</TD>";
		} else {
		  if (($test < $test2) || ($test > $test3)) {
			  echo "<TD class=fTable_3_2 colspan=3 bgcolor=red>" . $test . "</TD>";
			} else {
			  echo "<TD class=fTable_3_2 colspan=3 bgcolor=silver>" . $test . "</TD>";
			}
		}
		
?>
  </TR>
  <TR height=10>
    <TD class=fTable_3_3 width=33% style="border-left:2.0pt solid white;"><?php $test = @$farray9["INVMIN_BODY"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVMIN_BODY"][0]; } ?></TD>
    <TD class=fTable_3_3 width=33%><?php $test = @$farray9["INVSTD_BODY"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVSTD_BODY"][0]; } ?></TD>
    <TD class=fTable_3_3><?php $test = @$farray9["INVMAX_BODY"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVMAX_BODY"][0]; } ?></TD>
  </TR>
</TABLE>
<!-- DATA -->
<?php

// $farray8["INVCUR_BDATA"][0]-@$farray3["ACTINVA1_WEL"][0]
$DA_L = 620 * $x;
$DA_T = 170 * $x;
$DA_H = 60 * $x;
$DA_W = 90 * $x;
$DA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$DA_L}px; top: {$DA_T}px; height: {$DA_H}px; width: {$DA_W}px; \" cellpadding=\"0\" cellspacing=\"0\">";
  echo "<TR height=".$DA_height.">";
?>  	  
    <TD class=fTable_3_1 colspan=3>DATA</TD>
  </TR>
  <TR height=10>

<?php
echo "<TD class=fTable_3_2 colspan=3 bgcolor=silver>" . ($farray8["INVCUR_BDATA"][0]-@$farray3["ACTINVA1"][0])."</TD>";
?>

  
</TABLE>	
<!-- BODY (S/B...) -->
<?php
$BO_L = 330 * $x;
$BO_T = 15 * $x;
$BO_H = 145 * $x;
$BO_W = 450 * $x;
$BO_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$BO_L}px; top: {$BO_T}px; height: {$BO_H}px; width: {$BO_W}px; border:solid Black 1px;\" cellpadding=\"0\" cellspacing=\"0\">";
  echo "<TR height=".$BO_height.">";
?>
    <TD class=fTable_1_1_h colspan=3>BODY (D0)</TD>
  </TR>
  <TR height=10>
    <TD class=fTable_1_2_h width=33% style="border-left:none;">Capacity</TD>
    <TD class=fTable_1_2_h width=33%>Plan</TD>
    <TD class=fTable_1_2_h>Actual</TD>
  </TR>
  <TR>
    <TD class=fTable_1_3_h style="border-left:none;" bgcolor="#CCFFCC"><?php $test = number_format($fWorkTimeB/$BODY_TT*60,0); if ($test == "") { echo 0;} else {echo $test; } ?></TD>
    <TD class=fTable_1_3_h bgcolor="#CCFFCC"><?php $test = $fPlanB; if ($test == "") { echo 0;} else { echo /*($WT_D0 == $fDef) ? $fPlanB : $WT_D0*/ $fPlanB ; } ?></TD>
<?php
    $test = @$farray8["ACTPRD_BL"][0];
	$test2 = @$farray3["CUREXPRD_BL"][0];
	if (($test == "") && ($test2 == "")) {
    	echo "<TD class=fTable_1_3_h bgcolor=red>0</TD>";
    }	else {
		if ($test == "") {
			echo "<TD class=fTable_1_3_h bgcolor=red>0</TD>";
		} else {
			if ($test < $test2) {
	  			echo "<TD class=fTable_1_3_h bgcolor='#CCFFCC'>" . $farray8["ACTPRD_BL"][0] . "</TD>";
//  			  echo "<TD class=fTable_1_3 bgcolor=red>" . $farray8["ACTPRD_BL"][0] . "</TD>";
  			} else {
	  			echo "<TD class=fTable_1_3_h bgcolor='#CCFFCC'>" . $farray8["ACTPRD_BL"][0] . "</TD>";
		  	}
		} 
	}
?>
  </TR>
</TABLE>
<?php
$BO_L = 330 * $x;
$BO_T = 470 * $x;
$BO_H = 60 * $x;
$BO_W = 200 * $x;
$BO_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$BO_L}px; top: {$BO_T}px; height: {$BO_H}px; width: {$BO_W}px; border:solid Black 1px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR height=10>
    <TD class=fTable_1_2_t style="border-left:none;">L/S(min)</TD>
    <TD class=fTable_1_2_t>L/A (%)</TD>
    <TD class=fTable_1_2_t>OT(min)</TD>
  </TR>
  <TR>
<?php 
	//modification temporary begin 
	if ((@$farray3["STPSTSS_SB2"][0]=="PLNSTOP")&&(@$farray3["STPSTSO_SB2"][0]=="PLNSTOP")&&(@$farray3["STPSTSF_SB2"][0]=="PLNSTOP")) {
	  $test = "PLNSTOP";
    } else if ((@$farray3["STPSTSS_SB2"][0]=="ERRSTOP")||(@$farray3["STPSTSO_SB2"][0]=="ERRSTOP")||(@$farray3["STPSTSF_SB2"][0]=="ERRSTOP")) {
   	  $test = "ERRSTOP";
	} else {
	  $test = "RUNNING";
	}
	//modification end
//    $test = @$farray3["LINESTS_BDY"][0];
  	switch($test) {
		  case "RUNNING": 
				$test2 = @$farray3["STPTIME_BDY"][0];
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_1_3_t style='border-left:none;' bgcolor='#CCFFCC'>0</TD>";
				} else {
		      echo "<TD class=fTable_1_3_t style='border-left:none;' bgcolor='#CCFFCC'>" . $farray3["STPTIME_BDY"][0] . "</TD>";				
				}
			  break;
		  case "ERRSTOP": 
				$test2 = @$farray3["STPTIME_BDY"][0];
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_1_3_t style='border-left:none;' bgcolor=red>0</TD>";
				} else {
		      echo "<TD class=fTable_1_3_t style='border-left:none;' bgcolor=red>" . $farray3["STPTIME_BDY"][0] . "</TD>";				
				}
			  break;				
  		default: 
				$test2 = @$farray3["STPTIME_BDY"][0];
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_1_3_t style='border-left:none;' bgcolor='#CCFFCC'>0</TD>";
				} else {
		      echo "<TD class=fTable_1_3_t style='border-left:none;' bgcolor='#CCFFCC'>" . $farray3["STPTIME_BDY"][0] . "</TD>";				
				}
			  break;	
		}

?>    
	<TD class=fTable_1_3_t bgcolor="#CCFFCC"><?php $test = number_format(min(99.9,($fWorkTimeB-$farray3["STPTIME_BDY"][0])/$fWorkTimeB*100),1); if ($test == "") { echo 0;} else {echo $test; } ?></TD>
    <TD class=fTable_1_3_t bgcolor="#CCFFCC"><?php $test = @$farray3["OVERTIME_BDY"][0]; if ($test == "") { echo 0;} else {echo $farray3["OVERTIME_BDY"][0]; } ?></TD>
  </TR>
</TABLE>
<!-- S/M - L -->
<?php
$BO_L = 340 * $x;
$BO_T = 170 * $x;
$BO_H = 40 * $x;
$BO_W = 240 * $x;
//$BO_L = 375 * $x;
//$BO_T = 310 * $x;
//$BO_H = 40 * $x;
//$BO_W = 240 * $x;
echo "<TABLE style=\"position:absolute; left: {$BO_L}px; top: {$BO_T}px; height: {$BO_H}px; width: {$BO_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
<?php
	//modification temporary begin 
	if ((@$farray3["STPSTSS_SML"][0]=="PLNSTOP")&&(@$farray3["STPSTSO_SML"][0]=="PLNSTOP")&&(@$farray3["STPSTSF_SML"][0]=="PLNSTOP")) {
	  $test = "PLNSTOP";
    } else if ((@$farray3["STPSTSS_SML"][0]=="ERRSTOP")||(@$farray3["STPSTSO_SML"][0]=="ERRSTOP")||(@$farray3["STPSTSF_SML"][0]=="ERRSTOP")) {
   	  $test = "ERRSTOP";
	} else {
	  $test = "RUNNING";
	}
	//modification end
    //$test = @$farray3["LINESTS_SML"][0]; 
  	switch($test) {
		case "RUNNING": 
	   	  echo "<TD id=\"SM_L\" class=fTable_2_1 rowspan=2 bgcolor=00C131>S/M - L</TD>";
	   	  $_SESSION["SM_L"] = MAX_TIME;
	      break;
  		case "PLNSTOP": 
	  	  echo "<TD id=\"SM_L\" class=fTable_2_1 rowspan=2 bgcolor=white>S/M - L</TD>";
	  	  $_SESSION["SM_L"] = MAX_TIME;
		  break;
  		default: 
	  	  echo "<TD id=\"SM_L\" class=fTable_2_1 rowspan=2 bgcolor=red>S/M - L</TD>";
		  if ($_SESSION["SM_L"] == MAX_TIME) $_SESSION["SM_L"] = time();
	}

    $test = @$farray3["STPSTSS_SML"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_2 bgcolor=yellow>Short</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_2 bgcolor=white>Short</TD>";
		}
		$test = @$farray3["STPSTSO_SML"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_2 bgcolor=yellow>Self</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_2 bgcolor=white>Self</TD>";
		}
		$test = @$farray3["STPSTSF_SML"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_2 bgcolor=yellow>Full</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_2 bgcolor=white>Full</TD>";
		}
?>
  </TR>
  <TR>
<?php
    $test = @$farray3["STPSTSS_SML"][0]; 
		$test2 = @$farray3["STPTIMS_SML"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=yellow>" . $farray3["STPTIMS_SML"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=white>" . $farray3["STPTIMS_SML"][0] . "</TD>";
				}	
				break;
		}			

    $test = @$farray3["STPSTSO_SML"][0]; 
		$test2 = @$farray3["STPTIMO_SML"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=yellow>" . $farray3["STPTIMO_SML"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=white>" . $farray3["STPTIMO_SML"][0] . "</TD>";
				}	
				break;
		}			
    
		$test = @$farray3["STPSTSF_SML"][0]; 
		$test2 = @$farray3["STPTIMF_SML"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=yellow>" . $farray3["STPTIMF_SML"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=white>" . $farray3["STPTIMF_SML"][0] . "</TD>";
				}	
				break;
		}						 	
?>
  </TR>
</TABLE>
<!-- F/B TACK -->
<?php
$BO_L = 320 * $x;
$BO_T = 240 * $x;
$BO_H = 40 * $x;
$BO_W = 280 * $x;
echo "<TABLE style=\"position:absolute; left: {$BO_L}px; top: {$BO_T}px; height: {$BO_H}px; width: {$BO_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
<?php
	$test = @$farray3["STPSTSF_FBTK"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_4 bgcolor=yellow>Full</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_4 bgcolor=white>Full</TD>";
	}	
	$test = @$farray3["STPSTSO_FBTK"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_4 bgcolor=yellow>Self</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_4 bgcolor=white>Self</TD>";
	}
    $test = @$farray3["STPSTSS_FBTK"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_4 bgcolor=yellow>Short</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_4 bgcolor=white>Short</TD>";
	}
	//-----------------------------------------
	//modification temporary begin 
	if ((@$farray3["STPSTSS_FBTK"][0]=="PLNSTOP")&&(@$farray3["STPSTSO_FBTK"][0]=="PLNSTOP")&&(@$farray3["STPSTSF_FBTK"][0]=="PLNSTOP")) {
	  $test = "PLNSTOP";
    } else if ((@$farray3["STPSTSS_FBTK"][0]=="ERRSTOP")||(@$farray3["STPSTSO_FBTK"][0]=="ERRSTOP")||(@$farray3["STPSTSF_FBTK"][0]=="ERRSTOP")) {
   	  $test = "ERRSTOP";
	} else {
	  $test = "RUNNING";
	}
	//modification end
    //$test = @$farray3["LINESTS_FBTK"][0]; 
  	switch($test) {
		case "RUNNING": 
   	      echo "<TD id=\"FB_T\" class=fTable_2_1 rowspan=2 bgcolor=00C131>F/B TACK</TD>";
   	      $_SESSION["FB_T"] = MAX_TIME;
	      break;
  		case "PLNSTOP": 
	  	  echo "<TD id=\"FB_T\" class=fTable_2_1 rowspan=2 bgcolor=white>F/B TACK</TD>";
	  	  $_SESSION["FB_T"] = MAX_TIME;
		  break;
  		default: 
	  	  echo "<TD id=\"FB_T\" class=fTable_2_1 rowspan=2 bgcolor=red>F/B TACK</TD>";
		  if ($_SESSION["FB_T"] == MAX_TIME) $_SESSION["FB_T"] = time();
	}
?>		
  </TR>
  <TR>
<?php
	
	$test = @$farray3["STPSTSF_FBTK"][0]; 
	$test2 = @$farray3["STPTIMF_FBTK"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=yellow>" . $farray3["STPTIMF_FBTK"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=white>" . $farray3["STPTIMF_FBTK"][0] . "</TD>";
				}	
				break;
	}//switch
	
    $test = @$farray3["STPSTSO_FBTK"][0]; 
	$test2 = @$farray3["STPTIMO_FBTK"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=yellow>" . $farray3["STPTIMO_FBTK"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=white>" . $farray3["STPTIMO_FBTK"][0] . "</TD>";
				}	
				break;
	}//switch
	
   $test = @$farray3["STPSTSS_FBTK"][0]; 
	$test2 = (@$farray3["STPTIMS_FBTK"][0]+@$farray3["STPTIMO_FBTLB"][0]+@$farray3["STPTIMO_FBTCW"][0]
             +@$farray3["STPTIMO_FBTHD"][0]+@$farray3["STPTIMO_FBTBF"][0]+@$farray3["STPTIMO_FBTRF"][0]
             +@$farray3["STPTIMO_SMRST"][0]+@$farray3["STPTIMO_SMLST"][0]);
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=yellow>" . $test2 . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=white>" . $test2 . "</TD>";
				}	
				break;
	}//switch			
								 	
?>
  </TR>
</TABLE>
<!-- S/M - R -->
<?php
$BO_L = 340 * $x;
$BO_T = 310 * $x;
$BO_H = 40 * $x;
$BO_W = 240 * $x;	
//$BO_L = 375 * $x;
//$BO_T = 170 * $x;
//$BO_H = 40 * $x;
//$BO_W = 240 * $x;
echo "<TABLE style=\"position:absolute; left: {$BO_L}px; top: {$BO_T}px; height: {$BO_H}px; width: {$BO_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
<?php
	//modification temporary begin 
	if ((@$farray3["STPSTSS_SMR"][0]=="PLNSTOP")&&(@$farray3["STPSTSO_SMR"][0]=="PLNSTOP")&&(@$farray3["STPSTSF_SMR"][0]=="PLNSTOP")) {
	  $test = "PLNSTOP";
    } else if ((@$farray3["STPSTSS_SMR"][0]=="ERRSTOP")||(@$farray3["STPSTSO_SMR"][0]=="ERRSTOP")||(@$farray3["STPSTSF_SMR"][0]=="ERRSTOP")) {
   	  $test = "ERRSTOP";
	} else {
	  $test = "RUNNING";
	}
	//modification end
    //$test = @$farray3["LINESTS_SMR"][0]; 
  	switch($test) {
    	case "RUNNING": 
	   	  echo "<TD id=\"SM_R\" class=fTable_2_1 rowspan=2 bgcolor=00C131>S/M - R</TD>";
	   	  $_SESSION["SM_R"] = MAX_TIME;
	      break;
  		case "PLNSTOP": 
	  	  echo "<TD id=\"SM_R\" class=fTable_2_1 rowspan=2 bgcolor=white>S/M - R</TD>";
	  	  $_SESSION["SM_R"] = MAX_TIME;
		  break;
  		default: 
	  	  echo "<TD id=\"SM_R\" class=fTable_2_1 rowspan=2 bgcolor=red>S/M - R</TD>";
		  if ($_SESSION["SM_R"] == MAX_TIME) $_SESSION["SM_R"] = time();
		}

    $test = @$farray3["STPSTSS_SMR"][0]; 
  	switch($test) {
	case "ERRSTOP": 
        echo "<TD class=fTable_2_2 bgcolor=yellow>Short</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_2 bgcolor=white>Short</TD>";
	}
	$test = @$farray3["STPSTSO_SMR"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_2 bgcolor=yellow>Self</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_2 bgcolor=white>Self</TD>";
	}
	$test = @$farray3["STPSTSF_SMR"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_2 bgcolor=yellow>Full</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_2 bgcolor=white>Full</TD>";
	}
?>
  </TR>
  <TR>
<?php
    $test = @$farray3["STPSTSS_SMR"][0]; 
	$test2 = @$farray3["STPTIMS_SMR"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=yellow>" . $farray3["STPTIMS_SMR"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=white>" . $farray3["STPTIMS_SMR"][0] . "</TD>";
				}	
				break;
		}			

    $test = @$farray3["STPSTSO_SMR"][0]; 
	$test2 = @$farray3["STPTIMO_SMR"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=yellow>" . $farray3["STPTIMO_SMR"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=white>" . $farray3["STPTIMO_SMR"][0] . "</TD>";
				}	
				break;
		}			
    
		$test = @$farray3["STPSTSF_SMR"][0]; 
		$test2 = @$farray3["STPTIMF_SMR"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=yellow>" . $farray3["STPTIMF_SMR"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=white>" . $farray3["STPTIMF_SMR"][0] . "</TD>";
				}	
				break;
		}						 	
?>
  </TR>
</TABLE>
<!-- F/B RESPOT -->
<?php
$BO_L = 320 * $x;
$BO_T = 380 * $x;
$BO_H = 40 * $x;
$BO_W = 280 * $x;
echo "<TABLE style=\"position:absolute; left: {$BO_L}px; top: {$BO_T}px; height: {$BO_H}px; width: {$BO_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
<?php
	//modification temporary begin 
	if ((@$farray3["STPSTSS_FBRS"][0]=="PLNSTOP")&&(@$farray3["STPSTSO_FBRS"][0]=="PLNSTOP")&&(@$farray3["STPSTSF_FBRS"][0]=="PLNSTOP")) {
	  $test = "PLNSTOP";
    } else if ((@$farray3["STPSTSS_FBRS"][0]=="ERRSTOP")||(@$farray3["STPSTSO_FBRS"][0]=="ERRSTOP")||(@$farray3["STPSTSF_FBRS"][0]=="ERRSTOP")) {
   	  $test = "ERRSTOP";
	} else {
	  $test = "RUNNING";
	}
	//modification end
    //$test = @$farray3["LINESTS_FBRS"][0]; 
  	switch($test) {
		case "RUNNING": 
	   	  echo "<TD id=\"FB_R\" class=fTable_2_1 rowspan=2 bgcolor=00C131>F/B RESPOT</TD>";
	   	  $_SESSION["FB_R"] = MAX_TIME;
	      break;
  		case "PLNSTOP": 
	  	  echo "<TD id=\"FB_R\" class=fTable_2_1 rowspan=2 bgcolor=white>F/B RESPOT</TD>";
	  	  $_SESSION["FB_R"] = MAX_TIME;
		  break;
  		default: 
	  	  echo "<TD id=\"FB_R\" class=fTable_2_1 rowspan=2 bgcolor=red>F/B RESPOT</TD>";
		  if ($_SESSION["FB_R"] == MAX_TIME) $_SESSION["FB_R"] = time();
	}

    $test = @$farray3["STPSTSS_FBRS"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_2 bgcolor=yellow>Short</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_2 bgcolor=white>Short</TD>";
		}
		$test = @$farray3["STPSTSO_FBRS"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_2 bgcolor=yellow>Self</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_2 bgcolor=white>Self</TD>";
		}
		$test = @$farray3["STPSTSF_FBRS"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_2 bgcolor=yellow>Full</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_2 bgcolor=white>Full</TD>";
		}
?>
  </TR>
  <TR>
<?php
    $test = @$farray3["STPSTSS_FBRS"][0]; 
		$test2 = @$farray3["STPTIMS_FBRS"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=yellow>" . $farray3["STPTIMS_FBRS"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=white>" . $farray3["STPTIMS_FBRS"][0] . "</TD>";
				}	
				break;
		}			

    $test = @$farray3["STPSTSO_FBRS"][0]; 
		$test2 = @$farray3["STPTIMO_FBRS"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=yellow>" . $farray3["STPTIMO_FBRS"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=white>" . $farray3["STPTIMO_FBRS"][0] . "</TD>";
				}	
				break;
		}			
    
		$test = @$farray3["STPSTSF_FBRS"][0]; 
		$test2 = @$farray3["STPTIMF_FBRS"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=yellow>" . $farray3["STPTIMF_FBRS"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=white>" . $farray3["STPTIMF_FBRS"][0] . "</TD>";
				}	
				break;
		}						 	
?>
  </TR>
</TABLE>
<!-- S/B - 1 -->
<?php
$BO_L = 650 * $x;
$BO_T = 335 * $x;
$BO_H = 40 * $x;
$BO_W = 240 * $x;
echo "<TABLE style=\"position:absolute; left: {$BO_L}px; top: {$BO_T}px; height: {$BO_H}px; width: {$BO_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
<?php
	//modification temporary begin 
	if ((@$farray3["STPSTSS_SB1"][0]=="PLNSTOP")&&(@$farray3["STPSTSO_SB1"][0]=="PLNSTOP")&&(@$farray3["STPSTSF_SB1"][0]=="PLNSTOP")) {
	  $test = "PLNSTOP";
    } else if ((@$farray3["STPSTSS_SB1"][0]=="ERRSTOP")||(@$farray3["STPSTSO_SB1"][0]=="ERRSTOP")||(@$farray3["STPSTSF_SB1"][0]=="ERRSTOP")) {
   	  $test = "ERRSTOP";
	} else {
	  $test = "RUNNING";
	}
	//modification end
    //$test = @$farray3["LINESTS_SB1"][0]; 
  	switch($test) {
		case "RUNNING": 
	   	  echo "<TD id=\"SB_1\" class=fTable_2_1 rowspan=2 bgcolor=00C131>S/B - 1</TD>";
	   	  $_SESSION["SB_1"] = MAX_TIME;
	      break;
  		case "PLNSTOP": 
	  	  echo "<TD id=\"SB_1\" class=fTable_2_1 rowspan=2 bgcolor=white>S/B - 1</TD>";
	  	  $_SESSION["SB_1"] = MAX_TIME;
		  break;
  		default: 
	  	  echo "<TD id=\"SB_1\" class=fTable_2_1 rowspan=2 bgcolor=red>S/B - 1</TD>";
		  if ($_SESSION["SB_1"] == MAX_TIME) $_SESSION["SB_1"] = time();
	}

    $test = @$farray3["STPSTSS_SB1"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_2 bgcolor=yellow>Short</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_2 bgcolor=white>Short</TD>";
		}
		$test = @$farray3["STPSTSO_SB1"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_2 bgcolor=yellow>Self</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_2 bgcolor=white>Self</TD>";
		}
		$test = @$farray3["STPSTSF_SB1"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_2 bgcolor=yellow>Full</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_2 bgcolor=white>Full</TD>";
		}
?>
  </TR>
  <TR>
<?php
    $test = @$farray3["STPSTSS_SB1"][0]; 
		$test2 = @$farray3["STPTIMS_SB1"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=yellow>" . $farray3["STPTIMS_SB1"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=white>" . $farray3["STPTIMS_SB1"][0] . "</TD>";
				}	
				break;
		}			

    $test = @$farray3["STPSTSO_SB1"][0]; 
		$test2 = @$farray3["STPTIMO_SB1"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=yellow>" . $farray3["STPTIMO_SB1"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=white>" . $farray3["STPTIMO_SB1"][0] . "</TD>";
				}	
				break;
		}			
    
		$test = @$farray3["STPSTSF_SB1"][0]; 
		$test2 = @$farray3["STPTIMF_SB1"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=yellow>" . $farray3["STPTIMF_SB1"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=white>" . $farray3["STPTIMF_SB1"][0] . "</TD>";
				}	
				break;
		}						 	
?>
  </TR>
</TABLE>
<!-- S/B - 2 -->
<?php
$BO_L = 650 * $x;
$BO_T = 400 * $x;
$BO_H = 40 * $x;
$BO_W = 240 * $x;
echo "<TABLE style=\"position:absolute; left: {$BO_L}px; top: {$BO_T}px; height: {$BO_H}px; width: {$BO_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
<?php
	$test = @$farray3["STPSTSF_SB2"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_4 bgcolor=yellow>Full</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_4 bgcolor=white>Full</TD>";
	}
	$test = @$farray3["STPSTSO_SB2"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_4 bgcolor=yellow>Self</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_4 bgcolor=white>Self</TD>";
	}
    $test = @$farray3["STPSTSS_SB2"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_4 bgcolor=yellow>Short</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_4 bgcolor=white>Short</TD>";
	}
    //---
	//modification temporary begin 
	if ((@$farray3["STPSTSS_SB2"][0]=="PLNSTOP")&&(@$farray3["STPSTSO_SB2"][0]=="PLNSTOP")&&(@$farray3["STPSTSF_SB2"][0]=="PLNSTOP")) {
	  $test = "PLNSTOP";
    } else if ((@$farray3["STPSTSS_SB2"][0]=="ERRSTOP")||(@$farray3["STPSTSO_SB2"][0]=="ERRSTOP")||(@$farray3["STPSTSF_SB2"][0]=="ERRSTOP")) {
   	  $test = "ERRSTOP";
	} else {
	  $test = "RUNNING";
	}
	//modification end
    //$test = @$farray3["LINESTS_SB2"][0]; 
  	switch($test) {
		case "RUNNING": 
	   	  echo "<TD id=\"SB_2\" class=fTable_2_1 rowspan=2 bgcolor=00C131>S/B - 2</TD>";
	   	  $_SESSION["SB_2"] = MAX_TIME;
	      break;
  		case "PLNSTOP": 
	  	  echo "<TD id=\"SB_2\" class=fTable_2_1 rowspan=2 bgcolor=white>S/B - 2</TD>";
	  	  $_SESSION["SB_2"] = MAX_TIME;
		  break;
  		default: 
	  	  echo "<TD id=\"SB_2\" class=fTable_2_1 rowspan=2 bgcolor=red>S/B - 2</TD>";
		  if ($_SESSION["SB_2"] == MAX_TIME) $_SESSION["SB_2"] = time();
	}
?>
  </TR>
  <TR>
<?php
	$test = @$farray3["STPSTSF_SB2"][0]; 
	$test2 = @$farray3["STPTIMF_SB2"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=yellow>" . $farray3["STPTIMF_SB2"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=white>" . $farray3["STPTIMF_SB2"][0] . "</TD>";
				}	
				break;
	}
	
    $test = @$farray3["STPSTSO_SB2"][0]; 
	$test2 = @$farray3["STPTIMO_SB2"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=yellow>" . $farray3["STPTIMO_SB2"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=white>" . $farray3["STPTIMO_SB2"][0] . "</TD>";
				}	
				break;
	}			
    
	$test = @$farray3["STPSTSS_SB2"][0]; 
	$test2 = @$farray3["STPTIMS_SB2"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=yellow>" . $farray3["STPTIMS_SB2"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=white>" . $farray3["STPTIMS_SB2"][0] . "</TD>";
				}	
				break;
	}			
	
?>
  </TR>
</TABLE>
<!-- U/B -->
<?php
$BO_L = 870 * $x;
$BO_T = 15 * $x;
$BO_H = 240 * $x;
$BO_W = 50 * $x;
$BO_PIC_H = 50 * $x;
$BO_PIC_W = 30 * $x;
echo "<TABLE style=\"position:absolute; left: {$BO_L}px; top: {$BO_T}px; height: {$BO_H}px; width: {$BO_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
  echo "<TR>";
	//modification temporary begin 
	if ((@$farray3["STPSTSS_UB"][0]=="PLNSTOP")&&(@$farray3["STPSTSO_UB"][0]=="PLNSTOP")&&(@$farray3["STPSTSF_UB"][0]=="PLNSTOP")) {
	  $test = "PLNSTOP";
    } else if ((@$farray3["STPSTSS_UB"][0]=="ERRSTOP")||(@$farray3["STPSTSO_UB"][0]=="ERRSTOP")||(@$farray3["STPSTSF_UB"][0]=="ERRSTOP")) {
   	  $test = "ERRSTOP";
	} else {
	  $test = "RUNNING";
	}
	//modification end
    //$test = @$farray3["LINESTS_UB"][0]; 
  	switch($test) {
		case "RUNNING": 
	   	  echo "<TD id=\"UB\" class=fTable_2_1 bgcolor=00C131 style=\"\"><img alt=\"UB (1K)\" src=\"pictures/UB.gif\" height=".$BO_PIC_H." width=".$BO_PIC_W." /></TD>";
	   	  $_SESSION["UB"] = MAX_TIME;
	      break;
  		case "PLNSTOP": 
	  	  echo "<TD id=\"UB\" class=fTable_2_1 bgcolor=white style=\"\"><img alt=\"UB (1K)\" src=\"pictures/UB.gif\" height=".$BO_PIC_H." width=".$BO_PIC_W." /></TD>";
	  	  $_SESSION["UB"] = MAX_TIME;
		  break;
  		default: 
	  	  echo "<TD id=\"UB\" class=fTable_2_1 bgcolor=red style=\"\"><img alt=\"UB (1K)\" src=\"pictures/UB.gif\" height=".$BO_PIC_H." width=".$BO_PIC_W." /></TD>";
		  if ($_SESSION["UB"] == MAX_TIME) $_SESSION["UB"] = time();
	}
?>
  </TR>
  <TR>
<?php
    $test = @$farray3["STPSTSS_UB"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_6 bgcolor=yellow>Short</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_6 bgcolor=white>Short</TD>";
		}
?>
  </TR>
  <TR>
<?php
    $test = @$farray3["STPSTSS_UB"][0]; 
	$test2 = @$farray3["STPTIMS_UB"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_7 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_7 bgcolor=yellow>" . $farray3["STPTIMS_UB"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_7 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_7 bgcolor=white>" . $farray3["STPTIMS_UB"][0] . "</TD>";
				}	
				break;
		}			
?>
  </TR>
  <TR>
<?php
    $test = @$farray3["STPSTSO_UB"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_6 bgcolor=yellow>Self</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_6 bgcolor=white>Self</TD>";
		}
?>
  </TR>
  <TR>
<?php
    $test = @$farray3["STPSTSO_UB"][0]; 
		$test2 = @$farray3["STPTIMO_UB"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_7 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_7 bgcolor=yellow>" . $farray3["STPTIMO_UB"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_7 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_7 bgcolor=white>" . $farray3["STPTIMO_UB"][0] . "</TD>";
				}	
				break;
		}			
?>
  </TR>
  <TR>
<?php
    $test = @$farray3["STPSTSF_UB"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_6 bgcolor=yellow>Full</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_6 bgcolor=white>Full</TD>";
		}
?>
  </TR>
  <TR>
<?php
    $test = @$farray3["STPSTSF_UB"][0]; 
		$test2 = @$farray3["STPTIMF_UB"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_7 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_7 bgcolor=yellow>" . $farray3["STPTIMF_UB"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_7 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_7 bgcolor=white>" . $farray3["STPTIMF_UB"][0] . "</TD>";
				}	
				break;
		}			
?>
  </TR>
</TABLE>
<!-- Body Repair -->
<?php
$BO_L = 630 * $x;
$BO_T = 465 * $x;
$BO_H = 80 * $x;
$BO_W = 80 * $x;
echo "<TABLE style=\"position:absolute; left: {$BO_L}px; top: {$BO_T}px; height: {$BO_H}px; width: {$BO_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
    <TD class=fTable_5_1 colspan=2 style="">Body</TD>
  </TR>
  <TR bgcolor="#00CCFF">
<?php  
        $test = @$farray8["INVCUR_BDRP"][0];
  	    $test2 = @$farray9["INVMAX_BDRP"][0];
  	    $test3 = @$farray9["COLMAX_BDRP"][0];
		
		if ($test == "") { 
  	        echo "<TD class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>0</TD>";
			if ($test2 == "") {
                echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/0</BR></TD>";
			} else {
			    echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/" . $farray9["INVMAX_BDRP"][0] . "</BR></TD>";
			}
		} else {
			if ($test2 == "") { 
			    echo "<TD class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>" . $farray8["INVCUR_BDRP"][0] . "</TD>";
                echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>0</BR></TD>";
			} else {
                if ($test > $test3)	{		
  			        echo "<TD class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>" . $farray8["INVCUR_BDRP"][0] . "</BR></TD>";
                    echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/" . $farray9["INVMAX_BDRP"][0] . "</BR></TD>";				
				} else {
				    echo "<TD class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor='#00CCFF'>" . $farray8["INVCUR_BDRP"][0] . "</BR></TD>";
                    echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor='#00CCFF'>/" . $farray9["INVMAX_BDRP"][0] . "</BR></TD>";					
				}
			}		
		}
?>
  </TR>
</TABLE>


<!-- Body OK -->
<?php
$BO_L = 725 * $x;
$BO_T = 465 * $x;
$BO_H = 80 * $x;
$BO_W = 80 * $x;
echo "<TABLE style=\"position:absolute; left: {$BO_L}px; top: {$BO_T}px; height: {$BO_H}px; width: {$BO_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
    <TD class=fTable_5_1 style="">OK</TD>
  </TR>
  <TR bgcolor="#00CCFF">
<?php  
	$test = @$farray8["INVCUR_WELD_OK"][0];
//  	$test2 = @$farray9["INVMAX_PFRP"][0];
//  	$test3 = @$farray9["COLMAX_PFRP"][0];
		
	if ($test == "") { 
			echo "<TD class=fTable_5_2 style='border-left:1.0pt solid white; border-right:1.0pt solid white; text-align:center;' bgcolor='#00CCFF'>0</BR></TD>";
		
	} else {
			echo "<TD class=fTable_5_2 style='border-left:1.0pt solid white; border-right:1.0pt solid white; text-align:center;' bgcolor='#00CCFF'>" . $test . "</BR></TD>";
			}
				
	
?>
  </TR>
</TABLE>

<!-- Body Repaired -->
<?php
$BO_L = 820 * $x;
$BO_T = 465 * $x;
$BO_H = 80 * $x;
$BO_W = 80 * $x;
echo "<TABLE style=\"position:absolute; left: {$BO_L}px; top: {$BO_T}px; height: {$BO_H}px; width: {$BO_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
    <TD class=fTable_5_1 style="">Repaired</TD>
  </TR>
  <TR bgcolor="#00CCFF">
<?php  
	$test = @$farray8["INVCUR_WELD_REPAIRED"][0];
//  	$test2 = @$farray9["INVMAX_PFRP"][0];
//  	$test3 = @$farray9["COLMAX_PFRP"][0];
		
	if ($test == "") { 
			echo "<TD class=fTable_5_2 style='border-left:1.0pt solid white; border-right:1.0pt solid white; text-align:center;' bgcolor='#00CCFF'>0</BR></TD>";
		
	} else {
				echo "<TD class=fTable_5_2 style='border-left:1.0pt solid white; border-right:1.0pt solid white; text-align:center;' bgcolor='#00CCFF'>" . $test . "</BR></TD>";
			}
				
	
?>
  </TR>
</TABLE>

<?php
//start BODY
//F/B TACK a U/B
$BO01_L = 600 * $x;
$BO01_T = 280 * $x;
$BO01_H = 2 * $x;
$BO01_W = 290 * $x;

$BO02_L = 890 * $x;
$BO02_T = 254 * $x;
$BO02_H = 28 * $x;
$BO02_W = 2 * $x;
echo "<hr style=\"position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px;\" noshade color=black>";
echo "<hr style=\"position:absolute; left: {$BO02_L}px; top: {$BO02_T}px; height: {$BO02_H}px; width: {$BO02_W}px;\" noshade color=black>";
//S/M-R, S/M-L a F/B TACK - vodorovne
$BO03_L = 580 * $x;
$BO03_T = 200 * $x;
$BO03_H = 2 * $x;
$BO03_W = 10 * $x;

$BO04_L = 580 * $x;
$BO04_T = 330 * $x;
$BO04_H = 2 * $x;
$BO04_W = 10 * $x;
echo "<hr style=\"position:absolute; left: {$BO03_L}px; top: {$BO03_T}px; height: {$BO03_H}px; width: {$BO03_W}px;\" noshade color=black>";
echo "<hr style=\"position:absolute; left: {$BO04_L}px; top: {$BO04_T}px; height: {$BO04_H}px; width: {$BO04_W}px;\" noshade color=black>";
//S/M-R, S/M-L a F/B TACK - svisle
$BO01_L = 590 * $x;
$BO01_T = 200 * $x;
$BO01_H = 40 * $x;
$BO01_W = 2 * $x;

$BO02_L = 590 * $x;
$BO02_T = 290 * $x;
$BO02_H = 42 * $x;
$BO02_W = 2 * $x;
echo "<hr style=\"position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px;\" noshade color=black>";
echo "<hr style=\"position:absolute; left: {$BO02_L}px; top: {$BO02_T}px; height: {$BO02_H}px; width: {$BO02_W}px;\" noshade color=black>";
//vodorovne F/B TACK a F/B RESPOT
$BO01_L = 312 * $x;
$BO01_T = 265 * $x;
$BO01_H = 2 * $x;
$BO01_W = 8 * $x;

$BO02_L = 312 * $x;
$BO02_T = 405 * $x;
$BO02_H = 2 * $x;
$BO02_W = 8 * $x;
echo "<hr style=\"position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px;\" noshade color=black>";
echo "<hr style=\"position:absolute; left: {$BO02_L}px; top: {$BO02_T}px; height: {$BO02_H}px; width: {$BO02_W}px;\" noshade color=black>";
//svisle F/B TACK a F/B RESPOT
$BO01_L = 312 * $x;
$BO01_T = 265 * $x;
$BO01_H = 142 * $x;
$BO01_W = 2 * $x;

$BO02_L = 345 * $x;
$BO02_T = 365 * $x;
$BO02_H = 40 * $x;
$BO02_W = 2 * $x;
echo "<hr style=\"position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px;\" noshade color=black>";
//echo "<hr style=\"position:absolute; left: {$BO02_L}px; top: {$BO02_T}px; height: {$BO02_H}px; width: {$BO02_W}px;\" noshade color=black>";

// F/B TACK a S/B - 1
$BO01_L = 600 * $x;
$BO01_T = 405 * $x;
$BO01_H = 2 * $x;
$BO01_W = 18 * $x;

$BO02_L = 618 * $x;
$BO02_T = 360 * $x;
$BO02_H = 2 * $x;
$BO02_W = 33 * $x;

$BO03_L = 618 * $x;
$BO03_T = 360 * $x;
$BO03_H = 46 * $x;
$BO03_W = 2 * $x;
echo "<hr style=\"position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px;\" noshade color=black>";
echo "<hr style=\"position:absolute; left: {$BO02_L}px; top: {$BO02_T}px; height: {$BO02_H}px; width: {$BO02_W}px;\" noshade color=black>";
echo "<hr style=\"position:absolute; left: {$BO03_L}px; top: {$BO03_T}px; height: {$BO03_H}px; width: {$BO03_W}px;\" noshade color=black>";


// S/B - 1 a S/B - 2
$BO01_L = 890 * $x;
$BO01_T = 360 * $x;
$BO01_H = 2 * $x;
$BO01_W = 8 * $x;

$BO02_L = 890 * $x;
$BO02_T = 425 * $x;
$BO02_H = 2 * $x;
$BO02_W = 8 * $x;

$BO03_L = 898 * $x;
$BO03_T = 360 * $x;
$BO03_H = 67 * $x;
$BO03_W = 2 * $x;
echo "<hr style=\"position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px;\" noshade color=black>";
echo "<hr style=\"position:absolute; left: {$BO02_L}px; top: {$BO02_T}px; height: {$BO02_H}px; width: {$BO02_W}px;\" noshade color=black>";
echo "<hr style=\"position:absolute; left: {$BO03_L}px; top: {$BO03_T}px; height: {$BO03_H}px; width: {$BO03_W}px;\" noshade color=black>";

//BODY - PAINT  W - WASH to S/B - 2
$BO01_L = 620 * $x;
$BO01_T = 435 * $x;
$BO01_H = 2 * $x;
$BO01_W = 30 * $x;

$BO02_L = 620 * $x;
$BO02_T = 435 * $x;
$BO02_H = 146 * $x;
$BO02_W = 2 * $x;
echo "<hr style=\"position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px;\" noshade color=black>";
echo "<hr style=\"position:absolute; left: {$BO02_L}px; top: {$BO02_T}px; height: {$BO02_H}px; width: {$BO02_W}px;\" noshade color=black>";
?>	
<!-- WINDOW 2 FINISH-->


<!-- BODY - PAINT -->
<!-- WINDOW 3 -->
<!-- PAINT -->

<!-- W - WASH -->
<?php
$BO_L = 560 * $x;//600
$BO_T = 580 * $x;//580
$BO_H = 100 * $x;
$BO_W = 110 * $x;
$BO_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$BO_L}px; top: {$BO_T}px; height: {$BO_H}px; width: {$BO_W}px; \" cellpadding=\"0\" cellspacing=\"0\">";
  echo "<TR height=".$BO_height.">";
?>
    <TD class=fTable_4_1 colspan=3>W - WASH</TD>
  </TR>
  <TR height=10>
<?php
        $test = @$farray4["INVCUR_BTOP"][0];
		$test2 = @$farray9["COLMIN_BTOP2"][0];
		$test3 = @$farray9["COLMAX_BTOP2"][0];
		if ($test2 == "") {
		  $test2 = 0;
		}
		if ($test3 == "") {
		  $test3 = 0;
		}
    if ($test == "") {
  		echo "<TD class=fTable_4_2 colspan=3 bgcolor=red>0</TD>";
		} else {
		  if (($test < $test2) || ($test > $test3)) {
			  echo "<TD class=fTable_4_2 colspan=3 bgcolor=red>" . $farray4["INVCUR_BTOP"][0] . "</TD>";
			} else {
			  echo "<TD class=fTable_4_2 colspan=3 bgcolor=silver>" . $farray4["INVCUR_BTOP"][0] . "</TD>";
			}
		}
		
?>
  </TR>
  <TR height=10>
    <TD class=fTable_4_3 width=33% style="border-left:1.0pt solid black;"><?php $test = @$farray9["INVMIN_BTOP2"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVMIN_BTOP2"][0]; } ?></TD>
    <TD class=fTable_4_3 width=33%><?php $test = @$farray9["INVSTD_BTOP2"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVSTD_BTOP2"][0]; } ?></TD>
    <TD class=fTable_4_3><?php $test = @$farray9["INVMAX_BTOP2"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVMAX_BTOP2"][0]; } ?></TD>
  </TR>
</TABLE>

<!-- ED IN -->
<?php
$BO03_L = 669 * $x;
$BO03_T = 625 * $x;
$BO03_H = 2 * $x;
$BO03_W = 144 * $x;
echo "<hr style=\"position:absolute; left: {$BO03_L}px; top: {$BO03_T}px; height: {$BO03_H}px; width: {$BO03_W}px;\" noshade color=black>";

$PA_L = 685 * $x;
$PA_T = 590 * $x;
$PA_H = 80 * $x;
$PA_W = 50 * $x;
$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
<?php
    $test = $farray4["STAT_PTED_ENTRANCE"][0];
//    $test = "2";
  	switch($test) {
		case "4": 
	   	  echo "<TD id=\"SKID_C\" class=fTable_2_1 bgcolor=00C131>
	   			<TABLE>
	   			<TR><TD class=fTable_2_1_pai>skid</TD></TR>
	   			<TR><TD class=fTable_2_1_pai>-></TD></TR>
	   			<TR><TD class=fTable_2_1_pai>C</TD></TR></TABLE></TD>";
	   	  $_SESSION["SKID_C"] = MAX_TIME;
	      break;
  		case "1": 
	  	  echo "<TD id=\"SKID_C\" class=fTable_2_1 bgcolor=white>
	  	    	<TABLE>
	  	    	<TR><TD class=fTable_2_1_pai>skid</TD></TR>
	  	    	<TR><TD class=fTable_2_1_pai>-></TD></TR>
	  			<TR><TD class=fTable_2_1_pai>C</TD></TR></TABLE></TD>";
	  	  $_SESSION["SKID_C"] = MAX_TIME;
		  break;
  		case "2": 
	  	  echo "<TD id=\"SKID_C\" class=fTable_2_1 bgcolor=red>
	  	    	<TABLE>
	  	    	<TR><TD class=fTable_2_1_pai>skid</TD></TR>
	  	    	<TR><TD class=fTable_2_1_pai>-></TD></TR>
	  			<TR><TD class=fTable_2_1_pai>C</TD></TR></TABLE></TD>";
            if ($_SESSION["SKID_C"] == MAX_TIME) $_SESSION["SKID_C"] = time();
		  break;
  		case "3": 
	  	  echo "<TD id=\"SKID_C\" class=fTable_2_1 bgcolor=yellow>
	  	    	<TABLE>
	  	    	<TR><TD class=fTable_2_1_pai>skid</TD></TR>
	  	    	<TR><TD class=fTable_2_1_pai>-></TD></TR>
	  			<TR><TD class=fTable_2_1_pai>C</TD></TR></TABLE></TD>";
            $_SESSION["SKID_C"] = MAX_TIME;
		  break;
  		default: 
	  	  echo "<TD id=\"SKID_C\" class=fTable_2_1 bgcolor=red>
	  			<TABLE>
	  			<TR><TD class=fTable_2_1_pai>skid</TD></TR>
	  			<TR><TD class=fTable_2_1_pai>-></TD></TR>
	  			<TR><TD class=fTable_2_1_pai>C</TD></TR></TABLE></TD>";
		  if ($_SESSION["SKID_C"] == MAX_TIME) $_SESSION["SKID_C"] = time();
		}
//$_SESSION["SKID_C"] = time();
?>
</TR>
</TABLE>

<!-- ED OUT -->
<?php
$PA_L = 745 * $x;
$PA_T = 590 * $x;
$PA_H = 80 * $x;
$PA_W = 50 * $x;
$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
<?php
    $test = $farray4["STAT_PTED_EXIT"][0]; 
  	switch($test) {
		case "4": 
	   	  echo "<TD class=fTable_2_1 bgcolor=00C131>
	   			<TABLE>
	   			<TR><TD class=fTable_2_1_pai>C</TD></TR>
	   			<TR><TD class=fTable_2_1_pai>-></TD></TR>
	   			<TR><TD class=fTable_2_1_pai>skid</TD></TR></TABLE></TD>";
	   	  //$_SESSION["SKID_C"] = MAX_TIME;
	      break;
  		case "1": 
	  	  echo "<TD class=fTable_2_1 bgcolor=white>
	  			<TABLE>
	  			<TR><TD class=fTable_2_1_pai>C</TD></TR>
	  			<TR><TD class=fTable_2_1_pai>-></TD></TR>
	  			<TR><TD class=fTable_2_1_pai>skid</TD></TR></TABLE></TD>";
	  	  //$_SESSION["SKID_C"] = MAX_TIME;
		  break;
  		case "2": 
	  	  echo "<TD class=fTable_2_1 bgcolor=red>
	  			<TABLE>
	  			<TR><TD class=fTable_2_1_pai>C</TD></TR>
	  			<TR><TD class=fTable_2_1_pai>-></TD></TR>
	  			<TR><TD class=fTable_2_1_pai>skid</TD></TR></TABLE></TD>";
	  	  //$_SESSION["SKID_C"] = MAX_TIME;
		  break;
  		case "3": 
	  	  echo "<TD class=fTable_2_1 bgcolor=yellow>
	  			<TABLE>
	  			<TR><TD class=fTable_2_1_pai>C</TD></TR>
	  			<TR><TD class=fTable_2_1_pai>-></TD></TR>
	  			<TR><TD class=fTable_2_1_pai>skid</TD></TR></TABLE></TD>";
	  	  //$_SESSION["SKID_C"] = MAX_TIME;
		  break;
  		default: 
	  	  echo "<TD class=fTable_2_1 bgcolor=red>
	  			<TABLE>
	  			<TR><TD class=fTable_2_1_pai>C</TD></TR>
	  			<TR><TD class=fTable_2_1_pai>-></TD></TR>
	  			<TR><TD class=fTable_2_1_pai>skid</TD></TR></TABLE></TD>";
		  //if ($_SESSION["SKID_C"] == MAX_TIME) $_SESSION["SKID_C"] = time();
		}
?>
</TR>
</TABLE>

<!-- WASH-ED IN -->
<?php
$BO_L = 810 * $x;//600
$BO_T = 580 * $x;//580
$BO_H = 100 * $x;
$BO_W = 110 * $x;
$BO_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$BO_L}px; top: {$BO_T}px; height: {$BO_H}px; width: {$BO_W}px; \" cellpadding=\"0\" cellspacing=\"0\">";
  echo "<TR height=".$BO_height.">";
?>
    <TD class=fTable_4_1 colspan=3>WASH-ED IN.</TD>
  </TR>
  <TR height=10>
<?php
        $test = $farray8["INVCUR_BTOP"][0] - $farray4["INVCUR_BTOP"][0];
		$test2 = @$farray9["COLMIN_BTOP"][0];
		$test3 = @$farray9["COLMAX_BTOP"][0];
		if ($test2 == "") {
		  $test2 = 0;
		}
		if ($test3 == "") {
		  $test3 = 0;
		}
    if ($test == "") {
  		echo "<TD class=fTable_4_2 colspan=3 bgcolor=red>0</TD>";
		} else {
		  if (($test < $test2) || ($test > $test3)) {
			  echo "<TD class=fTable_4_2 colspan=3 bgcolor=red>" . $test . "</TD>";
			} else {
			  echo "<TD class=fTable_4_2 colspan=3 bgcolor=silver>" . $test . "</TD>";
			}
		}
		
?>
  </TR>
  <TR height=10>
    <TD class=fTable_4_3 width=33% style="border-left:1.0pt solid black;"><?php $test = @$farray9["INVMIN_BTOP"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVMIN_BTOP"][0]; } ?></TD>
    <TD class=fTable_4_3 width=33%><?php $test = @$farray9["INVSTD_BTOP"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVSTD_BTOP"][0]; } ?></TD>
    <TD class=fTable_4_3><?php $test = @$farray9["INVMAX_BTOP"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVMAX_BTOP"][0]; } ?></TD>
  </TR>
</TABLE>


<?php
$PA_L = 935 * $x;
$PA_T = 580 * $x;
$PA_H = 100 * $x;
$PA_W = 140 * $x;
$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px; \" cellpadding=\"0\" cellspacing=\"0\">";
  echo "<TR height=".$PA_height.">";
?>
    <TD class=fTable_3_1 colspan=3>EDINSP-PFC</TD>
  </TR>
  <TR height=10>
<?php
        $test = @$farray8["INVCUR_PAINT"][0];
		$test2 = @$farray9["COLMIN_PAINT"][0];
		$test3 = @$farray9["COLMAX_PAINT"][0];
		if ($test2 == "") {
		  $test2 = 0;
		}
		if ($test3 == "") {
		  $test3 = 0;
		}
    if ($test == "") {
  		echo "<TD class=fTable_3_2 colspan=3 bgcolor=red>0</TD>";
		} else {
		  if (($test < $test2) || ($test > $test3)) {
			  echo "<TD class=fTable_3_2 colspan=3 bgcolor=red>" . $farray8["INVCUR_PAINT"][0] . "</TD>";
			} else {
			  echo "<TD class=fTable_3_2 colspan=3 bgcolor=silver>" . $farray8["INVCUR_PAINT"][0] . "</TD>";
			}
		}
		
?>
  </TR>
  <TR height=10>
    <TD class=fTable_3_3 width=33% style="border-left:2.0pt solid white;"><?php $test = @$farray9["INVMIN_PAINT"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVMIN_PAINT"][0]; } ?></TD>
    <TD class=fTable_3_3 width=33%><?php $test = @$farray9["INVSTD_PAINT"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVSTD_PAINT"][0]; } ?></TD>
    <TD class=fTable_3_3><?php $test = @$farray9["INVMAX_PAINT"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVMAX_PAINT"][0]; } ?></TD>
  </TR>
</TABLE>
<!-- PAINT (Final) -->
<?php
$PA_L = 30 * $x;
$PA_T = 580 * $x;
$PA_H = 145 * $x;
$PA_W = 450 * $x;
$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px; border:solid Black 1px;\" cellpadding=\"0\" cellspacing=\"0\">";
  echo "<TR height=".$PA_height.">";
?>
    <TD class=fTable_1_1_h colspan=3>PAINT (G3)</TD>
  </TR>
  <TR height=10>
    <TD class=fTable_1_2_h width=33% style="border-left:none;">Capacity</TD>
    <TD class=fTable_1_2_h width=33%>Plan</TD>
    <TD class=fTable_1_2_h>Actual</TD>
  </TR>
  <TR>
    <TD class=fTable_1_3_h style="border-left:none;" bgcolor="#CCFFCC"><?php $test = number_format($fWorkTimeP/$PAINT_TT*60,0); if ($test == "") { echo 0;} else {echo $test; } ?></TD>
    <TD class=fTable_1_3_h bgcolor="#CCFFCC"><?php $test = $fPlanP; if ($test == "") { echo 0;} else {echo /*($WT_G3 == $fDef) ? $fPlanP : $WT_G3*/$fPlanP ; } ?></TD>    	
    <TD class=fTable_1_3_h bgcolor="#CCFFCC"><?php $test = @$farray8["ACTPRD_G3"][0]; if ($test == "") { echo 0;} else {echo $farray8["ACTPRD_G3"][0]; } ?></TD>
    	
<?php
/*    $test = @$farray8["ACTPRD_PF"][0];
	$test2 = @$farray4["CUREXPRD_PF"][0];
	if (($test == "") && ($test2 == "")) {
      echo "<TD class=fTable_1_3_h bgcolor=red>0</TD>";
    }else {
		if ($test == "") {
			  echo "<TD class=fTable_1_3_h bgcolor=red>0</TD>";
		} else {
			if ($test < $test2) {
	  		  echo "<TD class=fTable_1_3_h bgcolor='#CCFFCC'>" . $farray8["ACTPRD_PF"][0] . "</TD>";
//  			  echo "<TD class=fTable_1_3 bgcolor=red>" . $farray8["ACTPRD_PF"][0] . "</TD>";
  			} else {
	  		  echo "<TD class=fTable_1_3_h bgcolor='#CCFFCC'>" . $farray8["ACTPRD_PF"][0] . "</TD>";
		  	}
		}
	}*/
?>	
  </TR>
</TABLE>

<?php
//T/C DRR
$PA_L = 500 * $x;
$PA_T = 935 * $x;
$PA_H = 60 * $x;
$PA_W = 110 * $x;
$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px; border:solid Black 1px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>    
  <TR height=10>
    <TD colspan=1 class=fTable_1_1_h style="font-size:9;border-bottom:0pt solid black;">T/C DRR (%)</TD>
  </TR>
  <TR>
<?php 
    $test = "RUNNING";//@$farray4["LINESTS_WAX"][0];
	$test2 = number_format(($farray4["DRR_TCINSP"][0] / 10),1);
  	switch($test) {
		  case "RUNNING": 
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_1_3_t style='border-left:none;' bgcolor='#CCFFCC'>0</TD>";
				} else {
		      echo "<TD class=fTable_1_3_t style='border-left:none;' bgcolor='#CCFFCC'>" . $test2 . "</TD>";				
				}
			  break;
		  case "ERRSTOP": 
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_1_3_t style='border-left:none;' bgcolor=red>0</TD>";
				} else {
		      echo "<TD class=fTable_1_3_t style='border-left:none;' bgcolor=red>" . $test2 . "</TD>";				
				}
			  break;				
  		default: 
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_1_3_t style='border-left:none;' bgcolor='#CCFFCC'>0</TD>";
				} else {
		      echo "<TD class=fTable_1_3_t style='border-left:none;' bgcolor='#CCFFCC'>" . $test2 . "</TD>";				
				}
			  break;	
		}
?>    
  </TR>
</TABLE>

<?php
//FINAL DRR
$PA_L = 500 * $x;
$PA_T = 998 * $x;
$PA_H = 60 * $x;
$PA_W = 110 * $x;
$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px; border:solid Black 1px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>    
  <TR height=10>
    <TD colspan=1 class=fTable_1_1_h style="font-size:9;border-bottom:0pt solid black;">FINAL DRR(%)</TD>
  </TR>
  <TR>
<?php 
    $test = "RUNNING";//@$farray4["LINESTS_WAX"][0];
	$test2 = number_format(($farray4["DRR_FINSP"][0] / 10),1);
  	switch($test) {
		  case "RUNNING": 
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_1_3_t style='border-left:none;' bgcolor='#CCFFCC'>0</TD>";
				} else {
		      echo "<TD class=fTable_1_3_t style='border-left:none;' bgcolor='#CCFFCC'>" . $test2 . "</TD>";				
				}
			  break;
		  case "ERRSTOP": 
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_1_3_t style='border-left:none;' bgcolor=red>0</TD>";
				} else {
		      echo "<TD class=fTable_1_3_t style='border-left:none;' bgcolor=red>" . $test2 . "</TD>";				
				}
			  break;				
  		default: 
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_1_3_t style='border-left:none;' bgcolor='#CCFFCC'>0</TD>";
				} else {
		      echo "<TD class=fTable_1_3_t style='border-left:none;' bgcolor='#CCFFCC'>" . $test2 . "</TD>";				
				}
			  break;	
		}
?>    
  </TR>
</TABLE>

<?php
// TC new
$BO_L = 30 * $x;
$BO_T = 868 * $x;
$BO_H = 70 * $x;//100
$BO_W = 65 * $x;//110
$BO_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$BO_L}px; top: {$BO_T}px; height: {$BO_H}px; width: {$BO_W}px; \" cellpadding=\"0\" cellspacing=\"0\">";
  echo "<TR height=".$BO_height.">";
?>
    <TD class=fTable_7_1 colspan=3>T/C</TD>
  </TR>
  <TR height=10>
<?php
		$test = @$farray4["TC_TOTAL"][0];
		$test2 = @$farray9["COLMIN_TOPCT"][0];
		$test3 = @$farray9["COLMAX_TOPCT"][0];
		if ($test2 == "") {
		  $test2 = 0;
		}
		if ($test3 == "") {
		  $test3 = 0;
		}
    if ($test == "") {
  		echo "<TD class=fTable_7_2 colspan=3 bgcolor=red>0</TD>";
    } else {
		  if (($test < $test2) || ($test > $test3)) {
			  echo "<TD class=fTable_7_2 colspan=3 bgcolor=red>" . $test . "</TD>";
          } else {
			  echo "<TD class=fTable_7_2 colspan=3 bgcolor=silver>" . $test . "</TD>";
          }
		}
		
?>
  </TR>
  <TR height=10>
    <TD class=fTable_7_3 width=33% style="border-left:1.0pt solid black;"><?php $test = @$farray9["INVMIN_TOPCT"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVMIN_TOPCT"][0]; } ?></TD>
    <TD class=fTable_7_3 width=33%><?php $test = @$farray9["INVSTD_TOPCT"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVSTD_TOPCT"][0]; } ?></TD>
    <TD class=fTable_7_3><?php $test = @$farray9["INVMAX_TOPCT"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVMAX_TOPCT"][0]; } ?></TD>
  </TR>
</TABLE>


<?php
// CSS new
$BO_L = 245 * $x;
$BO_T = 770 * $x;
$BO_H = 70 * $x;//100
$BO_W = 65 * $x;//110
$BO_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$BO_L}px; top: {$BO_T}px; height: {$BO_H}px; width: {$BO_W}px; \" cellpadding=\"0\" cellspacing=\"0\">";
  echo "<TR height=".$BO_height.">";
?>
    <TD class=fTable_7_1 colspan=3>CSS</TD>
  </TR>
  <TR height=10>
<?php
		$test = @$farray4["CSS"][0];
		$test2 = @$farray9["COLMIN_CSS"][0];
		$test3 = @$farray9["COLMAX_CSS"][0];
		if ($test2 == "") {
		  $test2 = 0;
		}
		if ($test3 == "") {
		  $test3 = 0;
		}
    if ($test == "") {
  		echo "<TD class=fTable_7_2 colspan=3 bgcolor=red>0</TD>";
		} else {
		  if (($test < $test2) || ($test > $test3)) {
			  echo "<TD class=fTable_7_2 colspan=3 bgcolor=red>" . $test . "</TD>";
			} else {
			  echo "<TD class=fTable_7_2 colspan=3 bgcolor=silver>" . $test . "</TD>";
			}
		}
		
?>
  </TR>
  <TR height=10>
    <TD class=fTable_7_3 width=33% style="border-left:1.0pt solid black;"><?php $test = @$farray9["INVMIN_CSS"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVMIN_CSS"][0]; } ?></TD>
    <TD class=fTable_7_3 width=33%><?php $test = @$farray9["INVSTD_CSS"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVSTD_CSS"][0]; } ?></TD>
    <TD class=fTable_7_3><?php $test = @$farray9["INVMAX_CSS"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVMAX_CSS"][0]; } ?></TD>
  </TR>
</TABLE>


<?php
// Prm new
$BO_L = 315 * $x;
$BO_T = 770 * $x;
$BO_H = 70 * $x;//100
$BO_W = 65 * $x;//110
$BO_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$BO_L}px; top: {$BO_T}px; height: {$BO_H}px; width: {$BO_W}px; \" cellpadding=\"0\" cellspacing=\"0\">";
  echo "<TR height=".$BO_height.">";
?>
    <TD class=fTable_7_1 colspan=3>Prm</TD>
  </TR>
  <TR height=10>
<?php
		$test = @$farray4["PRM_TOTAL"][0];
		$test2 = @$farray9["COLMIN_PRIMR"][0];
		$test3 = @$farray9["COLMAX_PRIMR"][0];
		if ($test2 == "") {
		  $test2 = 0;
		}
		if ($test3 == "") {
		  $test3 = 0;
		}
    if ($test == "") {
  		echo "<TD class=fTable_7_2 colspan=3 bgcolor=red>0</TD>";
		} else {
		  if (($test < $test2) || ($test > $test3)) {
			  echo "<TD class=fTable_7_2 colspan=3 bgcolor=red>" . $test . "</TD>";
			} else {
			  echo "<TD class=fTable_7_2 colspan=3 bgcolor=silver>" . $test . "</TD>";
			}
		}
		
?>
  </TR>
  <TR height=10>
    <TD class=fTable_7_3 width=33% style="border-left:1.0pt solid black;"><?php $test = @$farray9["INVMIN_PRIMR"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVMIN_PRIMR"][0]; } ?></TD>
    <TD class=fTable_7_3 width=33%><?php $test = @$farray9["INVSTD_PRIMR"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVSTD_PRIMR"][0]; } ?></TD>
    <TD class=fTable_7_3><?php $test = @$farray9["INVMAX_PRIMR"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVMAX_PRIMR"][0]; } ?></TD>
  </TR>
</TABLE>


<?php
// BB new
$BO_L = 595 * $x;
$BO_T = 770 * $x;
$BO_H = 70 * $x;//100
$BO_W = 65 * $x;//110
$BO_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$BO_L}px; top: {$BO_T}px; height: {$BO_H}px; width: {$BO_W}px; \" cellpadding=\"0\" cellspacing=\"0\">";
  echo "<TR height=".$BO_height.">";
?>
    <TD class=fTable_7_1 colspan=3>BB</TD>
  </TR>
  <TR height=10>
<?php
		$test = @$farray4["PRM_BB"][0];
		$test2 = @$farray9["COLMIN_BB"][0];
		$test3 = @$farray9["COLMAX_BB"][0];
		if ($test2 == "") {
		  $test2 = 0;
		}
		if ($test3 == "") {
		  $test3 = 0;
		}
    if ($test == "") {
  		echo "<TD class=fTable_7_2 colspan=3 bgcolor=red>0</TD>";
		} else {
		  if (($test < $test2) || ($test > $test3)) {
			  echo "<TD class=fTable_7_2 colspan=3 bgcolor=red>" . $test . "</TD>";
			} else {
			  echo "<TD class=fTable_7_2 colspan=3 bgcolor=silver>" . $test . "</TD>";
			}
		}
		
?>
  </TR>
  <TR height=10>
    <TD class=fTable_7_3 width=33% style="border-left:1.0pt solid black;"><?php $test = @$farray9["INVMIN_BB"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVMIN_BB"][0]; } ?></TD>
    <TD class=fTable_7_3 width=33%><?php $test = @$farray9["INVSTD_BB"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVSTD_BB"][0]; } ?></TD>
    <TD class=fTable_7_3><?php $test = @$farray9["INVMAX_BB"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVMAX_BB"][0]; } ?></TD>
  </TR>
</TABLE>


<?php
// PFC new
$BO_L = 920 * $x;
$BO_T = 868 * $x;
$BO_H = 70 * $x;//100
$BO_W = 65 * $x;//110
$BO_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$BO_L}px; top: {$BO_T}px; height: {$BO_H}px; width: {$BO_W}px; \" cellpadding=\"0\" cellspacing=\"0\">";
  echo "<TR height=".$BO_height.">";
?>
    <TD class=fTable_7_1 colspan=3>PFC</TD>
  </TR>
  <TR height=10>
<?php
		$test = $farray8["INVCUR_SELEC"][0];
		$test2 = @$farray9["COLMIN_SELEC"][0];
		$test3 = @$farray9["COLMAX_SELEC"][0];
		if ($test2 == "") {
		  $test2 = 0;
		}
		if ($test3 == "") {
		  $test3 = 0;
		}
    if ($test == "") {
  		echo "<TD class=fTable_7_2 colspan=3 bgcolor=red>0</TD>";
		} else {
		  if (($test < $test2) || ($test > $test3)) {
			  echo "<TD class=fTable_7_2 colspan=3 bgcolor=red>" . $test . "</TD>";
			} else {
			  echo "<TD class=fTable_7_2 colspan=3 bgcolor=silver>" . $test . "</TD>";
			}
		}
		
?>
  </TR>
  <TR height=10>
    <TD class=fTable_7_3 width=33% style="border-left:1.0pt solid black;"><?php $test = @$farray9["INVMIN_SELEC"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVMIN_SELEC"][0]; } ?></TD>
    <TD class=fTable_7_3 width=33%><?php $test = @$farray9["INVSTD_SELEC"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVSTD_SELEC"][0]; } ?></TD>
    <TD class=fTable_7_3><?php $test = @$farray9["INVMAX_SELEC"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVMAX_SELEC"][0]; } ?></TD>
  </TR>
</TABLE>



<?php
//EMPTY
$PA_L = 485 * $x;
$PA_T = 580 * $x;
$PA_H = 70 * $x;
$PA_W = 70 * $x;
$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px; border:solid Black 1px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>    
  <TR height=10>
    <TD colspan=1 class=fTable_1_1_h style="font-size:9;border-bottom:0pt solid black;">EMPTY</TD>
  </TR>
  <TR>
<?php 
    $test = "RUNNING";//@$farray4["LINESTS_WAX"][0];
	$test2 = $farray4["INVCUR_ESC02"][0];
  	switch($test) {
		  case "RUNNING": 
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_1_3_t style='border-left:none;' bgcolor=silver>0</TD>";
				} else {
		      echo "<TD class=fTable_1_3_t style='border-left:none;' bgcolor=silver>" . $test2 . "</TD>";				
				}
			  break;
		  case "ERRSTOP": 
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_1_3_t style='border-left:none;' bgcolor=red>0</TD>";
				} else {
		      echo "<TD class=fTable_1_3_t style='border-left:none;' bgcolor=red>" . $test2 . "</TD>";				
				}
			  break;				
  		default: 
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_1_3_t style='border-left:none;' bgcolor=silver>0</TD>";
				} else {
		      echo "<TD class=fTable_1_3_t style='border-left:none;' bgcolor=silver>" . $test2 . "</TD>";				
				}
			  break;	
		}
?>    
  </TR>
</TABLE>

<?php
//PT/ED
$PA_L = 485 * $x;
$PA_T = 650 * $x;
$PA_H = 70 * $x;
$PA_W = 70 * $x;
$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px; border:solid Black 1px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>    
  <TR height=10>
    <TD colspan=1 class=fTable_1_1_h style="font-size:9;border-bottom:0pt solid black;">PT/ED</TD>
  </TR>
  <TR>
<?php 
    $test = "RUNNING";//@$farray4["LINESTS_WAX"][0];
	//$test2 = @$farray4["STPTIME_WAX"][0];
	$test2 = $farray4["INVCUR_PTED"][0];
  	switch($test) {
		  case "RUNNING": 
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_1_3_t style='border-left:none;' bgcolor=silver>0</TD>";
				} else {
		      echo "<TD class=fTable_1_3_t style='border-left:none;' bgcolor=silver>" . $test2 . "</TD>";				
				}
			  break;
		  case "ERRSTOP": 
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_1_3_t style='border-left:none;' bgcolor=red>0</TD>";
				} else {
		      echo "<TD class=fTable_1_3_t style='border-left:none;' bgcolor=red>" . $test2 . "</TD>";				
				}
			  break;				
  		default: 
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_1_3_t style='border-left:none;' bgcolor=silver>0</TD>";
				} else {
		      echo "<TD class=fTable_1_3_t style='border-left:none;' bgcolor=silver>" . $test2 . "</TD>";				
				}
			  break;	
		}
?>    
  </TR>
</TABLE>

<?php
//PT/ED
$PA_L = 485 * $x;
$PA_T = 722 * $x;
$PA_H = 45 * $x;
$PA_W = 70 * $x;
$PA_height = 8 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px; border:solid Black 1px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>    
  <TR height=10>
    <TD colspan=1 class=fTable_1_1_h style="font-size:9;border-bottom:0pt solid black;">Skid-A</TD>
  </TR>
  <TR>
<?php 
    $test = "RUNNING";//@$farray4["LINESTS_WAX"][0];
	//$test2 = @$farray4["STPTIME_WAX"][0];
	$test2 = ($farray4["SKID_INV"][0]+$farray4["WT"][0]+$farray8["INVCUR_PAINT"][0]);
  	//$test2 = $farray4["SKID_INV"][0]."+".$farray4["WT"][0]."+".$farray8["INVCUR_PAINT"][0]."=".($farray4["SKID_INV"][0]+$farray4["WT"][0]+$farray8["INVCUR_PAINT"][0]);
  	switch($test) {
		  case "RUNNING": 
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_1_3_t style='border-left:none;' bgcolor=silver>0</TD>";
				} else {
		      echo "<TD class=fTable_1_3_t style='font-size:15; border-left:none;' bgcolor=silver>" . $test2 . "</TD>";				
				}
			  break;
		}
?>    
  </TR>
</TABLE>
<?php
//wax
$PA_L = 615 * $x;
$PA_T = 950 * $x;
$PA_H = 60 * $x;
$PA_W = 466 * $x;
$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px; border:solid Black 1px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>    
  <TR height=20>
    <TD class=fTable_1_1_h style="font-size:12;width:20%;">PROCESS</TD>
    <TD class=fTable_1_1_h style="font-size:12;border-left:1pt solid black;width:20%">ED Insp.</TD>
    <TD class=fTable_1_1_h style="font-size:12;border-left:1pt solid black;width:20%">ILM</TD>
    <TD class=fTable_1_1_h style="font-size:12;border-left:1pt solid black;width:20%">T/C Insp.</TD>
    <TD class=fTable_1_1_h style="font-size:12;border-left:1pt solid black;width:20%">Wax (G3)</TD>
  </TR>
  <TR height=30>
    <TD class=fTable_1_2_t style="font-size:14;border-left:none;border-top:1pt solid black;">ACTUAL</TD>
	<TD class=fTable_1_3_t2 bgcolor="#CCFFCC"><?php echo $farray4["ACTPRD_EDINSP"][0]; ?></TD>
	<TD class=fTable_1_3_t2 bgcolor="#CCFFCC"><?php echo $farray4["ACTPRD_PRINSP"][0]; ?></TD>
	<TD class=fTable_1_3_t2 bgcolor="#CCFFCC"><?php echo $farray4["ACTPRD_TCINSP"][0]; ?></TD>
	<TD class=fTable_1_3_t2 bgcolor="#CCFFCC"><?php echo $farray8["ACTPRD_G3"][0]; ?></TD>
  </TR>

  <TR height=30>
    <TD class=fTable_1_2_t style="font-size:14;border-left:none;border-top:1pt solid black;">L/A</TD>
	<TD class=fTable_1_3_t2 bgcolor="#CCFFCC">
			<?php 
						$test = number_format((($fWorkTimeP - ($farray4["STPTIMF_EDINSP"][0] + $farray4["STPTIMO_EDINSP"][0] + $farray4["STPTIMS_EDINSP"][0]))/$fWorkTimeP)*100,1); 
						if ($test == "") { echo 0;} else {echo $test;}
						//if ($test == "") { echo 0;} else {echo $farray4["STPTIMS_EDINSP"][0];}
		  ?>
  </TD>
	<TD class=fTable_1_3_t2 bgcolor="#CCFFCC">
			<?php 
						$test = number_format((($fWorkTimeP - ($farray4["STPTIMF_PRINSP"][0] + $farray4["STPTIMO_PRINSP"][0] + $farray4["STPTIMS_PRINSP"][0]))/$fWorkTimeP)*100,1); 
						if ($test == "") { echo 0;} else {echo $test; } 
			?>
	</TD>
	<TD class=fTable_1_3_t2 bgcolor="#CCFFCC">
			<?php 
						$test = number_format((($fWorkTimeP - ($farray4["STPTIMF_TCINSP"][0] + $farray4["STPTIMO_TCINSP"][0] + $farray4["STPTIMS_TCINSP"][0]))/$fWorkTimeP)*100,1); 
						if ($test == "") { echo 0;} else {echo $test; } ?>
	</TD>
	<TD class=fTable_1_3_t2 bgcolor="#CCFFCC">
			<?php 
						$test = number_format((($fWorkTimeP - ($farray4["STPTIMF_WAX"][0] + $farray4["STPTIMO_WAX"][0] + $farray4["STPTIMS_WAX"][0]))/$fWorkTimeP)*100,1); 
						if ($test == "") { echo 0;} else {echo $test; } 
			?>
	</TD>      
  </TR>


  <TR height=30>
    <TD class=fTable_1_2_t style="font-size:14;border-left:none;border-top:1pt solid black;"><TABLE><TR><TD class=fTable_1_2_t style="font-size:9;border-left:none;">STOCK</TD></TR><TR><TD class=fTable_1_2_t style="font-size:9;border-left:none;">BUFFER</TD></TR></TABLE></TD>
<!--<?php 
    $test = @$farray4["LINESTS_WAX"][0];
	$test2 = @$farray4["STPTIME_WAX"][0];
  	switch($test) {
		  case "RUNNING": 
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_1_3_t2 bgcolor='#CCFFCC'>0</TD>";
				} else {
		      echo "<TD class=fTable_1_3_t2 bgcolor='#CCFFCC'>" . $farray4["STPTIME_WAX"][0] . "</TD>";				
				}
			  break;
		  case "ERRSTOP": 
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_1_3_t2 bgcolor=red>0</TD>";
				} else {
		      echo "<TD class=fTable_1_3_t2 bgcolor=red>" . $farray4["STPTIME_WAX"][0] . "</TD>";				
				}
			  break;				
  		default: 
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_1_3_t2 bgcolor='#CCFFCC'>0</TD>";
				} else {
		      echo "<TD class=fTable_1_3_t2 bgcolor='#CCFFCC'>" . $farray4["STPTIME_WAX"][0] . "</TD>";				
				}
			  break;	
		}

?>    -->
	<TD class=fTable_1_3_t2 bgcolor="#CCFFCC"><?php echo $farray4["DIFF_PTPVC"][0]; ?></TD>
	<TD class=fTable_1_3_t2 bgcolor="#CCFFCC"><?php echo $farray4["DIFF_PC"][0]; ?></TD>
	<TD class=fTable_1_3_t2 bgcolor="#CCFFCC"><?php echo $farray4["DIFF_TC"][0]; ?></TD>
	<TD class=fTable_1_3_t2 bgcolor="#CCFFCC"><?php echo $farray4["DIFF_FINWAX"][0]; ?></TD>
  </TR>
</TABLE>



	  
<?php
//overtime - paint
$PA_L = 515 * $x;
$PA_T = 1065 * $x;
$PA_H = 70 * $x;
$PA_W = 75 * $x;
$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px; border:solid Black 1px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR height=10>
    <TD class=fTable_1_2_t style="width:100px;border-left:0pt;">OT(min)</TD>
  </TR>
  <TR>
    <TD class=fTable_1_3_t bgcolor="#CCFFCC" style="width:100px;border-left:0pt;"><?php $test = @$farray4["OVERTIME_PNT"][0]; if ($test == "") { echo 0;} else {echo $farray4["OVERTIME_PNT"][0]; } ?></TD>
  </TR>
</TABLE>


<!-- 27 sedy vlevo dole -->



<?php
//to TCI
$PA_L = 146 * $x;
$PA_T = 931 * $x;
$PA_H = 40 * $x;
$PA_W = 50 * $x;
$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px; z-index: 2; border:solid Black 1px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
<TR height=5>
    <TD colspan=1 class=fTable_1_1_h style="font-size:7;border-bottom:0pt solid black;">to TCI</TD>
</TR>
<TR>
    <?php
    $test = "RUNNING";//@$farray4["LINESTS_WAX"][0];
    //$test2 = @$farray4["STPTIME_WAX"][0];
    $test2 = $farray4["TC_TOTAL"][0]-$farray4["TC_TO_ILM"][0];
    switch($test) {
        case "RUNNING":
            if ($test2 == "")	{
                echo "<TD class=fTable_1_3_t style='border-left:none; font-size:11;' bgcolor=silver>0</TD>";
            } else {
                echo "<TD class=fTable_1_3_t style='border-left:none;font-size:11;' bgcolor=silver>" . $test2 . "</TD>";
            }
            break;
        case "ERRSTOP":
            if ($test2 == "")	{
                echo "<TD class=fTable_1_3_t style='border-left:none;font-size:11;' bgcolor=red>0</TD>";
            } else {
                echo "<TD class=fTable_1_3_t style='border-left:none;font-size:11;' bgcolor=red>" . $test2 . "</TD>";
            }
            break;
        default:
            if ($test2 == "")	{
                echo "<TD class=fTable_1_3_t style='border-left:none;font-size:11;' bgcolor=silver>0</TD>";
            } else {
                echo "<TD class=fTable_1_3_t style='border-left:none;font-size:11;' bgcolor=silver>" . $test2 . "</TD>";
            }
            break;
    }
    ?>
</TR>
</TABLE>


<?php
//to ILM
$PA_L = 246 * $x;
$PA_T = 931 * $x;
$PA_H = 40 * $x;
$PA_W = 50 * $x;
$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px; z-index: 2; border:solid Black 1px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
<TR height=5>
    <TD colspan=1 class=fTable_1_1_h style="font-size:7;border-bottom:0pt solid black;">to ILM</TD>
</TR>
<TR>
    <?php
    $test = "RUNNING";//@$farray4["LINESTS_WAX"][0];
    //$test2 = @$farray4["STPTIME_WAX"][0];
    $test2 = ($farray8["INVCUR_F8"][0]+$farray8["INVCUR_F9"][0]-$farray8["INVCUR_Z8"][0]-$farray8["V3"][0]);
    switch($test) {
        case "RUNNING":
            if ($test2 == "")	{
                echo "<TD class=fTable_1_3_t style='border-left:none; font-size:11;' bgcolor=silver>0</TD>";
            } else {
                echo "<TD class=fTable_1_3_t style='border-left:none;font-size:11;' bgcolor=silver>" . $test2 . "</TD>";
            }
            break;
        case "ERRSTOP":
            if ($test2 == "")	{
                echo "<TD class=fTable_1_3_t style='border-left:none;font-size:11;' bgcolor=red>0</TD>";
            } else {
                echo "<TD class=fTable_1_3_t style='border-left:none;font-size:11;' bgcolor=red>" . $test2 . "</TD>";
            }
            break;
        default:
            if ($test2 == "")	{
                echo "<TD class=fTable_1_3_t style='border-left:none;font-size:11;' bgcolor=silver>0</TD>";
            } else {
                echo "<TD class=fTable_1_3_t style='border-left:none;font-size:11;' bgcolor=silver>" . $test2 . "</TD>";
            }
            break;
    }
    ?>
</TR>
</TABLE>



<?php
$AS_L = 406 * $x;
$AS_T = 931 * $x;
$AS_H = 70 * $x;
$AS_W = 80 * $x;
$AS_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$AS_L}px; top: {$AS_T}px; height: {$AS_H}px; width: {$AS_W}px; border:solid Black 1px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
<TR height=10>
    <TD class=fTable_1_2_t style="border-left:none;">G1B Ratio<br>TC off</TD>
</TR>

<TR>
    <?php
    $test = @$farray8["G1B_PNTRP_RATIO"][0];
    $testFmt = number_format($test, 2, '.', '');
    $test2 = @$farray8["G1B_RATIO_PFC_FNL"][0];
    $test2Fmt = number_format($test2, 2, '.', '');

    ?>
</TR>
<?php
echo "<TR><TD class=fTable_1_3_t style='border-left:none; font-size:10pt;'  bgcolor='#CCFFCC'>". $testFmt ."</TD></TR>";
echo "<TR><TD class=fTable_1_3_t style='border-left:none; font-size:7.6pt;'  bgcolor='#CCFFCC'>PFC FINAL</TD></TR>";
echo "<TR><TD class=fTable_1_3_t style='border-left:none; font-size:10pt;'  bgcolor='#CCFFCC'>". $test2Fmt ."</TD></TR>";
?>
</TABLE>


<!-- 22 ED repair-->
<?php
$PA_L = 20 * $x;
$PA_T = 975 * $x;
$PA_H = 70 * $x;
$PA_W = 90 * $x;
$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
    <TD class=fTable_5_1 colspan=2 style="">ED</TD>
  </TR>
  <TR bgcolor="#00CCFF">
<?php  
    $test = @$farray8["INVCUR_EDRP"][0];
   // $test = (@$farray8["INVCUR_EDRP"][0]+@$farray8["INVCUR_BEDREP"][0]);
  	$test2 = @$farray9["INVMAX_EDRP"][0];
  	$test3 = @$farray9["COLMAX_EDRP"][0];
		
	if ($test == "") { 
  		echo "<TD class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>0</TD>";
		if ($test2 == "") {
        	echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/0</BR></TD>";
		} else {
			echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/" . $farray9["INVMAX_EDRP"][0] . "</BR></TD>";
		}
	} else {
		if ($test2 == "") { 
			echo "<TD class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>" . ($farray8["INVCUR_EDRP"][0]+@$farray8["INVCUR_BEDREP"][0]) . "</TD>";
        	echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>0</BR></TD>";
		} else {
        	if ($test > $test3)	{		
  				echo "<TD class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>" . ($farray8["INVCUR_EDRP"][0]+@$farray8["INVCUR_BEDREP"][0]) . "</BR></TD>";
          		echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/" . $farray9["INVMAX_EDRP"][0] . "</BR></TD>";				
			} else {
				echo "<TD class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor='#00CCFF'>" . ($farray8["INVCUR_EDRP"][0]+@$farray8["INVCUR_BEDREP"][0]) . "</BR></TD>";
          		echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor='#00CCFF'>/" . $farray9["INVMAX_EDRP"][0] . "</BR></TD>";					
			}
		}		
	}
?>
  </TR>
</TABLE>

<!-- ?? BT >> -->
<?php
$PA_L = 120 * $x;
$PA_T = 975 * $x;
$PA_H = 70 * $x;
$PA_W = 90 * $x;
$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
    <TD class=fTable_5_1 colspan=2 style="">B/T</TD>
  </TR>
  <TR bgcolor="#00CCFF">
<?php  
    $test = @$farray8["INVCUR_Z9"][0];
  	$test2 = @$farray9["INVMAX_Z9"][0];
  	$test3 = @$farray9["COLMAX_Z9"][0];
		
	if ($test == "") { 
  	    echo "<TD class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>0</TD>";
		if ($test2 == "") {
            echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/0</BR></TD>";
		} else {
		    echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/" . $farray9["INVMAX_Z9"][0] . "</BR></TD>";
		}
	} else {
		if ($test2 == "") { 
			echo "<TD class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>" . $test . "</TD>";
        	echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>0</BR></TD>";
		} else {
        	if ($test > $test3)	{		
  				echo "<TD class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>" . $test . "</BR></TD>";
          		echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/" . $farray9["INVMAX_Z9"][0] . "</BR></TD>";
			} else {
				echo "<TD class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor='#00CCFF'>" . $test . "</BR></TD>";
          		echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor='#00CCFF'>/" . $farray9["INVMAX_Z9"][0] . "</BR></TD>";
			}
		}		
	}
?>
  </TR>
</TABLE>

<!-- 28 T/C_NG -->
<?php
$PA_L = 220 * $x;
$PA_T = 975 * $x;
$PA_H = 70 * $x;
$PA_W = 90 * $x;
$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
<TR>
    <TD class=fTable_5_1 colspan=2 style="">T/C</TD>
</TR>
<TR bgcolor="#00CCFF">
    <?php
    $test = @$farray4["TC_NG"][0] - $farray8["INVCUR_REPVEH"][0];
    $test2 = @$farray9["INVMAX_TCRP"][0];
    $test3 = @$farray9["COLMAX_TCRP"][0];
    $test_G1B = $farray8["INVCUR_TCRP_G1B"][0];

    if ($test == "") {
        echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>0</TD>";
        echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor=red>($test_G1B)</BR></TD></TR>";
        if ($test2 == "") {
            echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/0</BR></TD>";
        } else {
            echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/" . $farray9["INVMAX_TCRP"][0] . "</BR></TD>";
        }
    } else {
        if ($test2 == "") {
            echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>" . $test . "</TD>";
            echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor=red>($test_G1B)</BR></TD></TR>";
            echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>0</BR></TD>";
        } else {
            if ($test > $test3)	{
                echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>" . $test . "</BR></TD>";
                echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor=red>($test_G1B)</BR></TD></TR>";
                echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/" . $farray9["INVMAX_TCRP"][0] . "</BR></TD>";
            } else {
                echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor='#00CCFF'>" . $test . "</BR></TD>";
                echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor='#00CCFF'>($test_G1B)</BR></TD></TR>";
                echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor='#00CCFF'>/" . $farray9["INVMAX_TCRP"][0] . "</BR></TD>";
            }
        }
    }
    ?>
</TR>
</TABLE>

<!-- MASKING-->
<?php
$PA_L = 315 * $x;
$PA_T = 975 * $x;
$PA_H = 70 * $x;
$PA_W = 90 * $x;
$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
    <TD class=fTable_5_1 style="">MASKING</TD>
  </TR>
  <TR bgcolor="#00CCFF">
<?php  
	$test = $farray4["MASK"][0] ;
//  	$test2 = @$farray9["INVMAX_PFRP"][0];
//  	$test3 = @$farray9["COLMAX_PFRP"][0];
		
	if ($test == "") { 
			echo "<TD class=fTable_5_2 style='border-right:1.0pt solid white;' bgcolor=red>/" . $farray4["MASK"][0] . "</BR></TD>";
		
	} else {
				echo "<TD class=fTable_5_2 style='border-left:1.0pt solid white; border-right:1.0pt solid white; text-align:center;' bgcolor='#00CCFF'>" . $farray4["MASK"][0] . "</BR></TD>";
			}
?>	
  </TR>
</TABLE>


<!-- 25 primer repair-->
<?php
$PA_L = 20 * $x;
$PA_T = 1060 * $x;
$PA_H = 70 * $x;
$PA_W = 90 * $x;
$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
    <TD class=fTable_5_1 colspan=2 style="">Primer</TD>
  </TR>
  <TR bgcolor="#00CCFF">
<?php  
    $test = @$farray8["INVCUR_PRMRP"][0];
  	$test2 = @$farray9["INVMAX_PRMRP"][0];
  	$test3 = @$farray9["COLMAX_PRMRP"][0];
		
	if ($test == "") { 
  		echo "<TD class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>0</TD>";
		if ($test2 == "") {
        	echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/0</BR></TD>";
		} else {
			echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/" . $farray9["INVMAX_PRMRP"][0] . "</BR></TD>";
		}
	} else {
		if ($test2 == "") { 
			echo "<TD class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>" . $farray8["INVCUR_PRMRP"][0] . "</TD>";
        	echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>0</BR></TD>";
		} else {
        	if ($test > $test3)	{		
  				echo "<TD class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>" . $farray8["INVCUR_PRMRP"][0] . "</BR></TD>";
          		echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/" . $farray9["INVMAX_PRMRP"][0] . "</BR></TD>";				
			} else {
				echo "<TD class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor='#00CCFF'>" . $farray8["INVCUR_PRMRP"][0] . "</BR></TD>";
          		echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor='#00CCFF'>/" . $farray9["INVMAX_PRMRP"][0] . "</BR></TD>";					
			}
		}		
	}
?>
  </TR>
</TABLE>

<!-- ?? Restrict -->
<?php
$PA_L = 120 * $x;
$PA_T = 1060 * $x;
$PA_H = 70 * $x;
$PA_W = 90 * $x;
$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
    <TD class=fTable_5_1 colspan=2 style="">Restrict</TD>
  </TR>
  <TR bgcolor="#00CCFF">
<?php  
    $test = @$farray8["INVCUR_Z8"][0];
    $test2 = @$farray9["INVMAX_Z8"][0];
  	$test3 = @$farray9["COLMAX_Z8"][0];
		
	if (($test == "") && ($test <> 0) ) {
  		echo "<TD class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>0</TD>";
		if ($test2 == "") {
        	echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/0</BR></TD>";
		} else {
			echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>" . $farray9["INVMAX_Z8"][0] . "</BR></TD>";
		}
	} else {
		if ($test2 == "") { 
			echo "<TD class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>" . $test. "</TD>";
        	echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>0</BR></TD>";
		} else {
        	if ($test > $test3)	{		
  				echo "<TD class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>" . $test . "</BR></TD>";
          		echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/" . $farray9["INVMAX_Z8"][0] . "</BR></TD>";
			} else {
				echo "<TD class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor='#00CCFF'>" . $test . "</BR></TD>";
          		echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor='#00CCFF'>/" . $farray9["INVMAX_Z8"][0] . "</BR></TD>";
			}
		}		
	}
?>
  </TR>
</TABLE>


<!-- 31 final repair -->
<?php
$PA_L = 220 * $x;
$PA_T = 1060 * $x;
$PA_H = 70 * $x;
$PA_W = 90 * $x;
$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
<TR>
    <TD class=fTable_5_1 colspan=2 style="">Final</TD>
</TR>
<TR bgcolor="#00CCFF">
    <?php
    $finalV5 = ($farray8["INVCUR_PFRP"][0]-$farray4["OK_OFF"][0]);
    if ((0-$finalV5)>0){
        $test = $finalV5 + (0-$finalV5);
    } else {
        $test = $finalV5;
    }
    $test2 = @$farray9["INVMAX_PFRP"][0];
    $test3 = @$farray9["COLMAX_PFRP"][0];
    $test_G1B = $farray8["INVCUR_PFRP_G1B"][0];

    if (($test == "") && ($test <> 0) ) {
        echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>0</TD>";
        echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor=red>($test_G1B)</BR></TD></TR>";
        if ($test2 == "") {
            echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/0</BR></TD>";
        } else {
            echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>" . $farray9["INVMAX_PFRP"][0] . "</BR></TD>";
        }
    } else {
        if ($test2 == "") {
            echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>" . $test. "</TD>";
            echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor=red>($test_G1B)</BR></TD></TR>";
            echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>0</BR></TD>";
        } else {
            if ($test > $test3)	{
                echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>" . $test . "</BR></TD>";
                echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor=red>($test_G1B)</BR></TD></TR>";
                echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/" . $farray9["INVMAX_PFRP"][0] . "</BR></TD>";
            } else {
                echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor='#00CCFF'>" . $test . "</BR></TD>";
                echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor='#00CCFF'>($test_G1B)</BR></TD></TR>";
                echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor='#00CCFF'>/" . $farray9["INVMAX_PFRP"][0] . "</BR></TD>";
            }
        }
    }
    ?>
</TR>
</TABLE>

<!-- 21 sedy vpravo nahore -->
<!--<?php
$PA_L = 580 * $x;
$PA_T = 775 * $x;
$PA_H = 80 * $x;
$PA_W = 100 * $x;
$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px; \" cellpadding=\"0\" cellspacing=\"0\">";
  echo "<TR height=".$PA_height.">";

        $test = @$farray8["INVCUR_EDSLR"][0];
		$test2 = @$farray9["COLMIN_EDSLR"][0];
		$test3 = @$farray9["COLMAX_EDSLR"][0];
		if ($test2 == "") {
		  $test2 = 0;
		}
		if ($test3 == "") {
		  $test3 = 0;
		}
    if ($test == "") {
  		echo "<TD class=fTable_6_1 colspan=3 bgcolor=red>0</TD>";
		} else {
		  if (($test < $test2) || ($test > $test3)) {
			  echo "<TD class=fTable_6_1 colspan=3 bgcolor=red>" . $farray8["INVCUR_EDSLR"][0] . "</TD>";
			} else {
			  echo "<TD class=fTable_6_1 colspan=3 bgcolor=silver>" . $farray8["INVCUR_EDSLR"][0] . "</TD>";
			}
		}
		
?>
  </TR>
  <TR height=10>
    <TD class=fTable_6_2 width=33% style="border-left:1.0pt solid white;"><?php $test = @$farray9["INVMIN_EDSLR"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVMIN_EDSLR"][0]; } ?></TD>
    <TD class=fTable_6_2 width=33%><?php $test = @$farray9["INVSTD_EDSLR"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVSTD_EDSLR"][0]; } ?></TD>
    <TD class=fTable_6_2><?php $test = @$farray9["INVMAX_EDSLR"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVMAX_EDSLR"][0]; } ?></TD>
  </TR>
</TABLE> -->


<!-- TOP COAT A -->
<?php
$PA_L = 30 * $x;
$PA_T = 805 * $x;
$PA_H = 40 * $x;
$PA_W = 200 * $x;
//$PA_L = 30 * $x;
//$PA_T = 840 * $x;
//$PA_H = 40 * $x;
//$PA_W = 280 * $x;
$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
<?php

    $test = @$farray4["STPSTSF_TOPA"][0];
    switch($test) {
        case "ERRSTOP":
            echo "<TD class=fTable_2_2 bgcolor=yellow>Full</TD>";
            break;
        default:
            echo "<TD class=fTable_2_2 bgcolor=white>Full</TD>";
    }

	$test = @$farray4["STPSTSO_TOPA"][0];
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_2 bgcolor=yellow>Self</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_2 bgcolor=white>Self</TD>";
	}
    $test = @$farray4["STPSTSS_TOPA"][0];
    switch($test) {
        case "ERRSTOP":
            echo "<TD class=fTable_2_2 bgcolor=yellow>Short</TD>";
            break;
        default:
            echo "<TD class=fTable_2_2 bgcolor=white>Short</TD>";
    }
    $test = @$farray4["LINESTS_TOPA"][0];
    switch($test) {
        case "RUNNING":
            echo "<TD id=\"TC_A\" class=fTable_2_1 rowspan=2 bgcolor=00C131>TC A</TD>";
            $_SESSION["TC_A"] = MAX_TIME;
            break;
        case "PLNSTOP":
            echo "<TD id=\"TC_A\" class=fTable_2_1 rowspan=2 bgcolor=white>TC A</TD>";
            $_SESSION["TC_A"] = MAX_TIME;
            break;
        default:
            echo "<TD id=\"TC_A\" class=fTable_2_1 rowspan=2 bgcolor=red>TC A</TD>";
            if ($_SESSION["TC_A"] == MAX_TIME) $_SESSION["TC_A"] = time();
    }

	//---

?>
  </TR>
  <TR>
<?php
    $test = @$farray4["STPSTSF_TOPA"][0];
    $test2 = @$farray4["STPTIMF_TOPA"][0];
    switch($test) {
        case "ERRSTOP":
            if ($test2 == "")	{
                echo "<TD class=fTable_2_3 bgcolor=yellow>0</TD>";
            } else {
                echo "<TD class=fTable_2_3 bgcolor=yellow>" . $farray4["STPTIMF_TOPA"][0] . "</TD>";
            }
            break;
        default:
            if ($test2 == "")	{
                echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
            } else {
                echo "<TD class=fTable_2_3 bgcolor=white>" . $farray4["STPTIMF_TOPA"][0] . "</TD>";
            }
            break;
    }

    $test = @$farray4["STPSTSO_TOPA"][0]; 
	$test2 = @$farray4["STPTIMO_TOPA"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=yellow>" . $farray4["STPTIMO_TOPA"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=white>" . $farray4["STPTIMO_TOPA"][0] . "</TD>";
				}	
				break;
	}			
    



    $test = @$farray4["STPSTSS_TOPA"][0];
    $test2 = @$farray4["STPTIMS_TOPA"][0];
    switch($test) {
        case "ERRSTOP":
            if ($test2 == "")	{
                echo "<TD class=fTable_2_3 bgcolor=yellow>0</TD>";
            } else {
                echo "<TD class=fTable_2_3 bgcolor=yellow>" . $farray4["STPTIMS_TOPA"][0] . "</TD>";
            }
            break;
        default:
            if ($test2 == "")	{
                echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
            } else {
                echo "<TD class=fTable_2_3 bgcolor=white>" . $farray4["STPTIMS_TOPA"][0] . "</TD>";
            }
            break;
    }

?>
  </TR>
</TABLE>
<!-- TOP COAT B -->
<?php
$PA_L = 30 * $x;
$PA_T = 742 * $x;
$PA_H = 40 * $x;
$PA_W = 200 * $x;
//$PA_L = 30 * $x;
//$PA_T = 910 * $x;
//$PA_H = 40 * $x;
//$PA_W = 280 * $x;
$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
<?php

    $test = @$farray4["STPSTSF_TOPB"][0];
    switch($test) {
        case "ERRSTOP":
            echo "<TD class=fTable_2_2 bgcolor=yellow>Full</TD>";
            break;
        default:
            echo "<TD class=fTable_2_2 bgcolor=white>Full</TD>";
    }
	$test = @$farray4["STPSTSO_TOPB"][0]; 
  	switch($test) {
	  	case "ERRSTOP": 
        echo "<TD class=fTable_2_2 bgcolor=yellow>Self</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_2 bgcolor=white>Self</TD>";
	}
    $test = @$farray4["STPSTSS_TOPB"][0];
    switch($test) {
        case "ERRSTOP":
            echo "<TD class=fTable_2_2 bgcolor=yellow>Short</TD>";
            break;
        default:
            echo "<TD class=fTable_2_2 bgcolor=white>Short</TD>";
    }
    $test = @$farray4["LINESTS_TOPB"][0];
    switch($test) {
        case "RUNNING":
            echo "<TD id=\"TC_B\" class=fTable_2_1 rowspan=2 bgcolor=00C131>TC B</TD>";
            $_SESSION["TC_B"] = MAX_TIME;
            break;
        case "PLNSTOP":
            echo "<TD id=\"TC_B\" class=fTable_2_1 rowspan=2 bgcolor=white>TC B</TD>";
            $_SESSION["TC_B"] = MAX_TIME;
            break;
        default:
            echo "<TD id=\"TC_B\" class=fTable_2_1 rowspan=2 bgcolor=red>TC B</TD>";
            if ($_SESSION["TC_B"] == MAX_TIME) $_SESSION["TC_B"] = time();
    }
	//---

?>
  </TR>
  <TR>
<?php

    $test = @$farray4["STPSTSF_TOPB"][0];
    $test2 = @$farray4["STPTIMF_TOPB"][0];
    switch($test) {
        case "ERRSTOP":
            if ($test2 == "")	{
                echo "<TD class=fTable_2_3 bgcolor=yellow>0</TD>";
            } else {
                echo "<TD class=fTable_2_3 bgcolor=yellow>" . $farray4["STPTIMF_TOPB"][0] . "</TD>";
            }
            break;
        default:
            if ($test2 == "")	{
                echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
            } else {
                echo "<TD class=fTable_2_3 bgcolor=white>" . $farray4["STPTIMF_TOPB"][0] . "</TD>";
            }
            break;
    }
    $test = @$farray4["STPSTSO_TOPB"][0]; 
	$test2 = @$farray4["STPTIMO_TOPB"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=yellow>" . $farray4["STPTIMO_TOPB"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=white>" . $farray4["STPTIMO_TOPB"][0] . "</TD>";
				}	
				break;
	}			
    $test = @$farray4["STPSTSS_TOPB"][0];
    $test2 = @$farray4["STPTIMS_TOPB"][0];
    switch($test) {
        case "ERRSTOP":
            if ($test2 == "")	{
                echo "<TD class=fTable_2_3 bgcolor=yellow>0</TD>";
            } else {
                echo "<TD class=fTable_2_3 bgcolor=yellow>" . $farray4["STPTIMS_TOPB"][0] . "</TD>";
            }
            break;
        default:
            if ($test2 == "")	{
                echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
            } else {
                echo "<TD class=fTable_2_3 bgcolor=white>" . $farray4["STPTIMS_TOPB"][0] . "</TD>";
            }
            break;
    }

?>

  </TR>
</TABLE>

<!-- T/C INSP ZACATEK -->
<?php
$PA_L = 320 * $x;
$PA_T = 880 * $x;
$PA_H = 40 * $x;
$PA_W = 180 * $x;
$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
<?php
    $test = @$farray4["LINESTS_TCINSP"][0]; 
  	switch($test) {
		case "RUNNING": 
	   	  echo "<TD id=\"TCINSP\" class=fTable_2_1 rowspan=2 bgcolor=00C131>T/C</TD>";
	   	  $_SESSION["TCINSP"] = MAX_TIME;
	      break;
  		case "PLNSTOP": 
	  	  echo "<TD id=\"TCINSP\" class=fTable_2_1 rowspan=2 bgcolor=white>T/C</TD>";
	  	  $_SESSION["TCINSP"] = MAX_TIME;
 	      break;
  		default: 
	  	  echo "<TD id=\"TCINSP\" class=fTable_2_1 rowspan=2 bgcolor=red>T/C</TD>";
		  if ($_SESSION["TCINSP"] == MAX_TIME) $_SESSION["TCINSP"] = time();
	}

    $test = @$farray4["STPSTSS_TCINSP"][0]; 
  	switch($test) {
	  case "ERRSTOP": 
        echo "<TD class=fTable_2_2 bgcolor=yellow>Short</TD>";
		break;				
  	  default: 
        echo "<TD class=fTable_2_2 bgcolor=white>Short</TD>";
	  }
	  $test = @$farray4["STPSTSO_TCINSP"][0]; 
  	switch($test) {
	  case "ERRSTOP": 
        echo "<TD class=fTable_2_2 bgcolor=yellow>Self</TD>";
	    break;				
  	  default: 
        echo "<TD class=fTable_2_2 bgcolor=white>Self</TD>";
	  }
	  $test = @$farray4["STPSTSF_TCINSP"][0]; 
  	switch($test) {
	  case "ERRSTOP": 
        echo "<TD class=fTable_2_2 bgcolor=yellow>Full</TD>";
	    break;				
  	  default: 
        echo "<TD class=fTable_2_2 bgcolor=white>Full</TD>";
	  }
?>
  </TR>
  <TR>
<?php
    $test = @$farray4["STPSTSS_TCINSP"][0]; 
	$test2 = @$farray4["STPTIMS_TCINSP"][0];
  	switch($test) {
	    case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=yellow>" . $farray4["STPTIMS_TCINSP"][0] . "</TD>";
				}	
			    break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=white>" . $farray4["STPTIMS_TCINSP"][0] . "</TD>";
				}	
				break;
		}			

    $test = @$farray4["STPSTSO_TCINSP"][0]; 
	$test2 = @$farray4["STPTIMO_TCINSP"][0];
  	switch($test) {
	    case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=yellow>" . $farray4["STPTIMO_TCINSP"][0] . "</TD>";
				}	
			    break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=white>" . $farray4["STPTIMO_TCINSP"][0] . "</TD>";
				}	
				break;
		}			
    
		$test = @$farray4["STPSTSF_TCINSP"][0]; 
		$test2 = @$farray4["STPTIMF_TCINSP"][0];
  	switch($test) {
		case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=yellow>" . $farray4["STPTIMF_TCINSP"][0] . "</TD>";
				}	
			    break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=white>" . $farray4["STPTIMF_TCINSP"][0] . "</TD>";
				}	
				break;
		}						 	
?>
  </TR>
</TABLE>
<!-- T/C INSP KONEC -->

<?php
//TO F/I
$PA_L = 480 * $x;
$PA_T = 835 * $x;
$PA_H = 40 * $x;
$PA_W = 50 * $x;
$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px; border:solid Black 1px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
<TR height=5>
    <TD colspan=1 class=fTable_1_1_h style="font-size:7;border-bottom:0pt solid black;">TO F/I</TD>
</TR>
<TR>
    <?php
    $test = "RUNNING";//@$farray4["LINESTS_WAX"][0];
    //$test2 = @$farray4["STPTIME_WAX"][0];
    $test2 = $farray4["TC_TO_FI"][0];
    switch($test) {
        case "RUNNING":
            if ($test2 == "")	{
                echo "<TD class=fTable_1_3_t style='border-left:none; font-size:11;' bgcolor=silver>0</TD>";
            } else {
                echo "<TD class=fTable_1_3_t style='border-left:none;font-size:11;' bgcolor=silver>" . $test2 . "</TD>";
            }
            break;
        case "ERRSTOP":
            if ($test2 == "")	{
                echo "<TD class=fTable_1_3_t style='border-left:none;font-size:11;' bgcolor=red>0</TD>";
            } else {
                echo "<TD class=fTable_1_3_t style='border-left:none;font-size:11;' bgcolor=red>" . $test2 . "</TD>";
            }
            break;
        default:
            if ($test2 == "")	{
                echo "<TD class=fTable_1_3_t style='border-left:none;font-size:11;' bgcolor=silver>0</TD>";
            } else {
                echo "<TD class=fTable_1_3_t style='border-left:none;font-size:11;' bgcolor=silver>" . $test2 . "</TD>";
            }
            break;
    }
    ?>
</TR>
</TABLE>




<!-- FINAL -->
<?php
$PA_L = 505 * $x;
$PA_T = 880 * $x;
$PA_H = 40 * $x;
$PA_W = 180 * $x;
$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
<?php
    $test = @$farray4["LINESTS_PFIN"][0]; 
  	switch($test) {
		case "RUNNING": 
	   	  echo "<TD id=\"FINAL\" class=fTable_2_1 rowspan=2 bgcolor=00C131>FNL</TD>";
	   	  $_SESSION["FINAL"] = MAX_TIME;
	      break;
  		case "PLNSTOP": 
	  	  echo "<TD id=\"FINAL\" class=fTable_2_1 rowspan=2 bgcolor=white>FNL</TD>";
	  	  $_SESSION["FINAL"] = MAX_TIME;
 	      break;
  		default: 
	  	  echo "<TD id=\"FINAL\" class=fTable_2_1 rowspan=2 bgcolor=red>FNL</TD>";
		  if ($_SESSION["FINAL"] == MAX_TIME) $_SESSION["FINAL"] = time();
	}

    $test = @$farray4["STPSTSS_PFIN"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_2 bgcolor=yellow>Short</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_2 bgcolor=white>Short</TD>";
		}
		$test = @$farray4["STPSTSO_PFIN"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_2 bgcolor=yellow>Self</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_2 bgcolor=white>Self</TD>";
		}
		$test = @$farray4["STPSTSF_PFIN"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_2 bgcolor=yellow>Full</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_2 bgcolor=white>Full</TD>";
		}
?>
  </TR>
  <TR>
<?php
    $test = @$farray4["STPSTSS_PFIN"][0]; 
		$test2 = @$farray4["STPTIMS_PFIN"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=yellow>" . $farray4["STPTIMS_PFIN"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=white>" . $farray4["STPTIMS_PFIN"][0] . "</TD>";
				}	
				break;
		}			

    $test = @$farray4["STPSTSO_PFIN"][0]; 
		$test2 = @$farray4["STPTIMO_PFIN"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=yellow>" . $farray4["STPTIMO_PFIN"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=white>" . $farray4["STPTIMO_PFIN"][0] . "</TD>";
				}	
				break;
		}			
    
		$test = @$farray4["STPSTSF_PFIN"][0]; 
		$test2 = @$farray4["STPTIMF_PFIN"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=yellow>" . $farray4["STPTIMF_PFIN"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=white>" . $farray4["STPTIMF_PFIN"][0] . "</TD>";
				}	
				break;
		}						 	
?>

  </TR>
</TABLE>
<!-- PRIMER -->
<?php
$PA_L = 390 * $x;
$PA_T = 780 * $x;
$PA_H = 40 * $x;
$PA_W = 200 * $x;
$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
<?php
	$test = @$farray4["STPSTSF_PRIM"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_4 bgcolor=yellow>Full</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_4 bgcolor=white>Full</TD>";
	}
	$test = @$farray4["STPSTSO_PRIM"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_4 bgcolor=yellow>Self</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_4 bgcolor=white>Self</TD>";
	}
    $test = @$farray4["STPSTSS_PRIM"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_4 bgcolor=yellow>Short</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_4 bgcolor=white>Short</TD>";
	}
	//---
    $test = @$farray4["LINESTS_PRIM"][0]; 
  	switch($test) {
		case "RUNNING": 
	   	  echo "<TD id=\"PRIM\" class=fTable_2_1 rowspan=2 bgcolor=00C131>PRM</TD>";
	   	  $_SESSION["PRIM"] = MAX_TIME;
	      break;
  		case "PLNSTOP": 
	  	  echo "<TD id=\"PRIM\" class=fTable_2_1 rowspan=2 bgcolor=white>PRM</TD>";
	  	  $_SESSION["PRIM"] = MAX_TIME;
		  break;
  		default: 
	  	  echo "<TD id=\"PRIM\" class=fTable_2_1 rowspan=2 bgcolor=red>PRM</TD>";
		  if ($_SESSION["PRIM"] == MAX_TIME) $_SESSION["PRIM"] = time();
	}		
?>
  </TR>
  <TR>
<?php
	$test = @$farray4["STPSTSF_PRIM"][0]; 
	$test2 = @$farray4["STPTIMF_PRIM"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=yellow>" . $farray4["STPTIMF_PRIM"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=white>" . $farray4["STPTIMF_PRIM"][0] . "</TD>";
				}	
				break;
	}						 	
	
    $test = @$farray4["STPSTSO_PRIM"][0]; 
	$test2 = @$farray4["STPTIMO_PRIM"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=yellow>" . $farray4["STPTIMO_PRIM"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=white>" . $farray4["STPTIMO_PRIM"][0] . "</TD>";
				}	
				break;
	}			
    
    $test = @$farray4["STPSTSS_PRIM"][0]; 
	$test2 = @$farray4["STPTIMS_PRIM"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=yellow>" . $farray4["STPTIMS_PRIM"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=white>" . $farray4["STPTIMS_PRIM"][0] . "</TD>";
				}	
				break;
	}			
?>

  </TR>
</TABLE>
<!-- WAX -->
<?php
$PA_L = 690 * $x;
$PA_T = 880 * $x;
$PA_H = 40 * $x;
$PA_W = 180 * $x;
$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
<?php
    $test = @$farray4["LINESTS_WAX"][0]; 
  	switch($test) {
		case "RUNNING": 
	   	  echo "<TD id=\"WAX\" class=fTable_2_1 rowspan=2 bgcolor=00C131>WAX</TD>";
	   	  $_SESSION["WAX"] = MAX_TIME;
	      break;
  		case "PLNSTOP": 
	  	  echo "<TD id=\"WAX\" class=fTable_2_1 rowspan=2 bgcolor=white>WAX</TD>";
	  	  $_SESSION["WAX"] = MAX_TIME;
		  break;
  		default: 
	  	  echo "<TD id=\"WAX\" class=fTable_2_1 rowspan=2 bgcolor=red>WAX</TD>";
		  if ($_SESSION["WAX"] == MAX_TIME) $_SESSION["WAX"] = time();
	}

    $test = @$farray4["STPSTSS_WAX"][0]; 
  	switch($test) {
		case "ERRSTOP": 
        	echo "<TD class=fTable_2_2 bgcolor=yellow>Short</TD>";
			break;				
  		default: 
        	echo "<TD class=fTable_2_2 bgcolor=white>Short</TD>";
	}
	$test = @$farray4["STPSTSO_WAX"][0]; 
  	switch($test) {
		case "ERRSTOP": 
        	echo "<TD class=fTable_2_2 bgcolor=yellow>Self</TD>";
			 break;				
  		default: 
        	echo "<TD class=fTable_2_2 bgcolor=white>Self</TD>";
	}
	$test = @$farray4["STPSTSF_WAX"][0]; 
  	switch($test) {
		case "ERRSTOP": 
        	echo "<TD class=fTable_2_2 bgcolor=yellow>Full</TD>";
			break;				
  		default: 
        	echo "<TD class=fTable_2_2 bgcolor=white>Full</TD>";
	}
?>
  </TR>
  <TR>
<?php
    $test = @$farray4["STPSTSS_WAX"][0]; 
	$test2 = @$farray4["STPTIMS_WAX"][0];
  	switch($test) {
		case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=yellow>" . $farray4["STPTIMS_WAX"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=white>" . $farray4["STPTIMS_WAX"][0] . "</TD>";
				}	
				break;
		}			

    $test = @$farray4["STPSTSO_WAX"][0]; 
	$test2 = @$farray4["STPTIMO_WAX"][0];
  	switch($test) {
		case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=yellow>" . $farray4["STPTIMO_WAX"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=white>" . $farray4["STPTIMO_WAX"][0] . "</TD>";
				}	
				break;
		}			
    
	$test = @$farray4["STPSTSF_WAX"][0]; 
	$test2 = @$farray4["STPTIMF_WAX"][0];
  	switch($test) {
		case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=yellow>" . $farray4["STPTIMF_WAX"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=white>" . $farray4["STPTIMF_WAX"][0] . "</TD>";
				}	
				break;
		}						 	
?>

  </TR>
</TABLE>
<!-- SELECTIVITY PFC IN -->
<?php
$PA_L = 872 * $x;
$PA_T = 875 * $x;
$PA_H = 63 * $x;
$PA_W = 45 * $x;
$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
<?php
    $test = $farray4["STAT_PFC_ENTRANCE"][0]; 
  	switch($test) {
		case "4": 
	   	  echo "<TD class=fTable_2_1 bgcolor=00C131>
	   			<TABLE>
	   			<TR><TD class=fTable_2_1_pai>P/I</TD></TR>
	   			<TR><TD class=fTable_2_1_pai></TD></TR></TABLE></TD>";
	   	  //$_SESSION["SELEC"] = MAX_TIME;
	      break;
  		case "1": 
	  	  echo "<TD class=fTable_2_1 bgcolor=white>
	  			<TABLE>
	  			<TR><TD class=fTable_2_1_pai>P/I</TD></TR>
	  			<TR><TD class=fTable_2_1_pai></TD></TR></TABLE></TD>";
	  	  //$_SESSION["SELEC"] = MAX_TIME;
		  break;
  		case "2": 
	  	  echo "<TD class=fTable_2_1 bgcolor=red>
	  			<TABLE>
	  			<TR><TD class=fTable_2_1_pai>P/I</TD></TR>
	  			<TR><TD class=fTable_2_1_pai></TD></TR></TABLE></TD>";
	  	  //$_SESSION["SELEC"] = MAX_TIME;
		  break;
  		case "3": 
	  	  echo "<TD class=fTable_2_1 bgcolor=yellow>
	  			<TABLE>
	  			<TR><TD class=fTable_2_1_pai>P/I</TD></TR>
	  			<TR><TD class=fTable_2_1_pai></TD></TR></TABLE></TD>";
	  	  //$_SESSION["SELEC"] = MAX_TIME;
		  break;
  		default: 
	  	  echo "<TD class=fTable_2_1 bgcolor=red>
	  			<TABLE>
	  			<TR><TD class=fTable_2_1_pai>P/I</TD></TR>
	  			<TR><TD class=fTable_2_1_pai></TD></TR></TABLE></TD>";
		  //if ($_SESSION["SELEC"] == MAX_TIME) $_SESSION["SELEC"] = time();
		}
?>
</TABLE>

<!-- SELECTIVITY PFC OUT -->
<?php
$PA_L = 987 * $x;
$PA_T = 875 * $x;
$PA_H = 63 * $x;
$PA_W = 45 * $x;
$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
<?php
    $test = $farray4["STAT_PFC_EXIT"][0]; 
  	switch($test) {
		case "4": 
	   	  echo "<TD class=fTable_2_1 bgcolor=00C131>
	   			<TABLE>
	   			<TR><TD class=fTable_2_1_pai>P/O</TD></TR>
	   			<TR><TD class=fTable_2_1_pai></TD></TR></TABLE></TD>";
	   	  //$_SESSION["SELEC"] = MAX_TIME;
	      break;
  		case "1": 
	  	  echo "<TD class=fTable_2_1 bgcolor=white>
	  			<TABLE>
	  			<TR><TD class=fTable_2_1_pai>P/O</TD></TR>
	  			<TR><TD class=fTable_2_1_pai></TD></TR></TABLE></TD>";
	  	  //$_SESSION["SELEC"] = MAX_TIME;
		  break;
  		case "2": 
	  	  echo "<TD class=fTable_2_1 bgcolor=red>
	  			<TABLE>
	  			<TR><TD class=fTable_2_1_pai>P/O</TD></TR>
	  			<TR><TD class=fTable_2_1_pai></TD></TR></TABLE></TD>";
	  	  //$_SESSION["SELEC"] = MAX_TIME;
		  break;
  		case "3": 
	  	  echo "<TD class=fTable_2_1 bgcolor=yellow>
	  			<TABLE>
	  			<TR><TD class=fTable_2_1_pai>P/O</TD></TR>
	  			<TR><TD class=fTable_2_1_pai></TD></TR></TABLE></TD>";
	  	  //$_SESSION["SELEC"] = MAX_TIME;
		  break;
  		default: 
	  	  echo "<TD class=fTable_2_1 bgcolor=red>
	  			<TABLE>
	  			<TR><TD class=fTable_2_1_pai>P/O</TD></TR>
	  			<TR><TD class=fTable_2_1_pai></TD></TR></TABLE></TD>";
		  //if ($_SESSION["SELEC"] == MAX_TIME) $_SESSION["SELEC"] = time();
		}
?>
</TABLE>

<!-- CANVAS -->
<?php


// canvas values are getting from t_lm_paint table, spare_7 column and converted to binary system
// where last byte represent Robot, last-1 Lifter, last-2 conveyor.
//
//   Conveyor | Lifter | Robot  | spare_7
//      0     |    0   |   0    |   0  
//      0     |    0   |   1    |   1  
//      0     |    1   |   0    |   2  
//      0     |    1   |   1    |   3  
//      1     |    0   |   0    |   4  
//      1     |    0   |   1    |   5  
//      1     |    1   |   0    |   6  
//      1     |    1   |   1    |   7  
// 
// binary value 1 means ERROR, 0 means OK

// convert spare_7 to binary system
$canvas_values = substr(str_pad((decBin($farray4["CANVAS"][0])),10,'0',STR_PAD_LEFT),-3);


//$test = 6;
//$canvas_values = substr(str_pad((decBin($test)),10,'0',STR_PAD_LEFT),-3);
// $canvas_values = '111'; //test value

// split binary code to single values for each canvas equipment
$canvas_conveyor = substr($canvas_values,0,1);
$canvas_lifter = substr($canvas_values,1,1);
$canvas_robot = substr($canvas_values,2,1);


?>
<!-- Conveyor -->
<?php
$PA_L = 987 * $x;
$PA_T = 840 * $x;
$PA_H = 30 * $x;
$PA_W = 30 * $x;
$PA_height = 10 * $x;
echo "<TABLE title=\"Conveyor\" style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
<?php
    $test = $canvas_conveyor;
  	switch($test) {
  		case "1":
      echo "<TD class=fTable_2_1 bgcolor=red>C</TD>";
		  break;
  		default: 
	  	  echo "<TD class=fTable_2_1 rowspan=1 bgcolor=00C131>C</TD>";
	  		 break;
		}
?>
</TR>
</TABLE>

<!-- Lifter -->
<?php
$PA_L = 1016 * $x;
$PA_T = 840 * $x;
$PA_H = 30 * $x;
$PA_W = 30 * $x;
$PA_height = 10 * $x;
echo "<TABLE title=\"Lifter\" style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
<?php
    $test = $canvas_lifter;
  	switch($test) {
  		case "1":
      echo "<TD class=fTable_2_1 bgcolor=red>L</TD>";
		  break;
  		default: 
	  	  echo "<TD class=fTable_2_1 rowspan=1 bgcolor=00C131>L</TD>";
	  		 break;
		}
?>
</TR>
</TABLE>

<!-- Robot -->
<?php
$PA_L = 1045 * $x;
$PA_T = 840 * $x;
$PA_H = 30 * $x;
$PA_W = 30 * $x;
$PA_height = 10 * $x;
echo "<TABLE title=\"Robot\" style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
<?php
    $test = $canvas_robot;
  	switch($test) {
  		case "1":
      echo "<TD class=fTable_2_1 bgcolor=red>R</TD>";
		  break;
  		default: 
	  	  echo "<TD class=fTable_2_1 rowspan=1 bgcolor=00C131>R</TD>";
	  		 break;
		}
?>
</TR>
</TABLE>


<!-- ASSY LOAD -->
<?php
$PA_L = 1040 * $x;
$PA_T = 875 * $x;
$PA_H = 62 * $x;
$PA_W = 35 * $x;
$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
<?php
//    $test = "PLNSTOP";//@$farray4["LINESTS_SLCT"][0]; 
    $test = $farray5["ASSY_LOAD"][0]; 
  	switch($test) {
//		case "RUNNING": 
//	   	  echo "<TD class=fTable_2_1 rowspan=1 bgcolor=00C131>ASSY LOAD</TD>";
//	   	  //echo "<TD id=\"SELEC\" class=fTable_2_1 rowspan=1 bgcolor=00C131>ASSY LOAD</TD>";
//	   	  //$_SESSION["SELEC"] = MAX_TIME;
//	      break;
  		case "0":
// 	  echo "<TD id=\"ASS_L\" class=fTable_2_1 bgcolor=red style=\"\"><img src=\"pictures/assy_l.gif\" height=".$PA_H." width=".$PA_W." /></TD>";
      echo "<TD id=\"ASS_L\" class=fTable_2_1 bgcolor=red </TD>
        <TABLE>
				<TR><TD class=fTable_2_1_pai>AL</TD></TR>
	  		<TR><TD class=fTable_2_1_pai></TD></TR></TABLE></TD>";
		  if ($_SESSION["ASS_L"] == MAX_TIME) $_SESSION["ASS_L"] = time();
	 
		  break;
		  
		  
  		default: 
	  	  echo "<TD id=\"ASS_L\" class=fTable_2_1 rowspan=1 bgcolor=00C131>
				<TABLE>
				<TR><TD class=fTable_2_1_pai>AL</TD></TR>
	  		<TR><TD class=fTable_2_1_pai></TD></TR></TABLE></TD>";
	  		$_SESSION["ASS_L"] = MAX_TIME;
	  		
	  		
	  		 break;
		}
?>
</TR>
</TABLE>
	
<!-- PR.INSP -->
<!-- PR.I prejmenovano na ILM -->
<!-- ILM -->
<?php
$PA_L = 110 * $x;
$PA_T = 880 * $x;
$PA_H = 40 * $x;
$PA_W = 200 * $x;
$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
<?php
    $test = @$farray4["LINESTS_PRINSP"][0];
    switch($test) {
        case "RUNNING":
            echo "<TD class=fTable_2_1 rowspan=2 bgcolor=00C131>ILM</TD>";
            break;
        case "PLNSTOP":
            echo "<TD class=fTable_2_1 rowspan=2 bgcolor=white>ILM</TD>";
            break;
        default:
            echo "<TD class=fTable_2_1 rowspan=2 bgcolor=red>ILM</TD>";
    }
    $test = @$farray4["STPSTSS_PRINSP"][0];
    switch($test) {
        case "ERRSTOP":
            echo "<TD class=fTable_2_2 bgcolor=yellow>Short</TD>";
            break;
        default:
            echo "<TD class=fTable_2_2 bgcolor=white>Short</TD>";
    }

	$test = @$farray4["STPSTSO_PRINSP"][0];
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_2 bgcolor=yellow>Self</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_2 bgcolor=white>Self</TD>";
	}
    $test = @$farray4["STPSTSF_PRINSP"][0];
    switch($test) {
        case "ERRSTOP":
            echo "<TD class=fTable_2_2 bgcolor=yellow>Full</TD>";
            break;
        default:
            echo "<TD class=fTable_2_2 bgcolor=white>Full</TD>";
    }
 	//---
?>
  </TR>
  <TR>
<?php

    $test = @$farray4["STPSTSS_PRINSP"][0];
    $test2 = $farray4["STPTIMS_PRINSP"][0];
    switch($test) {
        case "ERRSTOP":
            if ($test2 == "")	{
                echo "<TD class=fTable_2_3 bgcolor=yellow>0</TD>";
            } else {
                echo "<TD class=fTable_2_3 bgcolor=yellow>" . $test2 . "</TD>";
            }
            break;
        default:
            if ($test2 == "")	{
                echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
            } else {
                echo "<TD class=fTable_2_3 bgcolor=white>" . $test2 . "</TD>";
            }
            break;
    }

    $test = @$farray4["STPSTSO_PRINSP"][0]; 
	$test2 = $farray4["STPTIMO_PRINSP"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=yellow>" . $test2 . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=white>" . $test2 . "</TD>";
				}	
				break;
	}

    $test = @$farray4["STPSTSF_PRINSP"][0];
    $test2 = $farray4["STPTIMF_PRINSP"][0];
    switch($test) {
        case "ERRSTOP":
            if ($test2 == "")	{
                echo "<TD class=fTable_2_3 bgcolor=yellow>0</TD>";
            } else {
                echo "<TD class=fTable_2_3 bgcolor=yellow>" . $test2 . "</TD>";
            }
            break;
        default:
            if ($test2 == "")	{
                echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
            } else {
                echo "<TD class=fTable_2_3 bgcolor=white>" . $test2 . "</TD>";
            }
            break;
    }

?>
  </TR>
</TABLE>

<!-- ED Inspection -->
<?php
$PA_L = 660 * $x;
$PA_T = 700 * $x;
$PA_H = 40 * $x;
$PA_W = 200 * $x;
$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
    <TR>
<?php

   $test = @$farray4["LINESTS_EDINSP"][0]; 
  	switch($test) {
		case "RUNNING": 
	   	  echo "<TD id=\"ED_S\" class=fTable_2_1 rowspan=2 bgcolor=00C131>ED I</TD>";
	   	  $_SESSION["ED_S"] = MAX_TIME;
	      break;
  		case "PLNSTOP": 
	  	  echo "<TD id=\"ED_S\" class=fTable_2_1 rowspan=2 bgcolor=white>ED I</TD>";
	  	  $_SESSION["ED_S"] = MAX_TIME;
		  break;
  		default: 
	  	  echo "<TD id=\"ED_S\" class=fTable_2_1 rowspan=2 bgcolor=red>ED I</TD>";
		  if ($_SESSION["ED_S"] == MAX_TIME) $_SESSION["ED_S"] = time();
	}	
	 $test = @$farray4["STPSTSS_EDINSP"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_2 bgcolor=yellow>Short</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_2 bgcolor=white>Short</TD>";
	}
	
	$test = @$farray4["STPSTSO_EDINSP"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_2 bgcolor=yellow>Self</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_2 bgcolor=white>Self</TD>";
	}
   $test = @$farray4["STPSTSF_EDINSP"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_2 bgcolor=yellow>Full</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_2 bgcolor=white>Full</TD>";
	}
	//---
 	
?>
  </TR>
  <TR>
<?php
				     $test = @$farray4["STPSTSS_EDINSP"][0]; 
	$test2 = $farray4["STPTIMS_EDINSP"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=yellow>" . $test2 . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=white>" . $test2 . "</TD>";
				}	
				break;
	}		
	
    $test = @$farray4["STPSTSO_EDINSP"][0]; 
	$test2 = $farray4["STPTIMO_EDINSP"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=yellow>" . $test2 . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=white>" . $test2 . "</TD>";
				}	
				break;
	}			
    
		
		$test = @$farray4["STPSTSF_EDINSP"][0]; 
	$test2 = $farray4["STPTIMF_EDINSP"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=yellow>" . $test2 . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=white>" . $test2 . "</TD>";
				}	
				break;
	}		
?>
  </TR>

</TABLE>

<!-- UBC -->
<?php
$PA_L = 665 * $x;
$PA_T = 780 * $x;
$PA_H = 40 * $x;
$PA_W = 200 * $x;
$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
<?php
	$test = @$farray4["STPSTSF_UBC"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_4 bgcolor=yellow>Full</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_4 bgcolor=white>Full</TD>";
	}
	$test = @$farray4["STPSTSO_UBC"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_4 bgcolor=yellow>Self</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_4 bgcolor=white>Self</TD>";
	}
    $test = @$farray4["STPSTSS_UBC"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_4 bgcolor=yellow>Short</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_4 bgcolor=white>Short</TD>";
	}
	//---
    $test = @$farray4["LINESTS_UBC"][0]; 
  	switch($test) {
		case "RUNNING": 
	   	  echo "<TD id=\"UBC\" class=fTable_2_1 rowspan=2 bgcolor=00C131>UBC</TD>";
	   	  $_SESSION["UBC"] = MAX_TIME;
	      break;
  		case "PLNSTOP": 
	  	  echo "<TD id=\"UBC\" class=fTable_2_1 rowspan=2 bgcolor=white>UBC</TD>";
	  	  $_SESSION["UBC"] = MAX_TIME;
		  break;
  		default: 
	  	  echo "<TD id=\"UBC\" class=fTable_2_1 rowspan=2 bgcolor=red>UBC</TD>";
		  if ($_SESSION["UBC"] == MAX_TIME) $_SESSION["UBC"] = time();
	}		
?>
  </TR>
  <TR>
<?php
	$test = @$farray4["STPSTSF_UBC"][0]; 
	$test2 = $farray4["STPTIMF_UBC"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=yellow>" . $test2 . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=white>" . $test2 . "</TD>";
				}	
				break;
	}						 	
	
    $test = @$farray4["STPSTSO_UBC"][0]; 
	$test2 = $farray4["STPTIMO_UBC"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=yellow>" . $test2 . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=white>" . $test2 . "</TD>";
				}	
				break;
	}			
    
    $test = @$farray4["STPSTSS_UBC"][0]; 
	$test2 = $farray4["STPTIMS_UBC"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=yellow>" . $test2 . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=white>" . $test2 . "</TD>";
				}	
				break;
	}			
?>
  </TR>
</TABLE>

<!-- SEALER P -->
<?php
$PA_L = 870 * $x;
$PA_T = 780 * $x;
$PA_H = 40 * $x;
$PA_W = 200 * $x;
$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
<?php
	$test = @$farray4["STPSTSF_SEAL"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_4 bgcolor=yellow>Full</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_4 bgcolor=white>Full</TD>";
	}
	$test = @$farray4["STPSTSO_SEAL"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_4 bgcolor=yellow>Self</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_4 bgcolor=white>Self</TD>";
	}
    $test = @$farray4["STPSTSS_SEAL"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_4 bgcolor=yellow>Short</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_4 bgcolor=white>Short</TD>";
	}
	//---
    $test = @$farray4["LINESTS_SEAL"][0]; 
  	switch($test) {
		case "RUNNING": 
	   	  echo "<TD id=\"SEAL\" class=fTable_2_1 rowspan=2 bgcolor=00C131>SEAL_M</TD>";
	   	  $_SESSION["SEAL"] = MAX_TIME;
	      break;
  		case "PLNSTOP": 
	  	  echo "<TD id=\"SEAL\" class=fTable_2_1 rowspan=2 bgcolor=white>SEAL_M</TD>";
	  	  $_SESSION["SEAL"] = MAX_TIME;
		  break;
  		default: 
	  	  echo "<TD id=\"SEAL\" class=fTable_2_1 rowspan=2 bgcolor=red>SEAL_M</TD>";
		  if ($_SESSION["SEAL"] == MAX_TIME) $_SESSION["SEAL"] = time();
	}		
?>
  </TR>
  <TR>
<?php
	$test = @$farray4["STPSTSF_SEAL"][0]; 
	$test2 = $farray4["STPTIMF_SEAL"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=yellow>" . $test2 . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=white>" . $test2 . "</TD>";
				}	
				break;
	}						 	
	
  $test = @$farray4["STPSTSO_SEAL"][0]; 
	$test2 = $farray4["STPTIMO_SEAL"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=yellow>" . $test2 . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=white>" . $test2 . "</TD>";
				}	
				break;
	}			
    
    $test = @$farray4["STPSTSS_SEAL"][0]; 
	$test2 = $farray4["STPTIMS_SEAL"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=yellow>" . $test2 . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=white>" . $test2 . "</TD>";
				}	
				break;
	}			
?>
  </TR>
</TABLE>

<!-- SEALER A -->
<?php
$PA_L = 870 * $x;
$PA_T = 700 * $x;
$PA_H = 40 * $x;
$PA_W = 200 * $x;
$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
<?php
    $test = @$farray4["LINESTS_SEALR"][0]; 
  	switch($test) {
		case "RUNNING": 
	   	  echo "<TD id=\"SEALR\" class=fTable_2_1 rowspan=2 bgcolor=00C131>SEAL_A</TD>";
	   	  $_SESSION["SEALR"] = MAX_TIME;
	      break;
  		case "PLNSTOP": 
	  	  echo "<TD id=\"SEALR\" class=fTable_2_1 rowspan=2 bgcolor=white>SEAL_A</TD>";
	  	  $_SESSION["SEALR"] = MAX_TIME;
		  break;
  		default: 
	  	  echo "<TD id=\"SEALR\" class=fTable_2_1 rowspan=2 bgcolor=red>SEAL_A</TD>";
		  if ($_SESSION["SEALR"] == MAX_TIME) $_SESSION["SEALR"] = time();
	}		
    $test = @$farray4["STPSTSS_SEALR"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_4 bgcolor=yellow>Short</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_4 bgcolor=white>Short</TD>";
	}
	$test = @$farray4["STPSTSO_SEALR"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_4 bgcolor=yellow>Self</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_4 bgcolor=white>Self</TD>";
	}
	$test = @$farray4["STPSTSF_SEALR"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_4 bgcolor=yellow>Full</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_4 bgcolor=white>Full</TD>";
	}
	//---

?>
  </TR>
  <TR>
<?php
  $test = @$farray4["STPSTSS_SEALR"][0]; 
	$test2 = $farray4["STPTIMS_SEALR"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=yellow>" . $test2 . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=white>" . $test2 . "</TD>";
				}	
				break;
	}			
  $test = @$farray4["STPSTSO_SEALR"][0]; 
	$test2 = $farray4["STPTIMO_SEALR"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=yellow>" . $test2 . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=white>" . $test2 . "</TD>";
				}	
				break;
	}			
	$test = @$farray4["STPSTSF_SEALR"][0]; 
	$test2 = $farray4["STPTIMF_SEALR"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=yellow>" . $test2 . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=white>" . $test2 . "</TD>";
				}	
				break;
	}						 	

?>
  </TR>
</TABLE>


<!-- 30 sedy dole uprostred -->
<!--<?php
$PA_L = 270 * $x;
$PA_T = 1050 * $x;
$PA_H = 80 * $x;
$PA_W = 100 * $x;
$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px; \" cellpadding=\"0\" cellspacing=\"0\">";
  echo "<TR height=".$PA_height.">";

        $test = @$farray8["INVCUR_PFIN"][0];
		$test2 = @$farray9["COLMIN_PFIN"][0];
		$test3 = @$farray9["COLMAX_PFIN"][0];
		if ($test2 == "") {
		  $test2 = 0;
		}
		if ($test3 == "") {
		  $test3 = 0;
		}
    if ($test == "") {
  		echo "<TD class=fTable_6_1 colspan=3 bgcolor=red>0</TD>";
		} else {
		  if (($test < $test2) || ($test > $test3)) {
			  echo "<TD class=fTable_6_1 colspan=3 bgcolor=red>" . $farray8["INVCUR_PFIN"][0] . "</TD>";
			} else {
			  echo "<TD class=fTable_6_1 colspan=3 bgcolor=silver>" . $farray8["INVCUR_PFIN"][0] . "</TD>";
			}
		}
		
?>
  </TR>
  <TR height=10>
    <TD class=fTable_6_2 width=33% style="border-left:1.0pt solid white;"><?php $test = @$farray9["INVMIN_PFIN"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVMIN_PFIN"][0]; } ?></TD>
    <TD class=fTable_6_2 width=33%><?php $test = @$farray9["INVSTD_PFIN"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVSTD_PFIN"][0]; } ?></TD>
    <TD class=fTable_6_2><?php $test = @$farray9["INVMAX_PFIN"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVMAX_PFIN"][0]; } ?></TD>
  </TR>
</TABLE> -->

<!-- 33 sedy vpravo dole -->
<!--<?php
$PA_L = 780 * $x;
$PA_T = 1050 * $x;
$PA_H = 80 * $x;
$PA_W = 100 * $x;
$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px; \" cellpadding=\"0\" cellspacing=\"0\">";
  echo "<TR height=".$PA_height.">";

        $test = @$farray8["INVCUR_SELEC"][0];
		$test2 = @$farray9["COLMIN_SELEC"][0];
		$test3 = @$farray9["COLMAX_SELEC"][0];
		if ($test2 == "") {
		  $test2 = 0;
		}
		if ($test3 == "") {
		  $test3 = 0;
		}
    if ($test == "") {
  		echo "<TD class=fTable_6_1 colspan=3 bgcolor=red>0</TD>";
		} else {
		  if (($test < $test2) || ($test > $test3)) {
			  echo "<TD class=fTable_6_1 colspan=3 bgcolor=red>" . $farray8["INVCUR_SELEC"][0] . "</TD>";
			} else {
			  echo "<TD class=fTable_6_1 colspan=3 bgcolor=silver>" . $farray8["INVCUR_SELEC"][0] . "</TD>";
			}
		}
		
?>
  </TR>
  <TR height=10>
    <TD class=fTable_6_2 width=33% style="border-left:1.0pt solid white;"><?php $test = @$farray9["INVMIN_SELEC"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVMIN_SELEC"][0]; } ?></TD>
    <TD class=fTable_6_2 width=33%><?php $test = @$farray9["INVSTD_SELEC"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVSTD_SELEC"][0]; } ?></TD>
    <TD class=fTable_6_2><?php $test = @$farray9["INVMAX_SELEC"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVMAX_SELEC"][0]; } ?></TD>
  </TR>
</TABLE>-->
<?php
//WASH-ED IN. do ED I
$BO01_L = 860 * $x;
$BO01_T = 678 * $x;
$BO01_H = 12 * $x;
$BO01_W = 2 * $x;

$BO02_L = 685 * $x;
$BO02_T = 690 * $x;
$BO02_H = 2 * $x;
$BO02_W = 176 * $x;

$BO03_L = 685 * $x;
$BO03_T = 690 * $x;
$BO03_H = 12 * $x;
$BO03_W = 2 * $x;

echo "<hr style=\"position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px;\" noshade color=black>";
echo "<hr style=\"position:absolute; left: {$BO02_L}px; top: {$BO02_T}px; height: {$BO02_H}px; width: {$BO02_W}px; \" noshade color=black>";
echo "<hr style=\"position:absolute; left: {$BO03_L}px; top: {$BO03_T}px; height: {$BO03_H}px; width: {$BO03_W}px; \" noshade color=black>";


//ED I do SEALER A

$BO01_L = 860 * $x;
$BO01_T = 725 * $x;
$BO01_H = 2 * $x;
$BO01_W = 10 * $x;
echo "<hr style=\"position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px;\" noshade color=black>";


//ze sealeru A do SEALER P

$BO01_L = 1070 * $x;
$BO01_T = 725 * $x;
$BO01_H = 2 * $x;
$BO01_W = 10 * $x;

$BO02_L = 1079 * $x;
$BO02_T = 725 * $x;
$BO02_H = 78 * $x;
$BO02_W = 2 * $x;

$BO03_L = 1069 * $x;
$BO03_T = 801 * $x;
$BO03_H = 2 * $x;
$BO03_W = 10 * $x;


echo "<hr style=\"position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px;\" noshade color=black>";
echo "<hr style=\"position:absolute; left: {$BO02_L}px; top: {$BO02_T}px; height: {$BO02_H}px; width: {$BO02_W}px;\" noshade color=black>";
echo "<hr style=\"position:absolute; left: {$BO03_L}px; top: {$BO03_T}px; height: {$BO03_H}px; width: {$BO03_W}px; \" noshade color=black>";

$BO01_L = 865 * $x;
$BO01_T = 800 * $x;
$BO01_H = 2 * $x;
$BO01_W = 5 * $x;

echo "<hr style=\"position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px; \" noshade color=black>";

$BO01_L = 660 * $x;
$BO01_T = 800 * $x;
$BO01_H = 2 * $x;
$BO01_W = 5 * $x;
echo "<hr style=\"position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px; \" noshade color=black>";


$BO01_L = 590 * $x;
$BO01_T = 800 * $x;
$BO01_H = 2 * $x;
$BO01_W = 6 * $x;
echo "<hr style=\"position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px; \" noshade color=black>";

$BO01_L = 382 * $x;
$BO01_T = 800 * $x;
$BO01_H = 2 * $x;
$BO01_W = 8 * $x;
echo "<hr style=\"position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px; \" noshade color=black>";


$BO01_L = 305 * $x;
$BO01_T = 800 * $x;
$BO01_H = 2 * $x;
$BO01_W = 10 * $x;
echo "<hr style=\"position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px; \" noshade color=black>";

$BO01_L = 236 * $x;
$BO01_T = 800 * $x;
$BO01_H = 2 * $x;
$BO01_W = 10 * $x;
echo "<hr style=\"position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px; \" noshade color=black>";

$BO01_L = 20 * $x;
$BO01_T = 766 * $x;
$BO01_H = 2 * $x;
$BO01_W = 10 * $x;
echo "<hr style=\"position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px; \" noshade color=black>";

$BO02_L = 20 * $x;
$BO02_T = 911 * $x;
$BO02_H = 2 * $x;
$BO02_W = 10 * $x;

$BO03_L = 20 * $x;
$BO03_T = 831 * $x;
$BO03_H = 2 * $x;
$BO03_W = 10 * $x;

$BO05_L = 20 * $x;
$BO05_T = 766 * $x;
$BO05_H = 147 * $x;
$BO05_W = 2 * $x;


echo "<hr style=\"position:absolute; left: {$BO05_L}px; top: {$BO05_T}px; height: {$BO05_H}px; width: {$BO05_W}px;\" noshade color=black>";
echo "<hr style=\"position:absolute; left: {$BO03_L}px; top: {$BO03_T}px; height: {$BO03_H}px; width: {$BO03_W}px; \" noshade color=black>";
echo "<hr style=\"position:absolute; left: {$BO02_L}px; top: {$BO02_T}px; height: {$BO02_H}px; width: {$BO02_W}px; \" noshade color=black>";


$BO02_L = 235 * $x;
$BO02_T = 766 * $x;
$BO02_H = 62 * $x;
$BO02_W = 2 * $x;

$BO03_L = 230 * $x;
$BO03_T = 766 * $x;
$BO03_H = 2 * $x;
$BO03_W = 5 * $x;

$BO04_L = 230 * $x;
$BO04_T = 828 * $x;
$BO04_H = 2 * $x;
$BO04_W = 5 * $x;

echo "<hr style=\"position:absolute; left: {$BO02_L}px; top: {$BO02_T}px; height: {$BO02_H}px; width: {$BO02_W}px;\" noshade color=black>";
echo "<hr style=\"position:absolute; left: {$BO03_L}px; top: {$BO03_T}px; height: {$BO03_H}px; width: {$BO03_W}px; \" noshade color=black>";
echo "<hr style=\"position:absolute; left: {$BO04_L}px; top: {$BO04_T}px; height: {$BO04_H}px; width: {$BO04_W}px; \" noshade color=black>";





$BO01_L = 103 * $x;
$BO01_T = 903 * $x;
$BO01_H = 2 * $x;
$BO01_W = 5 * $x;
echo "<hr style=\"position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px; \" noshade color=black>";

$BO02_L = 65 * $x;
$BO02_T = 940 * $x;
$BO02_H = 15 * $x;
$BO02_W = 2 * $x;

$BO01_L = 65 * $x;
$BO01_T = 955 * $x;
$BO01_H = 1 * $x;
$BO01_W = 275 * $x;

$BO03_L = 340 * $x;
$BO03_T = 930 * $x;
$BO03_H = 25 * $x;
$BO03_W = 2 * $x;

echo "<hr style=\"position:absolute; left: {$BO02_L}px; top: {$BO02_T}px; height: {$BO02_H}px; width: {$BO02_W}px; z-index: 1; \" noshade color=black>";
echo "<hr style=\"position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px; z-index: 1; \" noshade color=black>";
echo "<hr style=\"position:absolute; left: {$BO03_L}px; top: {$BO03_T}px; height: {$BO03_H}px; width: {$BO03_W}px; z-index: 1; \" noshade color=black>";

$BO01_L = 309 * $x;
$BO01_T = 903 * $x;
$BO01_H = 2 * $x;
$BO01_W = 4 * $x;
echo "<hr style=\"position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px; \" noshade color=black>";

$BO01_L = 311 * $x;
$BO01_T = 815 * $x;
$BO01_H = 90 * $x;
$BO01_W = 2 * $x;

$BO02_L = 308 * $x;
$BO02_T = 815 * $x;
$BO02_H = 2 * $x;
$BO02_W = 5 * $x;

echo "<hr style=\"position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px; \" noshade color=black>";
echo "<hr style=\"position:absolute; left: {$BO02_L}px; top: {$BO02_T}px; height: {$BO02_H}px; width: {$BO02_W}px; \" noshade color=black>";

$BO01_L = 500 * $x;
$BO01_T = 903 * $x;
$BO01_H = 2 * $x;
$BO01_W = 5 * $x;
echo "<hr style=\"position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px; \" noshade color=black>";


$BO01_L = 685 * $x;
$BO01_T = 903 * $x;
$BO01_H = 2 * $x;
$BO01_W = 5 * $x;
echo "<hr style=\"position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px; \" noshade color=black>";

$BO01_L = 870 * $x;
$BO01_T = 903 * $x;
$BO01_H = 2 * $x;
$BO01_W = 3 * $x;
echo "<hr style=\"position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px; \" noshade color=black>";

$BO01_L = 917 * $x;
$BO01_T = 903 * $x;
$BO01_H = 2 * $x;
$BO01_W = 4 * $x;
echo "<hr style=\"position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px; \" noshade color=black>";


$BO01_L = 984 * $x;
$BO01_T = 903 * $x;
$BO01_H = 2 * $x;
$BO01_W = 4 * $x;
echo "<hr style=\"position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px; \" noshade color=black>";

$BO01_L = 1036 * $x;
$BO01_T = 903 * $x;
$BO01_H = 2 * $x;
$BO01_W = 4 * $x;
echo "<hr style=\"position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px; \" noshade color=black>";


$BO01_L = 1077 * $x;
$BO01_T = 903 * $x;
$BO01_H = 2 * $x;
$BO01_W = 16 * $x;
echo "<hr style=\"position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px; \" noshade color=black>";

?>
<!-- WINDOW 3 FINISH-->

<!-- WINDOW 4 -->
<!-- PAINT - TRIM -->
<?php
//----------presun pred ASS	
//dlouha svisla z PAINT do ASS
$BO01_L = 1092 * $x;
$BO01_T = 561 * $x;
$BO01_H = 343 * $x;
$BO01_W = 2 * $x;
echo "<hr style=\"background:black; position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px;\"></hr>";
//dalsi vodorovna
$BO01_L = 994 * $x;
$BO01_T = 560 * $x;
$BO01_H = 2 * $x;
$BO01_W = 99 * $x;
echo "<hr style=\"position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px; \" noshade color=black>";
//svisla pred TRIM
$BO01_L = 995 * $x;
$BO01_T = 319 * $x;
$BO01_H = 242 * $x;
$BO01_W = 2 * $x;

$BO02_L = 1040 * $x;
$BO02_T = 543 * $x;
$BO02_H = 18 * $x;
$BO02_W = 2 * $x;
echo "<hr style=\"background:black; position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px;\"></hr>";
//echo "<hr style=\"position:absolute; left: {$BO02_L}px; top: {$BO02_T}px; height: {$BO02_H}px; width: {$BO02_W}px;\" noshade color=black>";
//mezi TRIM a CHASS
$BO01_L = 995 * $x;
$BO01_T = 20 * $x;
$BO01_H = 10 * $x;
$BO01_W = 2 * $x;

$BO02_L = 1065 * $x;
$BO02_T = 20 * $x;
$BO02_H = 10 * $x;
$BO02_W = 2 * $x;

$BO03_L = 995 * $x;
$BO03_T = 20 * $x;
$BO03_H = 2 * $x;
$BO03_W = 70 * $x;
echo "<hr style=\"position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px;\" noshade color=black>";
echo "<hr style=\"position:absolute; left: {$BO02_L}px; top: {$BO02_T}px; height: {$BO02_H}px; width: {$BO02_W}px;\" noshade color=black>";
echo "<hr style=\"position:absolute; left: {$BO03_L}px; top: {$BO03_T}px; height: {$BO03_H}px; width: {$BO03_W}px; \" noshade color=black>";

$BO01_L = 1050 * $x;
$BO01_T = 320 * $x;
$BO01_H = 30 * $x;
$BO01_W = 2 * $x;
//echo "<hr style=\"position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px;\" noshade color=black>";
//mezi chass a FIN1
$BO01_L = 1065 * $x;
$BO01_T = 316 * $x;
$BO01_H = 60 * $x;
$BO01_W = 2 * $x;

$BO02_L = 1065 * $x;
$BO02_T = 375 * $x;
$BO02_H = 2 * $x;
$BO02_W = 239 * $x;
echo "<div style=\"background:black; position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px;\"></div>";
//echo "<hr style=\"position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px;\" noshade color=black>";
echo "<hr style=\"position:absolute; left: {$BO02_L}px; top: {$BO02_T}px; height: {$BO02_H}px; width: {$BO02_W}px; \" noshade color=black>";
//mezi FIN1 a FIN2
$BO01_L = 1550 * $x;
$BO01_T = 375 * $x;
$BO01_H = 2 * $x;
$BO01_W = 10 * $x;

$BO02_L = 1560 * $x;
$BO02_T = 300 * $x;
$BO02_H = 77 * $x;
$BO02_W = 2 * $x;

$BO03_L = 1550 * $x;
$BO03_T = 300 * $x;
$BO03_H = 2 * $x;
$BO03_W = 10 * $x;
echo "<hr style=\"position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px; \" noshade color=black>";
echo "<hr style=\"position:absolute; left: {$BO02_L}px; top: {$BO02_T}px; height: {$BO02_H}px; width: {$BO02_W}px;\" noshade color=black>";
echo "<hr style=\"position:absolute; left: {$BO03_L}px; top: {$BO03_T}px; height: {$BO03_H}px; width: {$BO03_W}px; \" noshade color=black>";
//mezi ASS a QC
$BO01_L = 1260 * $x;
$BO01_T = 310 * $x;
$BO01_H = 2 * $x;
$BO01_W = 41 * $x;

$BO02_L = 1260 * $x;
$BO02_T = 310 * $x;
$BO02_H = 430 * $x;
$BO02_W = 2 * $x;
echo "<hr style=\"position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px; \" noshade color=black>";
echo "<div style=\"background:black; position:absolute; left: {$BO02_L}px; top: {$BO02_T}px; height: {$BO02_H}px; width: {$BO02_W}px;\"></div>";
//----------presun pred ASS	
	
$AS_L = 948 * $x;
$AS_T = 440 * $x;
$AS_H = 100 * $x;
$AS_W = 110 * $x;
$AS_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$AS_L}px; top: {$AS_T}px; height: {$AS_H}px; width: {$AS_W}px; \" cellpadding=\"0\" cellspacing=\"0\">";
  echo "<TR height=".$AS_height.">";
?>
    <TD class=fTable_4_1 colspan=3>T - A</TD>
  </TR>
  <TR height=10>
<?php
    $test = @$farray8["INVCUR_PTOA"][0];
		$test2 = @$farray9["COLMIN_PTOA"][0];
		$test3 = @$farray9["COLMAX_PTOA"][0];
		// $test3 = 40;
		if ($test2 == "") {
		  $test2 = 0;
		}
		if ($test3 == "") {
		  $test3 = 0;
		}
    if ($test == "") {
  		echo "<TD id=\"TASND\" class=fTable_4_2 colspan=3 bgcolor=red>0</TD>";
		} else {
		  if (($test < $test2) || ($test > $test3)) {
			  echo "<TD id=\"TASND\" class=fTable_4_2 colspan=3 bgcolor=red>" . $farray8["INVCUR_PTOA"][0] . "</TD>";
			  if ($_SESSION["TA"] == MAX_TIME) $_SESSION["TA"] = time();
			} else {
			  echo "<TD id=\"TASND\" class=fTable_4_2 colspan=3 bgcolor=silver>" . $farray8["INVCUR_PTOA"][0] . "</TD>";
			  $_SESSION["TA"] = MAX_TIME;
			}
		}
		
?>
  </TR>
  <TR height=10>
    <TD class=fTable_4_3 width=33% style="border-left:1.0pt solid black;"><?php $test = @$farray9["INVMIN_PTOA"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVMIN_PTOA"][0]; } ?></TD>
    <TD class=fTable_4_3 width=33%><?php $test = @$farray9["INVSTD_PTOA"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVSTD_PTOA"][0]; } ?></TD>
    <TD class=fTable_4_3><?php $test = @$farray9["INVMAX_PTOA"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVMAX_PTOA"][0]; } ?></TD>
  </TR>
</TABLE>



<?php
$AS_L = 952 * $x;
$AS_T = 325 * $x;
$AS_H = 70 * $x;
$AS_W = 80 * $x;
$AS_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$AS_L}px; top: {$AS_T}px; height: {$AS_H}px; width: {$AS_W}px; border:solid Black 1px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
<TR height=10>
    <TD class=fTable_1_2_t style="border-left:none;">G1B TA <BR>Ratio (%)</TD>
</TR>

<TR>
    <?php
    $test = @$farray8["G1B_TA_RATIO"][0];
    $testFmt = number_format($test, 2, '.', '');
    //    s9.G1B_RATIO_PLAN, s9.G1B_RATIO_DIFF_CLR
    $test2 = @$farray9["G1B_RATIO_PLAN"][0];
    $test2Fmt = number_format($test2, 2, '.', '');
    $border = @$farray9["G1B_RATIO_DIFF_CLR"][0];
    $difference = abs($test - $test2);
    $ta = @$farray8["INVCUR_PTOA"][0];
    $G1B = $test/100*($ta);
    $G1BFmt = number_format($G1B, 0, '.', '');

    if ($difference > $border) {
        echo "<TD class=fTable_1_3_t style='border-left:none; font-size:12pt;'  bgcolor='red'>". $testFmt ."</TD>";
    } else {
        echo "<TD class=fTable_1_3_t style='border-left:none; font-size:12pt;'  bgcolor='#CCFFCC'>". $testFmt ."</TD>";
    }
    ?>
</TR>
<?php
echo "<TD class=fTable_1_3_t style='border-left:none; font-size:12pt;'  bgcolor='#CCFFCC'>". $G1BFmt ."</TD>";
?>
</TABLE>
		
	<?php
	$AS_L = 1062 * $x;
	$AS_T = 440 * $x;
	$AS_H = 70 * $x;
	$AS_W = 80 * $x;
	$AS_height = 10 * $x;
	echo "<TABLE style=\"position:absolute; left: {$AS_L}px; top: {$AS_T}px; height: {$AS_H}px; width: {$AS_W}px; border:solid Black 1px;\" cellpadding=\"0\" cellspacing=\"0\">";
	?>	
	  <TR height=10>
		<TD colspan = 2 class=fTable_1_2_t style="border-left:none;">G1B Ratio (%)</TD>
	  </TR>
	  <TR height=10>
		<TD class=fTable_1_2_t style="border-left:none;">Ratio</TD>
		<TD class=fTable_1_2_t>Plan</TD>
	  </TR>


	  <TR>
	<?php 
	$test = @$farray8["G1B_RATIO"][0];
	$testFmt = number_format($test, 2, '.', '');
//    s9.G1B_RATIO_PLAN, s9.G1B_RATIO_DIFF_CLR
    $test2 = @$farray9["G1B_RATIO_PLAN"][0];
	$test2Fmt = number_format($test2, 2, '.', '');
	$border = @$farray9["G1B_RATIO_DIFF_CLR"][0];
	$difference = abs($test - $test2);
    $pfc = $farray8["INVCUR_SELEC"][0];
    $ta = @$farray8["INVCUR_PTOA"][0];
    $assy = @$farray8["INVCUR_ASSY"][0];
    $sum_G3_N0 = $pfc+$ta+$assy;
    $G1B = $test/100*($sum_G3_N0);
    $G1B_plan = $test2/100*($sum_G3_N0);
    $G1BFmt = number_format($G1B, 0, '.', '');
    $G1B_plan_Fmt = number_format($G1B_plan, 0, '.', '');

    if ($difference > $border) {
        echo "<TD class=fTable_1_3_t style='border-left:none; font-size:12pt;'  bgcolor='red'>". $testFmt ."</TD>";
        echo "<TD class=fTable_1_3_t style='font-size:10pt;' bgcolor='#CCFFCC'>". $test2Fmt."</TD>";
    } else {
        echo "<TD class=fTable_1_3_t style='border-left:none; font-size:12pt;'  bgcolor='#CCFFCC'>". $testFmt ."</TD>";
        echo "<TD class=fTable_1_3_t style='font-size:10pt;' bgcolor='#CCFFCC'>". $test2Fmt."</TD>";
    }
		?>
		</TR>
<?php
echo "<TD class=fTable_1_3_t style='border-left:none; font-size:12pt;'  bgcolor='#CCFFCC'>". $G1BFmt ."</TD>";
echo "<TD class=fTable_1_3_t style='font-size:10pt;' bgcolor='#CCFFCC'>". $G1B_plan_Fmt."</TD>";
?>
	</TABLE>


<!-- ASSEMBLY (F2 :...) -->
<?php
$AS_L = 1105 * $x;
$AS_T = 15 * $x;
$AS_H = 145 * $x;
$AS_W = 450 * $x;
$AS_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$AS_L}px; top: {$AS_T}px; height: {$AS_H}px; width: {$AS_W}px; border:solid Black 1px;\" cellpadding=\"0\" cellspacing=\"0\">";
  echo "<TR height=".$AS_height.">";
?>
    <TD class=fTable_1_1_h colspan=3>ASSEMBLY (N0)</TD>
  </TR>
  <TR height=10>
    <TD class=fTable_1_2_h width=33% style="border-left:none;">Capacity</TD>
    <TD class=fTable_1_2_h width=33%>Plan</TD>
    <TD class=fTable_1_2_h>Actual</TD>
  </TR>
  <TR>
    <TD class=fTable_1_3_h style="border-left:none;" bgcolor="#CCFFCC"><?php $test = number_format($fWorkTimeA/$ASSY_TT*60,0); if ($test == "") { echo 0;} else {echo $test; } ?></TD>
    <TD class=fTable_1_3_h bgcolor="#CCFFCC"><?php $test = $fPlanA; if ($test == "") { echo 0;} else {echo /*($WT_N0 == $fDef) ? $fPlanA : $WT_N0*/ $fPlanA; } ?></TD>
<?php
    $test = @$farray8["ACTPRD_LO"][0];
	$test2 = @$farray5["CUREXPRD_LO"][0];
	if (($test == "") && ($test2 == "")) {
    	echo "<TD class=fTable_1_3_h bgcolor=red>0</TD>";
    }	else {
		if ($test == "") {
  			echo "<TD class=fTable_1_3_h bgcolor=red>0</TD>";
		} else {	
			if ($test < $test2) {
//    			echo "<TD class=fTable_1_3 bgcolor=red>" . $farray8["ACTPRD_LO"][0] . "</TD>";
		  		echo "<TD class=fTable_1_3_h bgcolor='#CCFFCC'>" . $farray8["ACTPRD_LO"][0] . "</TD>";					
	  		} else {
		  		echo "<TD class=fTable_1_3_h bgcolor='#CCFFCC'>" . $farray8["ACTPRD_LO"][0] . "</TD>";
			}
		}
	}
?>
  </TR>
</TABLE>
<?php
$AS_L = 1325 * $x;
$AS_T = 470 * $x;
$AS_H = 60 * $x;
$AS_W = 200 * $x;
$AS_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$AS_L}px; top: {$AS_T}px; height: {$AS_H}px; width: {$AS_W}px; border:solid Black 1px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>	
  <TR height=10>
    <TD class=fTable_1_2_t style="border-left:none;">L/S(min)</TD>
    <TD class=fTable_1_2_t>L/A (%)</TD>
    <TD class=fTable_1_2_t>OT(min)</TD>
  </TR>
  <TR>
<?php 
    $test = @$farray5["LINESTS_ASY"][0];
  	switch($test) {
		  case "RUNNING": 
				$test2 = @$farray5["STPTIME_ASY"][0];
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_1_3_t style='border-left:none;' bgcolor='#CCFFCC'>0</TD>";
				} else {
		      echo "<TD class=fTable_1_3_t style='border-left:none;' bgcolor='#CCFFCC'>" . $farray5["STPTIME_ASY"][0] . "</TD>";				
				}
			  break;
		  case "ERRSTOP": 
				$test2 = @$farray5["STPTIME_ASY"][0];
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_1_3_t style='border-left:none;' bgcolor=red>0</TD>";
				} else {
		      echo "<TD class=fTable_1_3_t style='border-left:none;' bgcolor=red>" . $farray5["STPTIME_ASY"][0] . "</TD>";				
				}
			  break;				
  		default: 
				$test2 = @$farray5["STPTIME_ASY"][0];
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_1_3_t style='border-left:none;' bgcolor='#CCFFCC'>0</TD>";
				} else {
		      echo "<TD class=fTable_1_3_t style='border-left:none;' bgcolor='#CCFFCC'>" . $farray5["STPTIME_ASY"][0] . "</TD>";				
				}
			  break;	
		}
?>    
	<TD class=fTable_1_3_t bgcolor="#CCFFCC"><?php $test = number_format(min(99.9,($fWorkTimeA-$farray5["STPTIME_ASY"][0])/$fWorkTimeA*100),1); if ($test == "") { echo 0;} else {echo $test; } ?></TD>
    <TD class=fTable_1_3_t bgcolor="#CCFFCC"><?php $test = @$farray5["OVERTIME_ASY"][0]; if ($test == "") { echo 0;} else {echo $farray5["OVERTIME_ASY"][0]; } ?></TD>
  </TR>
</TABLE>
<!-- ASSY -->
<?php
$AS_L = 1110 * $x;
$AS_T = 200 * $x;
$AS_H = 100 * $x;
$AS_W = 140 * $x;
$AS_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$AS_L}px; top: {$AS_T}px; height: {$AS_H}px; width: {$AS_W}px; \" cellpadding=\"0\" cellspacing=\"0\">";
  echo "<TR height=".$AS_height.">";
?>
    <TD class=fTable_3_1 colspan=3>ASSY</TD>
  </TR>
  <TR height=10>
<?php
        $test = @$farray8["INVCUR_ASSY"][0];
		$test2 = @$farray9["COLMIN_ASSY"][0];
		$test3 = @$farray9["COLMAX_ASSY"][0];
		if ($test2 == "") {
		  $test2 = 0;
		}
		if ($test3 == "") {
		  $test3 = 0;
		}
    if ($test == "") {
  		echo "<TD class=fTable_3_2 colspan=3 bgcolor=red>0</TD>";
		} else {
		  if (($test < $test2) || ($test > $test3)) {
			  echo "<TD class=fTable_3_2 colspan=3 bgcolor=red>" . $farray8["INVCUR_ASSY"][0] . "</TD>";
			} else {
			  echo "<TD class=fTable_3_2 colspan=3 bgcolor=silver>" . $farray8["INVCUR_ASSY"][0] . "</TD>";
			}
		}
		
?>
  </TR>
  <TR height=10>
    <TD class=fTable_3_3 width=33% style="border-left:2.0pt solid white;"><?php $test = @$farray9["INVMIN_ASSY"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVMIN_ASSY"][0]; } ?></TD>
    <TD class=fTable_3_3 width=33%><?php $test = @$farray9["INVSTD_ASSY"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVSTD_ASSY"][0]; } ?></TD>
    <TD class=fTable_3_3><?php $test = @$farray9["INVMAX_ASSY"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVMAX_ASSY"][0]; } ?></TD>
  </TR>
</TABLE>
<!-- TRIM 37 -->
<?php
$AS_L = 970 * $x;
$AS_T = 30 * $x;
$AS_H = 290 * $x;
$AS_W = 50 * $x;
$AS_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$AS_L}px; top: {$AS_T}px; height: {$AS_H}px; width: {$AS_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
<?php
    $test = @$farray5["STPSTSF_TRIM"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_8 bgcolor=yellow>Full</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_8 bgcolor=white>Full</TD>";
		}
?>
  </TR>
  <TR>
<?php
    $test = @$farray5["STPSTSF_TRIM"][0]; 
		$test2 = @$farray5["STPTIMF_TRIM"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_9 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_9 bgcolor=yellow>" . $farray5["STPTIMF_TRIM"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_9 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_9 bgcolor=white>" . $farray5["STPTIMF_TRIM"][0] . "</TD>";
				}	
				break;
		}			
?>
  </TR>
  <TR>
<?php
    $test = @$farray5["STPSTSO_TRIM"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_8 bgcolor=yellow>Self</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_8 bgcolor=white>Self</TD>";
		}
?>
  </TR>
  <TR>
<?php
    $test = @$farray5["STPSTSO_TRIM"][0]; 
		$test2 = @$farray5["STPTIMO_TRIM"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_9 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_9 bgcolor=yellow>" . $farray5["STPTIMO_TRIM"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_9 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_9 bgcolor=white>" . $farray5["STPTIMO_TRIM"][0] . "</TD>";
				}	
				break;
		}			
?>
  </TR>
  <TR>
<?php
    $test = @$farray5["STPSTSS_TRIM"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_8 bgcolor=yellow>Short</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_8 bgcolor=white>Short</TD>";
		}
?>
  </TR>
  <TR>
<?php
    $test = @$farray5["STPSTSS_TRIM"][0]; 
		$test2 = @$farray5["STPTIMS_TRIM"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_9 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_9 bgcolor=yellow>" . $farray5["STPTIMS_TRIM"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_9 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_9 bgcolor=white>" . $farray5["STPTIMS_TRIM"][0] . "</TD>";
				}	
				break;
		}			
?>
  </TR>
  <TR>
<?php
$BO_PIC_H = 50 * $x;
$BO_PIC_W = 30 * $x;
    
		
		$test = @$farray5["LINESTS_TRIM"][0]; 
  	switch($test) {
		case "RUNNING": 
	   	  echo "<TD id=\"TRI\" class=fTable_2_1 bgcolor=00C131 style=\"\"><img alt=\"TR (1K)\" src=\"pictures/trim.gif\" height=".$BO_PIC_H." width=".$BO_PIC_W." /></TD>";
	   	  $_SESSION["TRI"] = MAX_TIME;
	      break;
  		case "PLNSTOP": 
	  	  echo "<TD id=\"TRI\" class=fTable_2_1 bgcolor=white style=\"\"><img alt=\"TR (1K)\" src=\"pictures/trim.gif\" height=".$BO_PIC_H." width=".$BO_PIC_W." /></TD>";
	  	  $_SESSION["TRI"] = MAX_TIME;
		  break;
  		default: 
	  	  echo "<TD id=\"TRI\" class=fTable_2_1 bgcolor=red style=\"\"><img alt=\"TR (1K)\" src=\"pictures/trim.gif\" height=".$BO_PIC_H." width=".$BO_PIC_W." /></TD>";
		  if ($_SESSION["TRI"] == MAX_TIME) $_SESSION["TRI"] = time();
	}  
?>
  </TR>
</TABLE>
<!-- CHASSIS -->
<?php
$AS_L = 1040 * $x;
$AS_T = 30 * $x;
$AS_H = 290 * $x;
$AS_W = 40 * $x;
$AS_height = 10 * $x;
$BO_PIC_H = 90 * $x;
$BO_PIC_W = 30 * $x;
echo "<TABLE style=\"position:absolute; left: {$AS_L}px; top: {$AS_T}px; height: {$AS_H}px; width: {$AS_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
  echo "<TR>";
    $test = @$farray5["LINESTS_CHAS"][0]; 
  	switch($test) {
		case "RUNNING": 
	   	  echo "<TD id=\"CHASS\" class=fTable_2_1 bgcolor=00C131 style=\"\"><img alt=\"CHS (1K)\" src=\"pictures/chassis.gif\" height=".$BO_PIC_H." width=".$BO_PIC_W." /></TD>";
	   	  $_SESSION["CHASS"] = MAX_TIME;
	      break;
  		case "PLNSTOP": 
	  	  echo "<TD id=\"CHASS\" class=fTable_2_1 bgcolor=white style=\"\"><img alt=\"CHS (1K)\" src=\"pictures/chassis.gif\" height=".$BO_PIC_H." width=".$BO_PIC_W." /></TD>";
	  	  $_SESSION["CHASS"] = MAX_TIME;
		  break;
  		default: 
	  	  echo "<TD id=\"CHASS\" class=fTable_2_1 bgcolor=red style=\"\"><img alt=\"CHS (1K)\" src=\"pictures/chassis.gif\" height=".$BO_PIC_H." width=".$BO_PIC_W." /></TD>";
		  if ($_SESSION["CHASS"] == MAX_TIME) $_SESSION["CHASS"] = time();
	}
?>
  </TR>
  <TR>
<?php
    $test = @$farray5["STPSTSS_CHAS"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_6 bgcolor=yellow>Short</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_6 bgcolor=white>Short</TD>";
		}
?>
  </TR>
  <TR>
<?php
    $test = @$farray5["STPSTSS_CHAS"][0]; 
		$test2 = @$farray5["STPTIMS_CHAS"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_7 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_7 bgcolor=yellow>" . $farray5["STPTIMS_CHAS"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_7 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_7 bgcolor=white>" . $farray5["STPTIMS_CHAS"][0] . "</TD>";
				}	
				break;
		}			
?>
  </TR>
  <TR>
<?php
    $test = @$farray5["STPSTSO_CHAS"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_6 bgcolor=yellow>Self</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_6 bgcolor=white>Self</TD>";
		}
?>
  </TR>
  <TR>
<?php
    $test = @$farray5["STPSTSO_CHAS"][0]; 
		$test2 = @$farray5["STPTIMO_CHAS"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_7 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_7 bgcolor=yellow>" . $farray5["STPTIMO_CHAS"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_7 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_7 bgcolor=white>" . $farray5["STPTIMO_CHAS"][0] . "</TD>";
				}	
				break;
		}			
?>
  </TR>
  <TR>
<?php
    $test = @$farray5["STPSTSF_CHAS"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_6 bgcolor=yellow>Full</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_6 bgcolor=white>Full</TD>";
		}
?>
  </TR>
  <TR>
<?php
    $test = @$farray5["STPSTSF_CHAS"][0]; 
		$test2 = @$farray5["STPTIMF_CHAS"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_7 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_7 bgcolor=yellow>" . $farray5["STPTIMF_CHAS"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_7 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_7 bgcolor=white>" . $farray5["STPTIMF_CHAS"][0] . "</TD>";
				}	
				break;
		}			
?>

  </TR>
</TABLE>
<!-- CHASSIS - FINAL 1 -->
<?php
$AS_L = 1110 * $x;
$AS_T = 335 * $x;
$AS_H = 100 * $x;
$AS_W = 110 * $x;
$AS_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$AS_L}px; top: {$AS_T}px; height: {$AS_H}px; width: {$AS_W}px; \" cellpadding=\"0\" cellspacing=\"0\">";
  echo "<TR height=".$AS_height.">";
?>
    <TD class=fTable_4_1 colspan=3>CHASS-FIN1</TD>
  </TR>
  <TR height=10>
<?php
    $test = @$farray5["INVCUR_CH_FI"][0];
		$test2 = @$farray9["COLMIN_CH_FI"][0];
		$test3 = @$farray9["COLMAX_CH_FI"][0];
		if ($test2 == "") {
		  $test2 = 0;
		}
		if ($test3 == "") {
		  $test3 = 0;
		}
    if ($test == "") {
  		echo "<TD class=fTable_4_2 colspan=3 bgcolor=red>0</TD>";
		} else {
		  if (($test < $test2) || ($test > $test3)) {
			  echo "<TD class=fTable_4_2 colspan=3 bgcolor=red>" . $farray5["INVCUR_CH_FI"][0] . "</TD>";
			} else {
			  echo "<TD class=fTable_4_2 colspan=3 bgcolor=silver>" . $farray5["INVCUR_CH_FI"][0] . "</TD>";
			}
		}
		
?>
  </TR>
  <TR height=10>
    <TD class=fTable_4_3 width=33% style="border-left:1.0pt solid black;"><?php $test = @$farray9["INVMIN_CH_FI"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVMIN_CH_FI"][0]; } ?></TD>
    <TD class=fTable_4_3 width=33%><?php $test = @$farray9["INVSTD_CH_FI"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVSTD_CH_FI"][0]; } ?></TD>
    <TD class=fTable_4_3><?php $test = @$farray9["INVMAX_CH_FI"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVMAX_CH_FI"][0]; } ?></TD>
  </TR>
</TABLE>
<!-- FINAL 2 -->
<?php
$AS_L = 1300 * $x;
$AS_T = 275 * $x;
$AS_H = 40 * $x;
$AS_W = 250 * $x;
$AS_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$AS_L}px; top: {$AS_T}px; height: {$AS_H}px; width: {$AS_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
<?php
	$test = @$farray5["STPSTSF_FIN2"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_4 bgcolor=yellow>Full</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_4 bgcolor=white>Full</TD>";
	}
	$test = @$farray5["STPSTSO_FIN2"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_4 bgcolor=yellow>Self</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_4 bgcolor=white>Self</TD>";
	}
    $test = @$farray5["STPSTSS_FIN2"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_4 bgcolor=yellow>Short</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_4 bgcolor=white>Short</TD>";
	}
	//---
    $test = @$farray5["LINESTS_FIN2"][0]; 
  	switch($test) {
		case "RUNNING": 
	   	  echo "<TD id=\"FIN2\" class=fTable_2_1 rowspan=2 bgcolor=00C131>FINAL 2</TD>";
	   	  $_SESSION["FIN2"] = MAX_TIME;
	      break;
  		case "PLNSTOP": 
	  	  echo "<TD id=\"FIN2\" class=fTable_2_1 rowspan=2 bgcolor=white>FINAL 2</TD>";
	  	  $_SESSION["FIN2"] = MAX_TIME;
		  break;
  		default: 
	  	  echo "<TD id=\"FIN2\" class=fTable_2_1 rowspan=2 bgcolor=red>FINAL 2</TD>";
		  if ($_SESSION["FIN2"] == MAX_TIME) $_SESSION["FIN2"] = time();
	}		
?>
  </TR>
  <TR>
<?php
	$test = @$farray5["STPSTSF_FIN2"][0]; 
	$test2 = @$farray5["STPTIMF_FIN2"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=yellow>" . $farray5["STPTIMF_FIN2"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=white>" . $farray5["STPTIMF_FIN2"][0] . "</TD>";
				}	
				break;
	}						 	
	
    $test = @$farray5["STPSTSO_FIN2"][0]; 
	$test2 = @$farray5["STPTIMO_FIN2"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=yellow>" . $farray5["STPTIMO_FIN2"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=white>" . $farray5["STPTIMO_FIN2"][0] . "</TD>";
				}	
				break;
	}			
    
    $test = @$farray5["STPSTSS_FIN2"][0]; 
	$test2 = @$farray5["STPTIMS_FIN2"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=yellow>" . $farray5["STPTIMS_FIN2"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_5 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_5 bgcolor=white>" . $farray5["STPTIMS_FIN2"][0] . "</TD>";
				}	
				break;
	}			
?>
  </TR>
</TABLE>
<!-- FINAL 1 -->
<?php
$AS_L = 1300 * $x;
$AS_T = 345 * $x;
$AS_H = 40 * $x;
$AS_W = 250 * $x;
$AS_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$AS_L}px; top: {$AS_T}px; height: {$AS_H}px; width: {$AS_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
<?php
    $test = @$farray5["LINESTS_FIN1"][0]; 
  	switch($test) {
		case "RUNNING": 
	   	  echo "<TD id=\"FIN1\" class=fTable_2_1 rowspan=2 bgcolor=00C131>FINAL 1</TD>";
	   	  $_SESSION["FIN1"] = MAX_TIME;
	      break;
  		case "PLNSTOP": 
	  	  echo "<TD id=\"FIN1\" class=fTable_2_1 rowspan=2 bgcolor=white>FINAL 1</TD>";
	  	  $_SESSION["FIN1"] = MAX_TIME;
		  break;
  		default: 
	  	  echo "<TD id=\"FIN1\" class=fTable_2_1 rowspan=2 bgcolor=red>FINAL 1</TD>";
		  if ($_SESSION["FIN1"] == MAX_TIME) $_SESSION["FIN1"] = time();
		}

    $test = @$farray5["STPSTSS_FIN1"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_2 bgcolor=yellow>Short</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_2 bgcolor=white>Short</TD>";
		}
		$test = @$farray5["STPSTSO_FIN1"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_2 bgcolor=yellow>Self</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_2 bgcolor=white>Self</TD>";
		}
		$test = @$farray5["STPSTSF_FIN1"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_2 bgcolor=yellow>Full</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_2 bgcolor=white>Full</TD>";
		}
?>
  </TR>
  <TR>
<?php
    $test = @$farray5["STPSTSS_FIN1"][0]; 
		$test2 = @$farray5["STPTIMS_FIN1"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=yellow>" . $farray5["STPTIMS_FIN1"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=white>" . $farray5["STPTIMS_FIN1"][0] . "</TD>";
				}	
				break;
		}			

    $test = @$farray5["STPSTSO_FIN1"][0]; 
		$test2 = @$farray5["STPTIMO_FIN1"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=yellow>" . $farray5["STPTIMO_FIN1"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=white>" . $farray5["STPTIMO_FIN1"][0] . "</TD>";
				}	
				break;
		}			
    
		$test = @$farray5["STPSTSF_FIN1"][0]; 
		$test2 = @$farray5["STPTIMF_FIN1"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=yellow>" . $farray5["STPTIMF_FIN1"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=white>" . $farray5["STPTIMF_FIN1"][0] . "</TD>";
				}	
				break;
		}						 	
?>
  </TR>
</TABLE>

<!-- SCP  LK 20151125 -->
<?php
$AS_L = 1325 * $x;
$AS_T = 422 * $x;
$AS_H = 40 * $x;
$AS_W = 80 * $x;
$AS_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$AS_L}px; top: {$AS_T}px; height: {$AS_H}px; width: {$AS_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
<?php


    $test = @$farray14["SCP_STATUS"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_2 bgcolor='#CCFFCC'>SCP</TD>";
			  break;				
			case "PLNSTOP": 
        echo "<TD class=fTable_2_2 bgcolor=white>SCP</TD>";
			  break;		
  		default: 
        echo "<TD class=fTable_2_2 bgcolor='#CCFFCC'>SCP</TD>";
		}
	
?>
  </TR>
  <TR>
<?php
    $test = @$farray14["SCP_STATUS"][0]; 
		$test2 = @$farray14["SCP_TOTAL"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor='#CCFFCC'>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor='#CCFFCC'>" . $farray14["SCP_TOTAL"][0] . "</TD>";
				}	
			  break;		
      case "PLNSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor=white>" . $farray14["SCP_TOTAL"][0] . "</TD>";
				}	
			  break;			
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_3 bgcolor='#CCFFCC'>0</TD>";
				} else {
				  echo "<TD class=fTable_2_3 bgcolor='#CCFFCC'>" . $farray14["SCP_TOTAL"][0] . "</TD>";
				}	
				break;
		}			

   						 	
?>
  </TR>
</TABLE>

<!-- N0 - INSPECTION - - NEW 2006 01 04 - Bukvald -->
<?php
$AS_L = 1210 * $x;
$AS_T = 440 * $x;
$AS_H = 100 * $x;
$AS_W = 110 * $x;
$AS_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$AS_L}px; top: {$AS_T}px; height: {$AS_H}px; width: {$AS_W}px; \" cellpadding=\"0\" cellspacing=\"0\">";
  echo "<TR height=".$AS_height.">";
?>
    <TD class=fTable_4_1 colspan=3>N0-INSP</TD>
  </TR>
  <TR height=10>
<?php
    $test = @$farray5["INVCUR_N0INSP"][0];
	$test2 = @$farray9["COLMIN_N0INSP"][0];
	$test3 = @$farray9["COLMAX_N0INSP"][0];
	if ($test2 == "") {
	  $test2 = 0;
	}
	if ($test3 == "") {
	  $test3 = 0;
	}
    if ($test == "") {
  		echo "<TD class=fTable_4_2 colspan=3 bgcolor=red>0</TD>";
	} else {
	  	if (($test < $test2) || ($test > $test3)) {
		 	echo "<TD class=fTable_4_2 colspan=3 bgcolor=red>" . $farray5["INVCUR_N0INSP"][0] . "</TD>";
		} else {
			echo "<TD class=fTable_4_2 colspan=3 bgcolor=silver>" . $farray5["INVCUR_N0INSP"][0] . "</TD>";
		}
	}
?>
  </TR>
  <TR height=10>
    <TD class=fTable_4_3 width=33% style="border-left:1.0pt solid black;"><?php $test = @$farray9["INVMIN_N0INSP"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVMIN_N0INSP"][0]; } ?></TD>
    <TD class=fTable_4_3 width=33%><?php $test = @$farray9["INVSTD_N0INSP"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVSTD_N0INSP"][0]; } ?></TD>
    <TD class=fTable_4_3><?php $test = @$farray9["INVMAX_N0INSP"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVMAX_N0INSP"][0]; } ?></TD>
  </TR>
</TABLE>
	
<!-- WINDOW 4 FINISH-->
		
<!-- WINDOW 5 -->
<!-- QUALITY CONTROL(SALES:BUY-OFF) -->
<?php
$AS_L = 1110 * $x;
$AS_T = 580 * $x;
$AS_H = 145 * $x;
$AS_W = 450 * $x;
$AS_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$AS_L}px; top: {$AS_T}px; height: {$AS_H}px; width: {$AS_W}px; border:solid Black 1px;\" cellpadding=\"0\" cellspacing=\"0\">";
  echo "<TR height=".$AS_height.">";
?>
    <TD class=fTable_1_1_h colspan=3 height=15>QUALITY CONTROL(R0)</TD>
  </TR>
  <TR height=10>
    <TD class=fTable_1_2_h width=33% style="border-left:none;">Capacity</TD>
<!--    <TD class=fTable_1_2_h width=33% style="border-left:none;color:#CCFFCC;">Plan Shift</TD>-->
    <TD class=fTable_1_2_h width=33%>Plan</TD>
    <TD class=fTable_1_2_h>Actual</TD>
  </TR>
  <TR>	
    <TD class=fTable_1_3_h style="border-left:none;" bgcolor="#CCFFCC"><?php $test = number_format($fWorkTimeQ/$QC_TT*60,0); if ($test == "") { echo 0;} else {echo $test; } ?></TD>
<!--   ohajo <TD class=fTable_1_3_h style="border-left:none; color=#CCFFCC;" bgcolor="#CCFFCC"><?php $test = @$farray7["PLNPRDSH_BO"][0]; if ($test == "") { echo 0;} else {echo $farray7["PLNPRDSH_BO"][0]; } ?></TD>-->
    <TD class=fTable_1_3_h bgcolor="#CCFFCC"><?php $test = $fPlanQ; if ($test == "") { echo 0;} else {echo $fPlanQ; } ?></TD>
<!--    <TD class=fTable_1_3_h bgcolor="#CCFFCC"><?php $test = @$farray5["CUREXPRD_BO"][0]; if ($test == "") { echo 0;} else {echo $farray5["CUREXPRD_BO"][0]; } ?></TD>-->
<?php
    $test = @$farray8["ACTPRD_VC"][0];
	$test2 = @$farray5["CUREXPRD_BO"][0];
	if (($test == "") && ($test2 == "")) {
    	echo "<TD class=fTable_1_3_h bgcolor=red>0</TD>";
    }	else {
		  if ($test == "") {
			  echo "<TD class=fTable_1_3_h bgcolor=red>0</TD>";
			} else {
  		  if ($test < $test2) {
			    echo "<TD class=fTable_1_3_h bgcolor='#CCFFCC'>" . $farray8["ACTPRD_VC"][0] . "</TD>";
//    			echo "<TD class=fTable_1_3 bgcolor=red>" . $farray8["ACTPRD_VC"][0] . "</TD>";
		  	} else {
			    echo "<TD class=fTable_1_3_h bgcolor='#CCFFCC'>" . $farray8["ACTPRD_VC"][0] . "</TD>";
   			}
			}
			
		}
?>
  </TR>
</TABLE>
<?php
$AS_L = 1290 * $x;
//$AS_L = 1188 * $x;
$AS_T = 735 * $x;
$AS_H = 60 * $x;
$AS_W = 155 * $x;
$AS_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$AS_L}px; top: {$AS_T}px; height: {$AS_H}px; width: {$AS_W}px; border:solid Black 1px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR height=10>
    <TD class=fTable_1_2_t>L/A (%)</TD>
    <TD class=fTable_1_2_t>OT(min)</TD>
  </TR>
  <TR>
	<TD class=fTable_1_3_t bgcolor="#CCFFCC"><?php $test = number_format(min(99.9,($fWorkTimeQ-($farray5["STPTIMS_AINS"][0]+$farray5["STPTIMO_AINS"][0]+$farray5["STPTIMF_AINS"][0]))/$fWorkTimeQ*100),1); if ($test == "") { echo 0;} else {echo $test; } ?></TD>
<!--  ohajo  <TD class=fTable_1_3_t bgcolor="#CCFFCC">NIC</TD>-->
    <TD class=fTable_1_3_t bgcolor="#CCFFCC"><?php $test = @$farray6["OVERTIME_QC"][0]; if ($test == "") { echo 0;} else {echo $farray6["OVERTIME_QC"][0]; } ?></TD>
  </TR>
</TABLE>

<?php

//<!-- LINE OFF - BUY OFF -->
$AS_L = 1170 * $x;
$AS_T = 1035 * $x;
$AS_H = 70 * $x;
$AS_W = 100 * $x;
$AS_height = 7 * $x;
$AS_font = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$AS_L}px; top: {$AS_T}px; height: {$AS_H}px; width: {$AS_W}px; \" cellpadding=\"0\" cellspacing=\"0\">";
  echo "<TR height=".$AS_height.">";
    echo "<TD class=fTable_3_1 colspan=3	style=\"font-size:".$AS_font."pt;\">LineOff-BuyOff</TD>";
?>
  </TR>
  <TR height=10>
<?php
        $test = @$farray8["INVCUR_QCSAL"][0];
		$test2 = @$farray9["COLMIN_QCSAL"][0];
		$test3 = @$farray9["COLMAX_QCSAL"][0];
		if ($test2 == "") {
		  $test2 = 0;
		}
		if ($test3 == "") {
		  $test3 = 0;
		}
    if ($test == "") {
  		echo "<TD class=fTable_3_2 colspan=3 bgcolor=red>0</TD>";
		} else {
		  if (($test < $test2) || ($test > $test3)) {
			  echo "<TD class=fTable_3_2 colspan=3 bgcolor=red>" . $farray8["INVCUR_QCSAL"][0] . "</TD>";
			} else {
			  echo "<TD class=fTable_3_2 colspan=3 bgcolor=silver>" . $farray8["INVCUR_QCSAL"][0] . "</TD>";
			}
		}
		
?>
  </TR>
  <TR height=10>
    <TD class=fTable_3_3 width=33% style="border-left:2.0pt solid white;"><?php $test = @$farray9["INVMIN_QCSAL"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVMIN_QCSAL"][0]; } ?></TD>
    <TD class=fTable_3_3 width=33%><?php $test = @$farray9["INVSTD_QCSAL"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVSTD_QCSAL"][0]; } ?></TD>
    <TD class=fTable_3_3><?php $test = @$farray9["INVMAX_QCSAL"][0]; if ($test == "") { echo 0;} else {echo $farray9["INVMAX_QCSAL"][0]; } ?></TD>
  </TR>
</TABLE>

<!-- QC SHOP REPAIR -->
<?php
$AS_L = 1460 * $x;
//$AS_L = 1395 * $x;
$AS_T = 730 * $x;
$AS_H = 80 * $x;
$AS_W = 100 * $x;
//$AS_W = 165 * $x;
$AS_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$AS_L}px; top: {$AS_T}px; height: {$AS_H}px; width: {$AS_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
    <TD class=fTable_5_1 colspan=2 >QC REPAIR<BR> TOTAL</TD>
  </TR>
  <TR bgcolor="#00CCFF">
<?php  
//    $test = @$farray8["INVCUR_INVES"][0];
  	$test2 = @$farray9["INVMAX_QRPAL2"][0];
  	$test3 = @$farray9["COLMAX_QRPAL2"][0];
	$SUMA = $farray8["INVCUR_INVES"][0] + $farray8["INVCUR_MAJRP"][0] + $farray8["INVCUR_ASYRP"][0] + $farray8["INVCUR_BDYRP"][0] + $farray8["INVCUR_PNTRP"][0] + $farray8["INVCUR_CONF"][0] + $farray8["INVCUR_WAIT"][0];
	$SUMA_G1B = $farray8["INVCUR_INVES_G1B"][0] + $farray8["INVCUR_MAJRP_G1B"][0] + $farray8["INVCUR_ASYRP_G1B"][0] + $farray8["INVCUR_BDYRP_G1B"][0] + $farray8["INVCUR_PNTRP_G1B"][0] + $farray8["INVCUR_CONF_G1B"][0] + $farray8["INVCUR_WAIT_G1B"][0];
    if ($test == "") {
  		echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>0</TD>";
        echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor=red>($SUMA_G1B)</BR></TD>";
        echo "</TR>";
		if ($test2 == "") {
        	echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/0</BR></TD>";
		} else {
			echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/" . $farray9["INVMAX_QRPAL2"][0] . "</BR></TD>";
		}
	} else {
		if ($test2 == "") { 
			echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>" . $SUMA . "</TD>";
            echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor=red>($SUMA_G1B)</BR></TD>";
            echo "</TR>";
        	echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>0</BR></TD>";
		} else {
        	if ($SUMA > $test3)	{		
  				echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>" . $SUMA . "</BR></TD>";
                echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor=red>($SUMA_G1B)</BR></TD>";
                echo "</TR>";
          		echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/" . $farray9["INVMAX_QRPAL2"][0] . "</BR></TD>";				
			} else {
				echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white; ' bgcolor='#00CCFF'>" . $SUMA . "</BR></TD>";
                echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor='#00CCFF'>($SUMA_G1B)</BR></TD>";
                echo "</TR>";
          		echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor='#00CCFF'>/" . $farray9["INVMAX_QRPAL2"][0] . "</BR></TD>";					
			}
		}		
	}
?>

  </TR>
</TABLE>

<!-- 48 -->
<?php
$AS_L = 1375 * $x;
$AS_T = 820 * $x;
$AS_H = 70 * $x;
$AS_W = 90 * $x;
$AS_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$AS_L}px; top: {$AS_T}px; height: {$AS_H}px; width: {$AS_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
    <TD class=fTable_5_1 colspan=2 >Invstg.</TD>
  </TR>
  <TR bgcolor="#00CCFF">
<?php  
    $test = @$farray8["INVCUR_INVES"][0];
    $test_G1B = @$farray8["INVCUR_INVES_G1B"][0];
    $test2 = @$farray9["INVMAX_INVES"][0];
  	$test3 = @$farray9["COLMAX_INVES"][0];
		
	if ($test == "") { 
  		echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>0</TD>";
        echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor=red>($test_G1B)</BR></TD>";
        echo "</TR>";
		if ($test2 == "") {
        	echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/0</BR></TD>";
		} else {
			echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/" . $farray9["INVMAX_INVES"][0] . "</BR></TD>";
		}
	} else {
		if ($test2 == "") { 
			echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>" . $farray8["INVCUR_INVES"][0] . "</TD>";
            echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor=red>($test_G1B)</BR></TD>";
            echo "</TR>";
        	echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>0</BR></TD>";
		} else {
        	if ($test > $test3)	{		
  				echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>" . $farray8["INVCUR_INVES"][0] . "</BR></TD>";
                echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor=red>($test_G1B)</BR></TD>";
                echo "</TR>";
          		echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/" . $farray9["INVMAX_INVES"][0] . "</BR></TD>";
			} else {
				echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor='#00CCFF'>" . $farray8["INVCUR_INVES"][0] . "</BR></TD>";
                echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor='#00CCFF'>($test_G1B)</BR></TD>";
                echo "</TR>";
          		echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor='#00CCFF'>/" . $farray9["INVMAX_INVES"][0] . "</BR></TD>";
			}
		}		
	}
?>

  </TR>
</TABLE>
<!-- 49 -->
<?php
$AS_L = 1375 * $x;
$AS_T = 900 * $x;
$AS_H = 70 * $x;
$AS_W = 90 * $x;
$AS_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$AS_L}px; top: {$AS_T}px; height: {$AS_H}px; width: {$AS_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
    <TD class=fTable_5_1 colspan=2 style="">Assy</TD>
  </TR>
  <TR bgcolor="#00CCFF">
<?php  
		$test = $farray8["INVCUR_ASYRP"][0] ;
  	$test2 = @$farray9["INVMAX_ASYRP"][0];
  	$test3 = @$farray9["COLMAX_ASYRP"][0];
  	$test_G1B = $farray8["INVCUR_ASYRP_G1B"][0] ;
	if ($test == "") { 
  		echo "<TD  rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>0</TD>";
        echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor=red>($test_G1B)</BR></TD>";
        echo "</TR>";
		if ($test2 == "") {
        	echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/0</BR></TD>";
		} else {
			echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/" . $farray9["INVMAX_ASYRP"][0] . "</BR></TD>";
		}
	} else {
		if ($test2 == "") { 
			echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>" . $test . "</TD>";
            echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor=red>($test_G1B)</BR></TD>";
            echo "</TR>";
            echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>0</BR></TD>";
		} else {
        	if ($test > $test3)	{		
  				echo "<TD  rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>" . $test . "</BR></TD>";
                echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor=red>($test_G1B)</BR></TD>";
                echo "</TR>";
                echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/" . $farray9["INVMAX_ASYRP"][0] . "</BR></TD>";
			} else {
				echo "<TD  rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor='#00CCFF'>" . $test . "</BR></TD>";
                echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor='#00CCFF'>($test_G1B)</BR></TD>";
                echo "</TR>";
          		echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor='#00CCFF'>/" . $farray9["INVMAX_ASYRP"][0] . "</BR></TD>";					
			}
		}		
	}
?>
  </TR>
</TABLE>
<!-- 50 -->
<?php
$AS_L = 1375 * $x;
$AS_T = 980 * $x;
$AS_H = 70 * $x;
$AS_W = 90 * $x;
$AS_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$AS_L}px; top: {$AS_T}px; height: {$AS_H}px; width: {$AS_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
    <TD class=fTable_5_1 colspan=2 style="">Body</TD>
  </TR>
  <TR bgcolor="#00CCFF">
<?php  
    $test = @$farray8["INVCUR_BDYRP"][0];
    $test_G1B = @$farray8["INVCUR_BDYRP_G1B"][0];
  	$test2 = @$farray9["INVMAX_BDYRP"][0];
  	$test3 = @$farray9["COLMAX_BDYRP"][0];
		
	if ($test == "") { 
  		echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>0</TD>";
        echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor=red>($test_G1B)</BR></TD>";
        echo "</TR>";
		if ($test2 == "") {
        	echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/0</BR></TD>";
		} else {
			echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/" . $farray9["INVMAX_BDYRP"][0] . "</BR></TD>";
		}
	} else {
		if ($test2 == "") { 
			echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>" . $farray8["INVCUR_BDYRP"][0] . "</TD>";
            echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor=red>($test_G1B)</BR></TD>";
            echo "</TR>";
        	echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>0</BR></TD>";
		} else {
        	if ($test > $test3)	{		
  				echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>" . $farray8["INVCUR_BDYRP"][0] . "</BR></TD>";
                echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor=red>($test_G1B)</BR></TD>";
                echo "</TR>";
          		echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/" . $farray9["INVMAX_BDYRP"][0] . "</BR></TD>";
			} else {
				echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor='#00CCFF'>" . $farray8["INVCUR_BDYRP"][0] . "</BR></TD>";
                echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor='#00CCFF'>($test_G1B)</BR></TD>";
                echo "</TR>";
          		echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor='#00CCFF'>/" . $farray9["INVMAX_BDYRP"][0] . "</BR></TD>";
			}
		}		
	}
?>

  </TR>
  </TABLE>
<!-- 51 -->
<?php
$AS_L = 1375 * $x;
$AS_T = 1060 * $x;
$AS_H = 70 * $x;
$AS_W = 90 * $x;
$AS_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$AS_L}px; top: {$AS_T}px; height: {$AS_H}px; width: {$AS_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
<!--    <TD class=fTable_5_1 colspan=2 style="">Paint</TD>-->
    <TD class=fTable_5_1 colspan=2 style="">Audit</TD>
  </TR>
  <TR bgcolor="#00CCFF">
<?php

function Void($var)
{
    if (empty($var) === true)
    {
        if (($var === 0) || ($var === '0'))
        {
            return false;
        }

        return true;
    }

    return false;
}

    $test = $farray8["INVCUR_AUDIT"][0] + $farray8["INVCUR_AUDRP"][0];
    $test_G1B = $farray8["INVCUR_AUDIT_G1B"][0] + $farray8["INVCUR_AUDRP_G1B"][0];
//    $test = 0;
  	$test2 = @$farray9["INVMAX_AUDIT_X0"][0];
  	$test3 = @$farray9["COLMAX_AUDIT_X0"][0];
		
	if ( Void($test) ) {
  		echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>0</TD>";
        echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor=red>($test_G1B)</BR></TD>";
        echo "</TR>";
		if ($test2 == "") {
        	echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/0</BR></TD>";
		} else {
			echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/" . $farray9["INVMAX_AUDIT_X0"][0] . "</BR></TD>";
		}
	} else {
		if ($test2 == "") { 
			echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>" . $test . "</TD>";
            echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor=red>($test_G1B)</BR></TD>";
            echo "</TR>";
        	echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>0</BR></TD>";
		} else {
        	if ($test > $test3)	{		
  				echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>" . $test . "</BR></TD>";
                echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor=red>($test_G1B)</BR></TD>";
                echo "</TR>";
          		echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/" . $farray9["INVMAX_AUDIT_X0"][0] . "</BR></TD>";
			} else {
				echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor='#00CCFF'>" . $test . "</BR></TD>";
                echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor='#00CCFF'>($test_G1B)</BR></TD>";
                echo "</TR>";
          		echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor='#00CCFF'>/" . $farray9["INVMAX_AUDIT_X0"][0] . "</BR></TD>";
			}
		}		
	}
?>

  </TR>
</TABLE>
<!-- 52 -->
<?php
$AS_L = 1469 * $x;
$AS_T = 820 * $x;
$AS_H = 70 * $x;
$AS_W = 90 * $x;
$AS_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$AS_L}px; top: {$AS_T}px; height: {$AS_H}px; width: {$AS_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
    <TD class=fTable_5_1 colspan=2 style="">Conf.</TD>
  </TR>
  <TR bgcolor="#00CCFF">
<?php  
    $test = @$farray8["INVCUR_CONF"][0];
    $test_G1B = @$farray8["INVCUR_CONF_G1B"][0];
    $test2 = @$farray9["INVMAX_CONF"][0];
  	$test3 = @$farray9["COLMAX_CONF"][0];
		
	if ($test == "") {
        echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>0</TD>";
        echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor=red>($test_G1B)</BR></TD>";
        echo "</TR>";
		if ($test2 == "") {
        	echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/0</BR></TD>";
		} else {
			echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/" . $farray9["INVMAX_CONF"][0] . "</BR></TD>";
		}
	} else {
		if ($test2 == "") { 
			echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>" . $farray8["INVCUR_CONF"][0] . "</TD>";
            echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor=red>($test_G1B)</BR></TD>";
            echo "</TR>";
            echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>0</BR></TD>";
		} else {
        	if ($test > $test3)	{		
  				echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>" . $farray8["INVCUR_CONF"][0] . "</BR></TD>";
                echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor=red>($test_G1B)</BR></TD>";
                echo "</TR>";
                echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/" . $farray9["INVMAX_CONF"][0] . "</BR></TD>";
			} else {
				echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor='#00CCFF'>" . $farray8["INVCUR_CONF"][0] . "</BR></TD>";
                echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor='#00CCFF'>($test_G1B)</BR></TD>";
                echo "</TR>";
          		echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor='#00CCFF'>/" . $farray9["INVMAX_CONF"][0] . "</BR></TD>";
			}
		}		
	}
?>

  </TR>
</TABLE>
<!-- 53 -->
<?php
$AS_L = 1469 * $x;
$AS_T = 900 * $x;
$AS_H = 70 * $x;
$AS_W = 90 * $x;
$AS_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$AS_L}px; top: {$AS_T}px; height: {$AS_H}px; width: {$AS_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
    <TD class=fTable_5_1 colspan=2 style="">QCE Wait.</TD>
  </TR>
  <TR bgcolor="#00CCFF">
<?php  
    $test = @$farray8["INVCUR_WAIT"][0];
    $test_G1B = @$farray8["INVCUR_WAIT_G1B"][0];
    $test2 = @$farray9["INVMAX_WAIT"][0];
  	$test3 = @$farray9["COLMAX_WAIT"][0];
		
	if ($test == "") { 
  		echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>0</TD>";
        echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor=red>($test_G1B)</BR></TD>";
        echo "</TR>";
		if ($test2 == "") {
        	echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/0</BR></TD>";
		} else {
			echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/" . $farray9["INVMAX_WAIT"][0] . "</BR></TD>";
		}
	} else {
		if ($test2 == "") { 
			echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>" . $farray8["INVCUR_WAIT"][0] . "</TD>";
            echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor=red>($test_G1B)</BR></TD>";
            echo "</TR>";
        	echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>0</BR></TD>";
		} else {
        	if ($test > $test3)	{		
  				echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>" . $farray8["INVCUR_WAIT"][0] . "</BR></TD>";
                echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor=red>($test_G1B)</BR></TD>";
                echo "</TR>";
          		echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/" . $farray9["INVMAX_WAIT"][0] . "</BR></TD>";
			} else {
				echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor='#00CCFF'>" . $farray8["INVCUR_WAIT"][0] . "</BR></TD>";
                echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor='#00CCFF'>($test_G1B)</BR></TD>";
                echo "</TR>";
          		echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor='#00CCFF'>/" . $farray9["INVMAX_WAIT"][0] . "</BR></TD>";
			}
		}		
	}
?>

  </TR>
</TABLE>

<!-- ASSEMBLY INSPECTION -->
<?php
$AS_L = 1220 * $x;
$AS_T = 740 * $x;
$AS_H = 220 * $x;
$AS_W = 50 * $x;
$AS_height = 10 * $x;
$AS_PIC_H = 110 * $x;
$AS_PIC_W = 30 * $x;
echo "<TABLE style=\"position:absolute; left: {$AS_L}px; top: {$AS_T}px; height: {$AS_H}px; width: {$AS_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
  echo "<TR>";
    $test = @$farray5["LINESTS_AINS"][0]; 
  	switch($test) {
		case "RUNNING": 
	   	  echo "<TD id=\"ASS_I\" class=fTable_2_1 bgcolor=00C131 style=\"\"><img alt=\"AI (1K)\" src=\"pictures/assy.gif\" height=".$AS_PIC_H." width=".$AS_PIC_W." /></TD>";
	   	  $_SESSION["ASS_I"] = MAX_TIME;
	      break;
  		case "PLNSTOP": 
	  	  echo "<TD id=\"ASS_I\" class=fTable_2_1 bgcolor=white style=\"\"><img alt=\"AI (1K)\" src=\"pictures/assy.gif\" height=".$AS_PIC_H." width=".$AS_PIC_W." /></TD>";
	  	  $_SESSION["ASS_I"] = MAX_TIME;
		  break;
  		default: 
	  	  echo "<TD id=\"ASS_I\" class=fTable_2_1 bgcolor=red style=\"\"><img alt=\"AI (1K)\" src=\"pictures/assy.gif\" height=".$AS_PIC_H." width=".$AS_PIC_W." /></TD>";
		  if ($_SESSION["ASS_I"] == MAX_TIME) $_SESSION["ASS_I"] = time();
	}
?>
  </TR>
  <TR>
<?php
    $test = @$farray5["STPSTSS_AINS"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_6 bgcolor=yellow>Short</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_6 bgcolor=white>Short</TD>";
		}
?>
  </TR>
  <TR>
<?php
    $test = @$farray5["STPSTSS_AINS"][0]; 
		$test2 = @$farray5["STPTIMS_AINS"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_7 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_7 bgcolor=yellow>" . $farray5["STPTIMS_AINS"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_7 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_7 bgcolor=white>" . $farray5["STPTIMS_AINS"][0] . "</TD>";
				}	
				break;
		}			
?>
  </TR>
  <TR>
<?php
    $test = @$farray5["STPSTSO_AINS"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_6 bgcolor=yellow>Self</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_6 bgcolor=white>Self</TD>";
		}
?>
  </TR>
  <TR>
<?php
    $test = @$farray5["STPSTSO_AINS"][0]; 
		$test2 = @$farray5["STPTIMO_AINS"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_7 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_7 bgcolor=yellow>" . $farray5["STPTIMO_AINS"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_7 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_7 bgcolor=white>" . $farray5["STPTIMO_AINS"][0] . "</TD>";
				}	
				break;
		}			
?>
  </TR>
  <TR>
<?php
    $test = @$farray5["STPSTSF_AINS"][0]; 
  	switch($test) {
		  case "ERRSTOP": 
        echo "<TD class=fTable_2_6 bgcolor=yellow>Full</TD>";
			  break;				
  		default: 
        echo "<TD class=fTable_2_6 bgcolor=white>Full</TD>";
		}
?>
  </TR>
  <TR>
<?php
    $test = @$farray5["STPSTSF_AINS"][0]; 
		$test2 = @$farray5["STPTIMF_AINS"][0];
  	switch($test) {
		  case "ERRSTOP": 	
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_7 bgcolor=yellow>0</TD>";
				} else {
				  echo "<TD class=fTable_2_7 bgcolor=yellow>" . $farray5["STPTIMF_AINS"][0] . "</TD>";
				}	
			  break;				
  		default:
				if ($test2 == "")	{ 
				  echo "<TD class=fTable_2_7 bgcolor=white>0</TD>";
				} else {
				  echo "<TD class=fTable_2_7 bgcolor=white>" . $farray5["STPTIMF_AINS"][0] . "</TD>";
				}	
				break;
		}			
?>
  </TR>
</TABLE>		
<!-- VEHICLE PERFORMANCE Insp. -->
<?php
$AS_L = 1165 * $x;
$AS_T = 800 * $x;
$AS_H = 200 * $x;
$AS_W = 50 * $x;
$AS_height = 10 * $x;
$AS_PIC_H = 100 * $x;
$AS_PIC_W = 30 * $x;
echo "<TABLE style=\"position:absolute; left: {$AS_L}px; top: {$AS_T}px; height: {$AS_H}px; width: {$AS_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
  echo "<TR>";
    echo "<TD class=fTable_2_1 bgcolor=00C131 style=\"\"><img alt=\"VP2 (1K)\" src=\"pictures/vpi.gif\" height=".$AS_PIC_H." width=".$AS_PIC_W." /></TD>";
?>
  </TR>
</TABLE>
<!-- SHOWER TESTER -->
<?php
$AS_L = 1110 * $x;
$AS_T = 740 * $x;
$AS_H = 120 * $x;
$AS_W = 50 * $x;
$AS_height = 10 * $x;
$AS_PIC_H = 90 * $x;
$AS_PIC_W = 30 * $x;
echo "<TABLE style=\"position:absolute; left: {$AS_L}px; top: {$AS_T}px; height: {$AS_H}px; width: {$AS_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
  echo "<TR>";
    echo "<TD class=fTable_2_1 bgcolor=00C131 style=\"\"><img alt=\"ST (1K)\" src=\"pictures/shower.gif\" height=".$AS_PIC_H." width=".$AS_PIC_W." /></TD>";
?>
  </TR>
</TABLE>
<!-- SALES -->
<?php
$AS_L = 1110 * $x;
$AS_T = 915 * $x;
$AS_H = 110 * $x;
$AS_W = 50 * $x;
$AS_height = 10 * $x;
$AS_PIC_H = 70 * $x;
$AS_PIC_W = 30 * $x;
echo "<TABLE style=\"position:absolute; left: {$AS_L}px; top: {$AS_T}px; height: {$AS_H}px; width: {$AS_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
  echo "<TR>";
    echo "<TD class=fTable_2_1 bgcolor=00C131 style=\"\"><img alt=\"SL (1K)\" src=\"pictures/sales.gif\" height=".$AS_PIC_H." width=".$AS_PIC_W." /></TD>";
?>
  </TR>
</TABLE>
<?php
$AS_L = 1110 * $x;
$AS_T = 1035 * $x;
$AS_H = 50 * $x;
$AS_W = 50 * $x;

?>
<!-- Buyoff Stop -->
<?php
$BS_L = 1100 * $x;
$BS_T = 1095 * $x;
$BS_H = 45 * $x;
$BS_W = 70 * $x;
$status = @$farray9["BUYOFF_STATUS"][0];
$strStopFrom = @$farray9["BUYOFF_STOP_FROM"][0];
if ($status != "RUNNING") {
//    $input = '06/10/2011 19:00:02';
//    $input = substr($strStopFrom,4,2)."/".substr($strStopFrom,6,2)."/".substr($strStopFrom,0,4)." ".substr($strStopFrom,8,2).":".substr($strStopFrom,10,2).":00";
    $dateStopFrom = date_create_from_format('YmdHi', $strStopFrom);
//    $dateStopFrom = date_create_from_format('YmdHi', '202012010000');
    $dateStopTo = new DateTime("now");
    $interval = date_diff($dateStopFrom, $dateStopTo);
//    $date = strtotime($input);
//    echo date('d/M/Y h:i:s', $date);
    if ( $status == "STOP") {
        echo "<IMG src=\"./pictures/buyoffR.gif\" style=\"position:absolute; left: {$AS_L}px; top: {$AS_T}px; height: {$AS_H}px; width: {$AS_W}px;\">";
    } else {
        echo "<IMG src=\"./pictures/buyoffY.gif\" style=\"position:absolute; left: {$AS_L}px; top: {$AS_T}px; height: {$AS_H}px; width: {$AS_W}px;\">";
    }
    echo "<div id=BuyOffStop style=\"background:gray; font-size: 12px; text-align:left; position:absolute; left: {$BS_L}px; top: {$BS_T}px; height: {$BS_H}px; width: {$BS_W}px;\">&nbsp; STOP<BR>".$interval->format('%ad %hh %im');
    echo "</div>";
} else {
    echo "<IMG src=\"./pictures/buyoffG.gif\" style=\"position:absolute; left: {$AS_L}px; top: {$AS_T}px; height: {$AS_H}px; width: {$AS_W}px;\">";
    echo "<div id=BuyOffStop style=\"background:gray; color:gray; font-size: 8px; text-align:left; position:absolute; left: {$BS_L}px; top: {$BS_T}px; height: {$BS_H}px; width: {$BS_W}px;\">&nbsp; RUNNING<BR>0d 0h 0m";
    echo "</div>";
}

//<!-- 45 -->
$AS_L = 1469 * $x;
$AS_T = 980 * $x;
$AS_H = 70 * $x;
$AS_W = 90 * $x;
echo "<TABLE style=\"position:absolute; left: {$AS_L}px; top: {$AS_T}px; height: {$AS_H}px; width: {$AS_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
    <TD class=fTable_5_1 colspan=2 style="">Major</TD>
  </TR>
  <TR bgcolor="#00CCFF">
<?php  
    $test = @$farray8["INVCUR_MAJRP"][0];
    $test_G1B = @$farray8["INVCUR_MAJRP_G1B"][0];
  	$test2 = @$farray9["INVMAX_MAJRP"][0];
  	$test3 = @$farray9["COLMAX_MAJRP"][0];
		
	if ($test == "") { 
  		echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>0</TD>";
        echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor=red>($test_G1B)</BR></TD>";
        echo "</TR>";
		if ($test2 == "") {
        	echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/0</BR></TD>";
		} else {
			echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/" . $farray9["INVMAX_MAJRP"][0] . "</BR></TD>";
		}
	} else {
		if ($test2 == "") { 
			echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>" . $farray8["INVCUR_MAJRP"][0] . "</TD>";
            echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor=red>($test_G1B)</BR></TD>";
            echo "</TR>";
        	echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>0</BR></TD>";
		} else {
        	if ($test > $test3)	{		
  				echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>" . $farray8["INVCUR_MAJRP"][0] . "</BR></TD>";
                echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor=red>($test_G1B)</BR></TD>";
                echo "</TR>";
          		echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/" . $farray9["INVMAX_MAJRP"][0] . "</BR></TD>";
			} else {
				echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor='#00CCFF'>" . $farray8["INVCUR_MAJRP"][0] . "</BR></TD>";
                echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor='#00CCFF'>($test_G1B)</BR></TD>";
                echo "</TR>";
          		echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor='#00CCFF'>/" . $farray9["INVMAX_MAJRP"][0] . "</BR></TD>";
			}
		}		
	}
?>

   </TR>
</TABLE>

<!-- Z0-->
<?php
$PA_L = 1469 * $x;
$PA_T = 1060 * $x;
$PA_H = 70 * $x;
$PA_W = 90 * $x;
$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
<TR>
<!--    <TD class=fTable_5_1 style="">C-Pillar</TD>-->
    <TD class=fTable_5_1 colspan=2 style="">Paint</TD>
</TR>
<TR bgcolor="#00CCFF">
    <?php
    $test =  $farray8["INVCUR_PNTRP"][0] ;
//    $test = @$farray8["INVCUR_MAJRP"][0];
    $test2 = @$farray9["INVMAX_PNTRP"][0];
    $test3 = @$farray9["COLMAX_PNTRP"][0];
    $test_G1B = $farray8["INVCUR_PNTRP_G1B"][0] ;

    if ($test == "") {
        echo "<TD rowspan='2'  class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>0</TD>";
        echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor=red>($test_G1B)</BR></TD>";
        echo "</TR>";
        if ($test2 == "") {
            echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/0</BR></TD>";
        } else {
            echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/" . $farray9["INVMAX_PNTRP"][0] . "</BR></TD>";
        }
    } else {
        if ($test2 == "") {
            echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>" . $test . "</TD>";
            echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor=red>($test_G1B)</BR></TD>";
            echo "</TR>";
            echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>0</BR></TD>";
        } else {
            if ($test > $test3)	{
                echo "<TD rowspan='2'  class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>" . $test . "</BR></TD>";
                echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor=red>($test_G1B)</BR></TD>";
                echo "</TR>";
                echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/" . $farray9["INVMAX_PNTRP"][0] . "</BR></TD>";
            } else {
                echo "<TD rowspan='2'  class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor='#00CCFF'>" . $test . "</BR></TD>";
                echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor='#00CCFF'>($test_G1B)</BR></TD>";
                echo "</TR>";
                echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor='#00CCFF'>/" . $farray9["INVMAX_PNTRP"][0] . "</BR></TD>";
            }
        }
    }
    ?>
</TR>
</TABLE>





<!-- 55 -->
<?php
/*
$AS_L = 1469 * $x;
$AS_T = 1060 * $x;
$AS_H = 70 * $x;
$AS_W = 90 * $x;
echo "<TABLE style=\"position:absolute; left: {$AS_L}px; top: {$AS_T}px; height: {$AS_H}px; width: {$AS_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
*/
?>
<!--
  <TR>
    <TD class=fTable_5_1 colspan=2 style="">Audit</TD>
  </TR>
  <TR bgcolor="#00CCFF">
-->
<?php  
/*
    $test = (@$farray8["INVCUR_AUDIT"][0]+$farray8["INVCUR_AUDRP"][0]);
  	$test2 = @$farray9["INVMAX_AUDIT"][0];
  	$test3 = @$farray9["COLMAX_AUDIT"][0];
		
	if ($test == "") { 
  		echo "<TD class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>0</TD>";
		if ($test2 == "") {
        	echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/0</BR></TD>";
		} else {
			echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/" . $farray9["INVMAX_AUDIT"][0] . "</BR></TD>";
		}
	} else {
		if ($test2 == "") { 
			echo "<TD class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>" . (@$farray8["INVCUR_AUDIT"][0]+$farray8["INVCUR_AUDRP"][0]) . "</TD>";
        	echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>0</BR></TD>";
		} else {
        	if ($test > $test3)	{		
  				echo "<TD class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>" . (@$farray8["INVCUR_AUDIT"][0]+$farray8["INVCUR_AUDRP"][0]) . "</BR></TD>";
          		echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/" . $farray9["INVMAX_WAIT"][0] . "</BR></TD>";				
			} else {
				echo "<TD class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor='#00CCFF'>" . (@$farray8["INVCUR_AUDIT"][0]+$farray8["INVCUR_AUDRP"][0]) . "</BR></TD>";
          		echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor='#00CCFF'>/" . $farray9["INVMAX_AUDIT"][0] . "</BR></TD>";					
			}
		}		
	}
*/
?>
<!--
  </TR>
</TABLE>  
-->
<?php
$BO01_L = 1245 * $x;
$BO01_T = 1007 * $x;
$BO01_H = 10 * $x;
$BO01_W = 2 * $x;

$BO02_L = 1190 * $x;
$BO02_T = 1015 * $x;
$BO02_H = 2 * $x;
$BO02_W = 55 * $x;

$BO03_L = 1190 * $x;
$BO03_T = 1000 * $x;
$BO03_H = 15 * $x;
$BO03_W = 2 * $x;
echo "<hr style=\"position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px;\" noshade color=black>";
echo "<hr style=\"position:absolute; left: {$BO02_L}px; top: {$BO02_T}px; height: {$BO02_H}px; width: {$BO02_W}px; \" noshade color=black>";
echo "<hr style=\"position:absolute; left: {$BO03_L}px; top: {$BO03_T}px; height: {$BO03_H}px; width: {$BO03_W}px;\" noshade color=black>";

$BO01_L = 1190 * $x;
$BO01_T = 730 * $x;
$BO01_H = 70 * $x;
$BO01_W = 2 * $x;

$BO02_L = 1130 * $x;
$BO02_T = 730 * $x;
$BO02_H = 2 * $x;
$BO02_W = 60 * $x;

$BO03_L = 1130 * $x;
$BO03_T = 730 * $x;
$BO03_H = 12 * $x;
$BO03_W = 2 * $x;
//<hr style="position:absolute; left:1200px; top:600px; height:100px; width: 2px;" noshade color=black>
//<hr style="position:absolute; left:1200px; top:700px; height:20px; width: 2px;" noshade color=black>
echo "<div style=\"background:black; position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px;\"></div>";
echo "<hr style=\"position:absolute; left: {$BO02_L}px; top: {$BO02_T}px; height: {$BO02_H}px; width: {$BO02_W}px; \" noshade color=black>";
echo "<hr style=\"position:absolute; left: {$BO03_L}px; top: {$BO03_T}px; height: {$BO03_H}px; width: {$BO03_W}px;\" noshade color=black>";

$BO01_L = 1130 * $x;
$BO01_T = 860 * $x;
$BO01_H = 54 * $x;
$BO01_W = 2 * $x;
echo "<hr style=\"position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px;\" noshade color=black>";

$BO01_L = 1094 * $x;
$BO01_T = 865 * $x;
$BO01_H = 2 * $x;
$BO01_W = 35 * $x;

$BO02_L = 1094 * $x;
$BO02_T = 955 * $x;
$BO02_H = 2 * $x;
$BO02_W = 35 * $x;
//echo "<hr style=\"position:absolute; left: {$BO01_L}px; top: {$BO01_T}px; height: {$BO01_H}px; width: {$BO01_W}px; \" noshade color=black>";
//echo "<hr style=\"position:absolute; left: {$BO02_L}px; top: {$BO02_T}px; height: {$BO02_H}px; width: {$BO02_W}px; \" noshade color=black>";

//echo "<div ID=\"note1\" style=\"color:white; background:black; position:absolute; left: 10}px; top: 110}px; height: 20}px; width: 200}px;\"></div>";

?>
<!-- WINDOW 5 FINISH-->

<?php

//Specialky pro CCR
/*
$ips = array('CCR' => '172.16.16.26', 
             'Michal Smitka' => '172.16.18.50', 
						 'Cesta' => '172.16.18.15');

$users = array('CCR' => 'tpca\ccr', 
             'Michal Smitka' => 'tpca\smitka', 
						 'Cesta' => 'tpca\socha');
*/
//$ips = array("172.16.18.16", "172.16.18.50", "172.16.16.26");
//if (array_search($_SERVER['REMOTE_ADDR'], $ips)=='CCR') {

//if (array_search($_SERVER['REMOTE_ADDR'], $ips)) {
//if (array_search(strtolower($_SERVER['REMOTE_USER']), $users)) {
?>

<?php
		//Tondovo ;-) 
		$PA_L = 685 * $x;
		$PA_T = 705 * $x;
		$PA_H = 55 * $x;
		$PA_W = 50 * $x;
		$PA_height = 10 * $x;
		echo "<!-- <TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px; border:solid Black 1px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
		  <TR class=fTable_1_3>
		    <TD bgcolor=silver style="width:200px;border-left:0pt;">LC</TD>
		  </TR>
		</TABLE> -->

<?php
		// WT smycka - paint
		$PA_L = 560 * $x;
		$PA_T = 695 * $x;
		$PA_H = 70 * $x;
		$PA_W = 95 * $x;
		$PA_height = 10 * $x;
		echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px; border:solid Black 1px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
		  <TR height=10>
		    <TD class=fTable_1_1_h style="font-size:9;border-bottom:0pt solid black;">W EMPTY</TD>
		  </TR>
		  <TR>
                <?php
                    $test = @$farray4["WT"][0]-$farray4["INVCUR_BTOP"][0];
                    $test2 = @$farray9["COLMIN_WEMPTY"][0];
                    $test3 = @$farray9["COLMAX_WEMPTY"][0];
                    if ($test2 == "") {
                        $test2 = 0;
                    }
                    if ($test3 == "") {
                        $test3 = 0;
                    }

                    if ($test == "") {
                        echo "<TD id=\"WEMPTY\" class=fTable_1_3_t bgcolor=silver style='width:100px;border-left:0pt;'>". 0 . "</TD>";
                    } else {
                        if (($test < $test2) || ($test > $test3)) {
                            echo "<TD id=\"WEMPTY\" class=fTable_1_3_t colspan=3 bgcolor=red style='width:100px;border-left:0pt;'>" . $test . "</TD>";
                            if ($_SESSION["WEMPTY"] == MAX_TIME) $_SESSION["WEMPTY"] = time();
                        } else {
                            echo "<TD id=\"WEMPTY\" class=fTable_1_3_t colspan=3 bgcolor=silver style='width:100px;border-left:0pt;'>" . $test . "</TD>";
                            $_SESSION["WEMPTY"] = MAX_TIME;
                        }
                    }

                ?>
		  </TR>
		</TABLE>
		
<?php
   if (1==2) { 
		// Hodnoty z lakovny - BB, Primer, CSS, TC
		//.SPARE_10 as PRM_TOTAL, s4.SPARE_11 as PRM_BB, s4.SPARE_12 as TC_TOTAL, s4.SPARE_13 as CSS
    $PA_L = 658 * $x;
		$PA_T = 695 * $x;
		$PA_H = 70 * $x;
		$PA_W = 200 * $x;
		$PA_height = 10 * $x;
		echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px; border:solid Black 1px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>

style="width:1400px;border-left:0pt;filter: alpha(opacity=40);"
		  <TR height=10>
		    <TD class=fTable_1_1_h style="font-size:9;border-bottom:0pt solid black;border-right:1pt solid black;">BB</TD>
		    <TD class=fTable_1_1_h style="font-size:9;border-bottom:0pt solid black;border-right:1pt solid black;">Prm</TD>
		    <TD class=fTable_1_1_h style="font-size:9;border-bottom:0pt solid black;border-right:1pt solid black;">CSS</TD>
		    <TD class=fTable_1_1_h style="font-size:9;border-bottom:0pt solid black;">TC</TD>
		  </TR>
		  <TR>
		    <TD class=fTable_1_3_t bgcolor=silver style="width:25px;border-left:0pt;border-right:1pt solid black;"><?php $test = @$farray4["PRM_BB"][0]; if ($test == "") { echo 0;} else {echo $farray4["PRM_BB"][0]; } ?></TD>
		    <TD class=fTable_1_3_t bgcolor=silver style="width:25px;border-left:0pt;border-right:1pt solid black;"><?php $test = @$farray4["PRM_TOTAL"][0]; if ($test == "") { echo 0;} else {echo $farray4["PRM_TOTAL"][0]; } ?></TD>
		    <TD class=fTable_1_3_t bgcolor=silver style="width:25px;border-left:0pt;border-right:1pt solid black;"><?php $test = @$farray4["CSS"][0]; if ($test == "") { echo 0;} else {echo $farray4["CSS"][0]; } ?></TD>
		    <TD class=fTable_1_3_t bgcolor=silver style="width:25px;border-left:0pt;">                             <?php $test = @$farray4["TC_TOTAL"][0]; if ($test == "") { echo 0;} else {echo $farray4["TC_TOTAL"][0]; } ?></TD>
		  </TR>
		</TABLE>
<?php
}
?>

		
<!-- REPAIRED-->
<?php
$PA_L = 320 * $x;
$PA_T = 1060 * $x;
$PA_H = 70 * $x;
$PA_W = 80 * $x;
$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
    <TD class=fTable_5_1 colspan=2 style="">Repaired </TD>
  </TR>
  <TR bgcolor="#00CCFF">
<?php  
	$test = $farray8["INVCUR_REPVEH"][0] ;
//  	$test2 = @$farray9["INVMAX_PFRP"][0];
//  	$test3 = @$farray9["COLMAX_PFRP"][0];
    $test_G1B = $farray8["INVCUR_REPVEH_G1B"][0];
		
	if ($test == "") { 
			echo "<TD  rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>/" . $farray8["INVCUR_REPVEH"][0] . "</BR></TD>";
            echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor=red>($test_G1B)</BR></TD></TR>";
            echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red></BR></TD>";

    } else {
			echo "<TD  rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white; ' bgcolor='#00CCFF'>" . $farray8["INVCUR_REPVEH"][0] . "</BR></TD>";
            echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor='#00CCFF'>($test_G1B)</BR></TD></TR>";
            echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor='#00CCFF'></BR></TD>";
    }
				
	
?>
  </TR>
</TABLE>

<!-- OK OFFLINE-->
<?php
$PA_L = 410 * $x;
$PA_T = 1060 * $x;
$PA_H = 70 * $x;
$PA_W = 80 * $x;
$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
    <TD class=fTable_5_1 style="">OK</TD>
  </TR>
  <TR bgcolor="#00CCFF">
<?php  


$finalV5 = (@$farray8["INVCUR_PFRP"][0]-@$farray4["OK_OFF"][0]);
$OK = $farray4["OK_OFF"][0];
	if ((0-$finalV5)>0){
  $test = $OK - (0-$finalV5);
  } else {
  $test = $OK;
  }
  
  //$test = $farray4["OK_OFF"][0] ;
//  	$test2 = @$farray9["INVMAX_PFRP"][0];
//  	$test3 = @$farray9["COLMAX_PFRP"][0];
		
	if ($test == "" && $test <> 0) { 
			echo "<TD class=fTable_5_2 style='border-right:1.0pt solid white;' bgcolor=red>" . $test . "</BR></TD>";
		
	} else {
				echo "<TD class=fTable_5_2 style='border-left:1.0pt solid white; border-right:1.0pt solid white; text-align:center;' bgcolor='#00CCFF'>" . $test . "</BR></TD>";
			}
				
	
?>
  </TR>
</TABLE>




		



<?php
   // SCP Alert
  
	if ($farray14["SCP_STATUS"][0]=='ERRSTOP') {
		$PA_L = 1110 * $x;
		$PA_T = 270 * $x;
		$PA_H = 60 * $x;
		$PA_W = 140 * $x;
		$PA_height = 10 * $x;
		echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px; border:solid Black 1px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
		  <TR class=fTable_1_3>
					<TD bgcolor=red style="width:1400px;border-left:0pt;">
					<P>SCP</P>					
				
					</TD>
			</TR>
		</TABLE>
		<?php
	};
	
//};
?>	

<!-- emergancy parking-->
<?php
$PA_L = 1290 * $x;
$PA_T = 805 * $x;
$PA_H = 65 * $x;
$PA_W = 70 * $x;
$PA_height = 5 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
    <TD class=fTable_5_1 colspan=2 style="">EP1</TD>
  </TR>
  <TR bgcolor="#FF0000">
<?php  
	$test = $farray8["INVCUR_X1"][0] ;
    $test_G1B = $farray8["INVCUR_X1_G1B"][0] ;
	$test_G1B_string = ($test>99) ? '' : "(".$test_G1B.")";

	if ($test == "" or $test <> 0) { 
		echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>" . $test . "</BR></TD>";
        echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor=red>". $test_G1B_string ."</BR></TD></TR>";
        echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red></BR></TD>";
	} else {
		echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor='#00CCFF'>" . $test . "</BR></TD>";
        echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor='#00CCFF'>". 	$test_G1B_string	."</BR></TD></TR>";
        echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor='#00CCFF'></BR></TD>";
	}
			
?>
  </TR>
</TABLE>	


<!-- AUDIT -->
<!-- EP2 -->
<?php
$PA_L = 1290 * $x;
$PA_T = 875 * $x;
$PA_H = 65 * $x;
$PA_W = 70 * $x;
$PA_height = 5 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
    <TD class=fTable_5_1 colspan=2 style="">EP2</TD>
  </TR>
  <TR bgcolor="#FF0000">
<?php  
	$test = (@$farray8["INVCUR_Z1"][0]);
	$test_G1B = (@$farray8["INVCUR_Z1_G1B"][0]);
	$test_G1B_string = ($test>99) ? '' : "(".$test_G1B.")";

if ($test == "" or $test <> 0) {
    echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>" . $test . "</BR></TD>";
	echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor=red>". $test_G1B_string ."</BR></TD></TR>";
	echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red></BR></TD>";
} else {
    echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor='#00CCFF'>" . $test . "</BR></TD>";
	echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor='#00CCFF'>". 	$test_G1B_string	."</BR></TD></TR>";
	echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor='#00CCFF'></BR></TD>";
}
?>

  </TR>
</TABLE>
<!-- EP2 -->
<!-- AUDIT -->
<?php
$PA_L = 1290 * $x;
$PA_T = 945 * $x;
$PA_H = 65 * $x;
$PA_W = 70 * $x;
$PA_height = 5 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
        <TD class=fTable_5_1 colspan=2 style="">Test<BR>Track</TD>
  </TR>
  <TR bgcolor="#FF0000">
<?php  
    $test = (@$farray8["INVCUR_Z2"][0]);
	$test_G1B = (@$farray8["INVCUR_Z2_G1B"][0]);
	$test_G1B_string = ($test>99) ? '' : "(".$test_G1B.")";

	if ($test == "" or $test <> 0) {
		echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>" . $test . "</BR></TD>";
		echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor=red>". $test_G1B_string ."</BR></TD></TR>";
		echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red></BR></TD>";
	} else {
		echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor='#00CCFF'>" . $test . "</BR></TD>";
		echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white; border-bottom: 0pt solid;' bgcolor='#00CCFF'>". 	$test_G1B_string	."</BR></TD></TR>";
		echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor='#00CCFF'></BR></TD>";
	}
?>

  </TR>


</TABLE>	

<!--------------------------------------------------------------------------------------------------------------------->
<!-- ACCESSORIES-->
<?php
$AC_L = 1290 * $x;
$AC_T = 1035 * $x;
$AC_H = 65 * $x;
$AC_W = 70 * $x;
$AC_height = 5 * $x;
echo "<TABLE style=\"position:absolute; left: {$AC_L}px; top: {$AC_T}px; height: {$AC_H}px; width: {$AC_W}px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
  <TR>
        <TD class=fTable_5_1 colspan=2 style="">Accesso.</TD>
  </TR>
  <TR bgcolor="#FF0000">
<?php 
		$test = @$farray8["INVCUR_Z0"][0];
    $test_G1B = @$farray8["INVCUR_Z0_G1B"][0];
  	$test2 = @$farray9["INVMAX_Z0"][0];
  	$test3 = @$farray9["COLMAX_Z0"][0];
		
	if ($test == "") { 
  		echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>0</TD>";

        echo "</TR>";
		if ($test2 == "") {
        	echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/0</BR></TD>";
		} else {
			echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/" . $farray9["INVMAX_Z0"][0] . "</BR></TD>";
		}
	} else {
		if ($test2 == "") { 
			echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>" . $farray8["INVCUR_Z0"][0] . "</TD>";

            echo "</TR>";
        	echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>0</BR></TD>";
		} else {
        	if ($test > $test3)	{		
  				echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor=red>" . $farray8["INVCUR_Z0"][0] . "</BR></TD>";

                echo "</TR>";
          		echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor=red>/" . $farray9["INVMAX_Z0"][0] . "</BR></TD>";
			} else {
				echo "<TD rowspan='2' class=fTable_5_2 style='border-left:1.0pt solid white;' bgcolor='#00CCFF'>" . $farray8["INVCUR_Z0"][0] . "</BR></TD>";

                echo "</TR>";
          		echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor='#00CCFF'>/" . $farray9["INVMAX_Z0"][0] . "</BR></TD>";
			}
		}		
	}

    echo '<TR bgcolor="#FF0000">';
    echo "<TD rowspan='2' class=fTable_5_3 style='border-left:1.0pt solid white; border-right:none' bgcolor='#00CCFF'>act</BR>" . $farray8["INVCUR_ACCESS_REAL"][0] . "</BR></TD>";
    echo "<TD class=fTable_5_3 style='border-right:1.0pt solid white;' bgcolor='#00CCFF'>/pln</BR>/" . $farray8["INVCUR_ACCESS_PLAN"][0] . "</BR></TD>";
    echo "<TR>";
?>

   </TR>

</TABLE>



<!--------------------------------------------------------------------------------------------------------------------->
<?php


if (!empty($farray13['PTNAME'][0])){
$farray13[0];
    $PA_L = 1100 * $x;
		$PA_T = 580 * $x;
		$PA_H = 550 * $x;
		$PA_W = 470 * $x;
		$PA_height = 10 * $x;
echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px; border:solid Black 1px;\" cellpadding=\"0\" cellspacing=\"0\">";


$pocetRadku=count($farray13);
$pocetRadku=$frows13;
/*echo "<p> v poli je pocet tiskaren: $pocetRadku</p>";*/

?>
		  <TR class=fTable_6_3>
					<TD bgcolor=black style="width:1400px;border-left:0pt;filter: alpha(opacity=40);">
					<U>Pokayoke Printers</U>
          <p>delka fronty je nastavena na: <?php echo $farray14["DELKAFRONTY"][0]?></p>
					 <p>zkontroluj stav terminálu na TC01</p>
          <p> tiskarna: </p>
          <?php 
          for ($i=0;$i<$pocetRadku;$i++) {
          echo $farray13["PTNAME"][$i];
          echo "</br>";
           /*echo"<p>$farray13["PTNAME"]</p> $i";
           
           <? echo $farray13["PTNAME"][0]?>
           */
            }
          ?>					
</td>
			</TR>
			
</table>
?>
<?php
	};
?>

<?php

if ($history === 'Y') {
//    oracle_logon($conn, "LINEMON", "LINEMON", "CVQSPROD");
    oracle_logon($conn, "LINEMON", "LINEMON", "CVQSPROD");
// ******* NEW HISTORY FUNCTIONALITY BEGIN *******
$HIS_L = 400 * $x;
$HIS_T = 1160 * $x;
$HIS_H = 10 * $x;
$HIS_W = 660 * $x;

echo "<div align=right style=\"background-color: transparent; position:absolute; left: $HIS_L" . "px; top: $HIS_T" . "px; height: $HIS_H" . "px; width: $HIS_W" . "px;\">";
//echo "<div align=right style=\"background-color: red; position:absolute; left: $HIS_Lpx; top: $HIS_Tpx; height: $HIS_Hpx; width: $HIS_Wpx;\">";
if (isset($fTime)) {
    //echo $fquery;
    echo "<TABLE><TR><TD valign=top>";
    //echo "<B>".$farray["TIME"][0]."</B>";
    echo "</TD><TD>";
    echo "<FORM ACTION=\"plmsh.php\" METHOD=\"POST\">";
    echo "<INPUT TYPE=\"SUBMIT\" NAME=\"fHistory\" VALUE=\" NEW \" CLASS=\"button1\">";
    echo "</FORM>";
    echo "</TD></TR></TABLE>";
} else if (isset($fYear) && isset($fMonth) && isset($fDay)) {
    $mtime = mktime(0, 0, 0, $fMonth, $fDay, $fYear);
    $fHisTimequery = "select to_char(DATE_STAMP,'YYYYMMDDHH24MISS') as TIME, to_char(DATE_STAMP,'YYYY/MM/DD HH24:MI:SS') as TIME2 from T_HIS_STAMP_BLKG where to_char(DATE_STAMP,'YYYYMMDD') = '" . date("Ymd", $mtime) . "' order by DATE_STAMP";
    // echo $fHisTimequery;
    $fHisTimearray = array();
    $fHisTimerows = 0;
    oracle_select($conn, $fHisTimequery, $fHisTimearray, $fHisTimerows);
    echo "<TABLE><TR><TD valign=top>";
    //echo "<B>".$farray["TIME"][0]."</B>";
    echo "</TD><TD valign=top>";
    echo "<FORM ACTION=\"plmsh.php\" METHOD=\"POST\">";
    echo "<SELECT NAME=\"fTime\" SIZE=\"1\" CLASS=\"button1\">";
    if ($fHisTimerows == 0)
        echo "<OPTION VALUE = \"\">NO RECORDS";
    for ($i = 0; $i < $fHisTimerows; $i++) {
        echo "<OPTION VALUE = " . $fHisTimearray["TIME"][$i];
        //if ($fYear == $i) echo "SELECTED";
        echo ">" . $fHisTimearray["TIME2"][$i];
    }
    echo "</SELECT>";
    echo "</TD><TD>";
    if ($fHisTimerows != 0)
        echo "<INPUT TYPE=\"SUBMIT\" NAME=\"fHistory\" VALUE=\"   GO   \" CLASS=\"button1\">";
    echo "</FORM>";
    echo "</TD><TD>";
    echo "<FORM ACTION=\"plmsh.php\" METHOD=\"POST\">";
    echo "<INPUT TYPE=\"SUBMIT\" NAME=\"fHistory\" VALUE=\" BACK \" CLASS=\"button1\">";
    echo "</FORM>";
    echo "</TD></TR></TABLE>";
} else {
    if (!isset($fYear))
        $fYear = date("Y", $mtime);
    if (!isset($fMonth))
        $fMonth = date("m", $mtime);
    if (!isset($fDay))
        $fDay = date("d", $mtime);
    
    echo "<FORM ACTION=\"plmsh.php\" METHOD=\"POST\">";
    echo "<TABLE><TR><TD>";
    echo "<SELECT NAME=\"fYear\" SIZE=\"1\" CLASS=\"button1\">";
    
    for ($i = date("Y", time()) - 1; $i <= date("Y", time()); $i++) {
        echo "<OPTION VALUE = $i ";
        if ($fYear == $i)
            echo "SELECTED";
        echo ">$i";
    }

    echo "</SELECT>";
    echo "<SELECT NAME=\"fMonth\" SIZE=\"1\" CLASS=\"button1\">";

    for ($i = 1; $i <= 12; $i++) {
        echo "<OPTION VALUE = $i ";
        if ($fMonth == $i)
            echo "SELECTED";
        echo ">$i";
    }
    
    echo "</SELECT>";
    echo "<SELECT NAME=\"fDay\" SIZE=\"1\" CLASS=\"button1\">";
    
    for ($i = 1; $i <= 31; $i++) {
        echo "<OPTION VALUE = $i ";
        if ($fDay == $i)
            echo "SELECTED";
        echo ">$i";
    }

    echo "</SELECT>";
    echo "</TD><TD>";
    echo "<INPUT TYPE=\"SUBMIT\" NAME=\"fHistory\" VALUE=\"   GO   \" CLASS=\"button1\">";
    echo "</TD></TR></TABLE>";
    echo "</FORM>";

}
echo "</div>";


// Datum a cas uprostred Paintu, vlevo od selektoru datumu
$HIS2_L = 400 * $x;
$HIS2_T = 1160 * $x;
$HIS2_H = 10 * $x;
$HIS2_W = 230 * $x;
echo "<div align=left style=\"background-color: #AAAAAA; position:absolute; left: $HIS2_L" . "px; top: $HIS2_T" . "px; height: $HIS2_H" . "px; width: $HIS2_W" . "px;\">";

echo "<B>&nbsp" . $farray["TIME"][0] . "</B>";
echo "</div>";

    oracle_logoff($conn);
// ******* NEW HISTORY FUNCTIONALITY *******
}
?>

<?php

if ($history === 'N') {

// ******* CCR alerts **********    
    
//$HOD_L = 1300 * $x;
//$HOD_T = 165 * $x;
//$HOD_H = 75 * $x;
//$HOD_W = 250 * $x;

   // Pokayoke na fixovani sequence
   if (date("Hi")>="0130" && date("Hi")<="0230"  ) {
//   if (2 == 3) {
	//$a = 1;
	//if ($a == 1) {
	if ($farray11["MAX_LO_DATE"][0]<>$farray11["VLT_MAX_DATE"][0]) {
		$PA_L = 1300 * $x;
		$PA_T = 160 * $x;
		$PA_H = 75 * $x;
		$PA_W = 260 * $x;
		$PA_height = 10 * $x;
		echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px; border:solid Black 1px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
		  <TR class=.fccrnote>
					<TD bgcolor=red style="width:1400px;border-left:0pt;">
					<p>
<U>VLT Pokayoke</U><BR>
Nefixovali jste sequenci<BR>
ALC date: <? echo $farray11["MAX_LO_DATE"][0]?><BR>
PPOC date: <? echo $farray11["VLT_MAX_DATE"][0]?></p>
					</TD>
			</TR>
		</TABLE>
		<?php
	};
	};
//};
?>

<?php
  // Extra Upozornìní na nízký TA buffer
	//$a = 1;
	//if ($a == 1) {
	if ($farray8["INVCUR_PTOA"][0] <= $farray9["TA_BUFF_ACTION"][0]) {
		$PA_L = 25 * $x;
		$PA_T = 290 * $x;
		$PA_H = 100 * $x;
		$PA_W = 100 * $x;
		$PA_height = 10 * $x;
		echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px; border:solid Black 1px;\" cellpadding=\"0\" cellspacing=\"0\">";
?>
		  <TR class=.fccrnote>
					<TD class="blink" style="width:1400px;border-left:0pt;text-align: center;">
			<!--		<TD style="width:1400px;border-left:0pt;background-color: rgba(255, 0, 0, 0.7);">   -->
      <!--		<TD bgcolor=red style="width:1400px;border-left:0pt;filter: alpha(opacity=55);">   -->
					<U>Low T-A Buffer</U>
					</TD>
			</TR>
		</TABLE>
		<?php
	};
//};
    // Extra Upozornění na nízký počet aut v seqenci
    //$a = 1;
    //if ($a == 1) {
    if ($farray8["INVCUR_BSEQ"][0] <= $farray9["BSEQ_ACTION"][0]) {
        $PA_L = 25 * $x;
        $PA_T = 290 * $x;
        $PA_H = 100 * $x;
        $PA_W = 100 * $x;
        $PA_height = 10 * $x;
        echo "<TABLE style=\"position:absolute; left: {$PA_L}px; top: {$PA_T}px; height: {$PA_H}px; width: {$PA_W}px; border:solid Black 1px;\" cellpadding=\"0\" cellspacing=\"0\">";
        ?>
        <TR class=.fccrnote>
            <TD class="blink" style="width:1400px;border-left:0pt;text-align: center;">
                <!--		<TD style="width:1400px;border-left:0pt;background-color: rgba(255, 0, 0, 0.7);">   -->
                <!--		<TD bgcolor=red style="width:1400px;border-left:0pt;filter: alpha(opacity=55);">   -->
                <U>Low B/S data</U>
            </TD>
        </TR>
        </TABLE>
        <?php
    };
//};


        
// ******* CCR alerts **********         
}        
?>

</body>
</html>


<?php require('config.php'); ?>

<div class="fleft top10header" style='margin-top:10px;margin-bottom:10px;'>
	<div style='font-weight: bold'>
		<span style='color:rgb(0,84,130)'>Ana</span><span style='color:rgb(237,28,36); font-style:italic'>Log</span><span style='color:rgb(0,84,130)'>i</span>
	</div>
	<div class='tiny'>
		<span class="tiny">a&#183;nal&#183;o&#183;gi [uh-nal-uh-jee] noun. a similarity between like features of two things, on which a comparison may be based</span>
	</div>
</div>


<div class="fright" style='margin-top:10px;margin-bottom:10px;' >
	<?php
	if(count($glb_ossecdb)>1){
		echo "
		<form action='./index.php'>
			<select name='glb_ossecdb' onchange='document.cookie=\"ossecdbjs=\"+glb_ossecdb.options[selectedIndex].value ; location.reload(true)'>";

			foreach ($glb_ossecdb as $name => $file){
				if($_COOKIE['ossecdbjs'] == $name){
					$glb_ossecdb_selected=" SELECTED ";
				}else{
					$glb_ossecdb_selected="";
				}
				$glb_ossecdb_option.="<option value='".$name."' ".$glb_ossecdb_selected." >".$name." (".DB_NAME_O.", ".DB_HOST_O.")</option>";
			}
			echo $glb_ossecdb_option;
		echo "</select>
		</form>";
	}
	?>
</div>


<script src="./scripts/jquery.min.js" type="text/javascript"></script>
<script src="./scripts/jquery.cookie.js" type="text/javascript"></script>
<script type="text/javascript">
	var WALLBOARD_MODE_ON=1;
	var WALLBOARD_MODE_OFF=0;	

	var current_wallboard_mode=$.cookie('wallboard_mode');
	
	function set_wallboard_mode(mode){
		$.cookie('wallboard_mode', mode, {expires: 20});
		location.reload();
	}

</script>
<div class="fright" style='margin-top:15px;margin-bottom:10px;margin-right:30px;' >
	<a class='tiny tinyblack' href='./index.php'>Index</a>
	<a class='tiny tinyblack' href='./newsfeed.php'>NewsFeed</a>
	<a class='tiny tinyblack' href='./massmonitoring.php'>Mass Monitoring</a>
	<a class='tiny tinyblack' href='./detail.php?from=<?php echo date("Hi dmy", (time()-(3600*24*30))) ?>'>Detail</a>
	<a class='tiny tinyblack' href='./ip_info.php'>IP Info</a>
	<a class='tiny tinyblack' onclick='alert("Warning : Due to the complexity of the code, this page may take a few minute to load."); window.location="./management.php"' href='#' >Management</a>
	<a class='tiny tinyblack' href='./about.php'>About</a>
	<br>
	<?php

	if(isset($_COOKIE['wallboard_mode']) && $_COOKIE['wallboard_mode']=='1'){

		// If the wallmode is on, display a "Off" button on the screen
		echo "<span class='tiny fright'>Wallboard Mode [<a href='javascript:set_wallboard_mode(WALLBOARD_MODE_OFF);' title='Turn off the wallboard mode'>On</a>]</span>";

		if(isset($_GET['slide']) && preg_match("/^[0-9]*$/",$_GET['slide'])){
			$slide=$_GET['slide']+1;
			$slidemax=count($glb_slidehow_pages);

			if($slide>$slidemax){
				$slide=1;
			}

			$newpage=$glb_slidehow_pages[$slide];
		}else{
			$slide=1;

			$newpage=$glb_slidehow_pages[$slide];
		}
		echo "<meta http-equiv=\"refresh\" content=\"".$glb_autorefresh.";URL='".$newpage."&slide=".$slide."'\">";

	}else{
		// If the wallmode is off, display a "On" button on the screen
		echo "<span class='tiny fright'>Wallboard Mode [<a href='javascript:set_wallboard_mode(WALLBOARD_MODE_ON);' title='Turn on the wallboard mode'>Off</a>]</span>";

		echo "<meta http-equiv=\"refresh\" content=\"".$glb_autorefresh."\" >";
	}
	?>	
</div>

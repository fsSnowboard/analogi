<?php
/*
 * Copyright (c) 2012 Andy 'Rimmer' Shepherd <andrew.shepherd@ecsc.co.uk> (ECSC Ltd).
 * This program is free software; Distributed under the terms of the GNU GPL v3.
 */

if($glb_debug==1){
	$starttime_topidchart = microtime();
	$startarray_topidchart = explode(" ", $starttime_topidchart);
	$starttime_topidchart = $startarray_topidchart[1] + $startarray_topidchart[0];
}

# To filter on 'Category' (SSHD) extra table needs adding, but they slow down the query for other things, so lets only put them into the SQL if needed....
if(strlen($wherecategory)>5){
	$wherecategory_tables=", signature_category_mapping, category";
	$wherecategory_and="and alert.rule_id=signature_category_mapping.rule_id
        and signature_category_mapping.cat_id=category.cat_id";
}else{
	$wherecategory_tables="";
	$wherecategory_and="";
}

$query="SELECT count(alert.id) as res_cnt, alert.rule_id as res_id, signature.description as res_desc, signature.rule_id as res_rule
	FROM alert, signature ".$wherecategory_tables."
	WHERE alert.timestamp>'".(time()-($inputhours*60*60))."' 
	and alert.rule_id=signature.rule_id 
	".$wherecategory_and."
	AND signature.level>=".$inputlevel."
	".$glb_notrepresentedwhitelist_sql." 
	".$wherecategory." 
	GROUP BY res_id, res_desc, res_rule  
	ORDER BY count(alert.id) DESC
	LIMIT ".$glb_indexsubtablelimit; 

echo "<div class='top10header'>"
	."<a href='#' class='tooltip'><img src='./images/help.png' /><span>Busiest"
	."rules in given time period.</span></a>"
	."Top Rule_ID, <span class='tw'>".$inputhours."</span> Hrs (lvl "
	."<span class='tw'>".($inputlevel)."</span>+)</div>";

$mainstring="";

if(!$result=mysql_query($query, $db_ossec)){
	$mainstring= "SQL Error: ".$query;

}elseif($glb_debug==1){
	$mainstring="<div style='font-size:24px; color:red;font-family: Helvetica,Arial,sans-serif;'>Debug</div>";
	$mainstring.=$query;

	$endtime_topidchart = microtime();
	$endarray_topidchart = explode(" ", $endtime_topidchart);
	$endtime_topidchart = $endarray_topidchart[1] + $endarray_topidchart[0];
	$totaltime_topidchart = $endtime_topidchart - $starttime_topidchart;
	$mainstring.="<br>Took ".round($totaltime_topidchart,1)." seconds";
}else{

	# Keep this in the same format that detail.php already uses
	$from=date("Hi dmy", (time()-($inputhours*3600)));
	$mainstring.="<div style='width: 220px; height: 220px; font-size: 11px;'"
				."id='topid-chartdiv'></div>";

    $i = 0;
	while($row = @mysql_fetch_assoc($result)){
		$topidchartdata[$i]['title'] = htmlspecialchars($row['res_desc']);
		$topidchartdata[$i]['value'] = number_format($row['res_cnt']);
		$topidchartdata[$i]['url'] = "./detail.php?rule_id=".$row['res_rule']."&from=".$from."&breakdown=source";
		$i++;
		$mainstring.="<div class='fleft top10data' style='width:60px'>"
		            .number_format($row['res_cnt'])
		            ."</div>"
				    ."<div class='fleft top10data'>"
				    ."<a class='top10data tooltip_small' href='".$topidchartdata[$i]['url']."'>"
				    .htmlspecialchars(substr($row['res_desc'], 0, 28))
				    ."...<span>".$topidchartdata[$i]['title']."</span></a>"
				    ."</div>";
	
		$mainstring.="			<div class='clr'></div>";
	}
	
}
?>

<script type="text/javascript">
var chart = AmCharts.makeChart("topid-chartdiv", {
    "type": "pie",
    "theme": "none",
/*    "dataProvider": [{
        "title": "New",
        "value": 4852
    }, {
        "title": "Returning",
        "value": 9899
    }],*/

    "dataProvider": [
    	<?php
    	$javaDataProvider = "";
			foreach ($topidchartdata as $datatype) {
				$javaDataProvider.=  "{\n";
			    foreach ($datatype as $key => $value) {
			        $javaDataProvider.= "\"".$key ."\": \"" .$value ."\",";
			    }
			    $javaDataProvider.= "\n}, ";
			}
    	
    	echo rtrim($javaDataProvider, ", ");
    	?>
    ],

    "titleField": "title",
    "valueField": "value",
    "urlField": "url",

    "radius": "42%",
    "innerRadius": "40%",
    "hideLabelsPercent": 100,
    "hoverAlpha": .4,
    "balloonText": "[[title]] ([[value]])\n [[percents]]%",
 	"startDuration": 0

});

chart.labelsEnabled = false;
chart.autoMargins = false;
chart.marginTop = 0;
chart.marginBottom = 0;
chart.marginLeft = 0;
chart.marginRight = 0;
chart.pullOutRadius = 0;
</script>
<?php
if($mainstring==""){
	echo $glb_nodatastring;
}else{
	echo $mainstring;
}



?>

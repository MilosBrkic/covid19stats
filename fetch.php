<?php
include('database.php');

$response = file_get_contents('https://corona.lmao.ninja/v3/covid-19/countries/serbia?yesterday');
$response = json_decode($response);

$result = pg_query($db, "SELECT MAX(date) as date FROM stats");
$maxDate = pg_fetch_all($result);


if(time() - strtotime($maxDate[0]['date']) > 80000 && 16 <= date('H') && date('H') <= 20){

	if($response->todayCases == 0){
		echo "Data has not been updated!";
		return;
	}
	

	$res = pg_insert($db, 'stats', [
		'cases' => $response->cases,
		'today_cases' => $response->todayCases,
		'deaths' => $response->deaths,
		'today_deaths' => $response->todayDeaths,
		'recovered' => $response->recovered,
		'today_recovered' => $response->todayRecovered,
		'active' => $response->active,
		'critical' => $response->critical,
		'tests' => $response->tests
		]);
	if ($res) {
		echo "Data is successfully fetched\n";
	} else {
		echo "Error\n";
	}
}
else echo "Data can be fetchted once per day between 16h and 20h";
	

	
	

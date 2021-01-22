<?php
	include('database.php');
	$result = pg_query($db, "SELECT * FROM stats");
	$data = pg_fetch_all($result);

	//converting datetime to timestamp suitable for javascript
	foreach ($data as &$row) {		
		$row['date'] = strtotime($row['date']);
	}
	unset($row);
	
	$recent = $data[count($data)-1];
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">

	<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
	<title>SERBIA COVID 19 STATS</title>
</head>
<body>
	<div class="container">
	
		<div class="d-flex justify-content-between">
			<h3>Data for <?= date('d.m.Y.',$recent['date']) ?></h3>
			<small class="mt-3">Data usually gets updated at 18:00 </small>	
		</div>
		<hr class="mb-1 mt-1">

		<div class="row mb-3">
			<div class="col-12 col-md-4">
				<h4>Total cases: <?= $recent['cases'] ?></h4>
				<h4>Active: <?= $recent['active'] ?></h4>
				<h4>Deaths: <?= $recent['deaths'] ?></h4>		
			</div>
			<div class="col-12 col-md-4">
				<h4>Today cases: <?= $recent['today_cases'] ?></h4>
				<h4>Deaths today: <?= $recent['today_deaths'] ?></h4>
				<h4>Recovered today: <?= $recent['today_recovered'] ?></h4>
			</div>
			<div class="col-12 col-md-4">
				<h4>Recovered: <?= $recent['recovered'] ?></h4>				
				<h4>Tests: <?= $recent['tests'] ?></h4>
				<h4>Critical: <?= $recent['critical'] ?></h4>
			</div>	
		</div>

		

		<div class="row justify-content-center">
			<div class="mb-5">
				<canvas id="totalChart"  height="400 "></canvas>				
			</div>
			<div class="mb-5">
				<canvas id="todayChart"  height="400 "></canvas>	
			</div>
			<div>
				<canvas id="pieChart"  height="400 "></canvas>	
			</div>
				
		</div>

	<div class="">
		<table class="table mt-3">
			<thead>
				<tr>
					<th scope="col">Date</th>
					<th scope="col">Active</th>
					<th scope="col">Cases</th>
					<th scope="col">New cases</th>
					<th scope="col">Deaths</th>
					<th scope="col">New deaths</th>
					<th scope="col">Recovered</th>
					<th scope="col">New recovered</th>
					<th scope="col">Critical</th>
					<th scope="col">Tests</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach(array_reverse($data) as $row): ?>
				<tr>
					<th scope="row"><?= date('d.m.Y.',$row['date']); ?></th>
					<td><?= $row['active'] ?></td>
					<td><?= $row['cases'] ?></td>
					<td><?= $row['today_cases'] ?></td>
					<td><?= $row['deaths'] ?></td>
					<td><?= $row['today_deaths'] ?></td>
					<td><?= $row['recovered'] ?></td>
					<td><?= $row['today_recovered'] ?></td>
					<td><?= $row['critical'] ?></td>
					<td><?= $row['tests'] ?></td>
				</tr>
				<?php endforeach; ?>	
			</tbody>
		</table>
	</div>

	</div> 			
	

<div style="margin-top:16px;color:dimgrey;font-size:9px;font-family: Verdana, Arial, Helvetica, sans-serif;text-decoration:none;">
	Data source: <a href="https://corona.lmao.ninja/" target="_blank" title="JavaScript Multi Series Charts &amp; Graphs ">https://corona.lmao.ninja/</a>
</div>




</body>

<script>

	window.onload = function () {
	
		var timestamps = <?= json_encode(array_column($data, 'date'), JSON_NUMERIC_CHECK); ?>;
		//convert PHP timestamps to javascript dates
		var dates = timestamps.map(element => {
			return new Intl.DateTimeFormat('en-GB', { month: 'long', day: '2-digit'}).format(1000 * element);
		});


		Chart.defaults.global.elements.line.borderWidth = 4;
		Chart.defaults.global.elements.line.fill = false;	
		Chart.defaults.global.title.fontSize = 25;
		Chart.defaults.global.tooltips.intersect = false;
		Chart.defaults.global.tooltips.mode = 'x';
	
		//console.log(Chart.defaults.global);

		var ctx = document.getElementById('totalChart');
		var ctx2 = document.getElementById('todayChart');
		var ctxPie = document.getElementById('pieChart');

		const totalChart = new Chart(ctx, {
			type: 'line',
			
			data: {
				labels: dates,
				datasets: [{
					label: 'Active',
					data: <?= json_encode(array_column($data, 'active'), JSON_NUMERIC_CHECK); ?>,  
					borderColor: 'red'
				},
				{
					label: 'Cases',
					data: <?= json_encode(array_column($data, 'cases'), JSON_NUMERIC_CHECK); ?>,
					borderColor: 'orange'
				},			
				{
					label: 'Deaths',
					data: <?= json_encode(array_column($data, 'deaths'), JSON_NUMERIC_CHECK); ?>,
					borderColor: 'black'
				},
				{
					label: 'Recovered',
					data: <?= json_encode(array_column($data, 'recovered'), JSON_NUMERIC_CHECK); ?>,
					borderColor: 'green'
				},
				{
					label: 'Tests',
					data: <?= json_encode(array_column($data, 'tests'), JSON_NUMERIC_CHECK); ?>,
					borderColor: 'DarkMagenta',
					hidden: true
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				title: {
					display: true,
					text: 'Total:',
				},
				scales: {
					yAxes: [{
						ticks: {
							beginAtZero: false
						}
					}]
				}
			}
		});

		const todayChart = new Chart(ctx2, {
			type: 'line',
			
			data: {
				labels: dates,
				datasets: [
				{
					label: 'Cases',
					data: <?= json_encode(array_column($data, 'today_cases'), JSON_NUMERIC_CHECK); ?>,
					borderColor: 'red'
				},
				{
					label: 'Deaths',
					data: <?= json_encode(array_column($data, 'today_deaths'), JSON_NUMERIC_CHECK); ?>,
					borderColor: 'black'
				},
				{
					label: 'Recovered',
					data: <?= json_encode(array_column($data, 'today_recovered'), JSON_NUMERIC_CHECK); ?>,
					borderColor: 'green'
				},
				{
					label: 'Critical',
					data: <?= json_encode(array_column($data, 'critical'), JSON_NUMERIC_CHECK); ?>,
					borderColor: 'orange'
				}
				]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				title: {
					display: true,
					text: 'Daily:'
				},
				scales: {
					yAxes: [{
						ticks: {
							beginAtZero: false,
							precision: 0
						}
					}]
				}
			}
		});

		const pieChart = new Chart(ctxPie, {
			type: 'pie',
			
			data: {
				labels: [
					'Active',
					'Deaths',
					'Recovered'
				],
				datasets: [{
					data: <?= json_encode(array( $recent['active'], $recent['deaths'], $recent['recovered']), JSON_NUMERIC_CHECK); ?>,  
					backgroundColor: ['red', 'black', 'green']
				}]
			},
			options: {
				title: {
					display: true,
					text: '<?= "Total cases: {$recent['cases']}" ?>'
				},
				tooltips: {
					mode: 'nearest',
				},
				responsive: true,
				maintainAspectRatio: false,
			}
		});

	}
	 
</script>

</html>
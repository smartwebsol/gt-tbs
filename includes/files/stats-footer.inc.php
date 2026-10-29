<?php 
require_once(_PATH.'includes/classes/student.class.php');
require_once(_PATH.'includes/classes/gcsform.class.php');
require_once(_PATH.'includes/classes/step1results.class.php');
require_once(_PATH.'includes/classes/step2form.class.php');
require_once(_PATH.'includes/classes/step3mark.class.php');

$student = new Student($dbase);

$langStats = $lang->get(['stats']);

$data = array(
	"label" => array(
		$langStats['students'],	
		$langStats['step1'],	
		$langStats['step1res'],	
		$langStats['step2'],	
		$langStats['step3']	
	),
	"total" => array(
		(int)(new Student($dbase))->total(),
		(int)(new GCSForm($dbase))->totalStudents(),
		(int)(new Step1Result($dbase))->totalStudents(),
		(int)(new Step2Form($dbase))->total(),
		(int)(new Step3Mark($dbase))->totalStudents()
	)
);


$datastep3 = array(
	"label" => array(
		$langStats['step3chem'],	
		$langStats['step3midyis'],	
		$langStats['step3gl'],	
		$langStats['step3elis']
	),
	"total" => (new Step3Mark($dbase))->step3graph()	
);

?>
<script src="dist/js/moment.min.js"></script>
<script src="dist/js/Chart.bundle.min.js"></script>
<script src="dist/js/bootstrap-autocomplete.min.js"></script>

<script>
window.chartColors = {
	red: 'rgb(255, 99, 132)',
	orange: 'rgb(255, 159, 64)',
	yellow: 'rgb(255, 205, 86)',
	green: 'rgb(75, 192, 192)',
	blue: 'rgb(54, 162, 235)',
	purple: 'rgb(153, 102, 255)',
	grey: 'rgb(201, 203, 207)'
};

var randomScalingFactor = function() {
	return Math.round(Math.random() * 100);
};

		var config = {
			type: 'doughnut',			
			data: {
				datasets: [{
					data: <?php echo json_encode($data['total'])?>,
					backgroundColor: [
						window.chartColors.red,
						window.chartColors.orange,
						window.chartColors.yellow,
						window.chartColors.green,
						window.chartColors.blue
					],
					label: 'Dataset 1'
				}],
				labels: <?php echo json_encode($data['label'])?>
			},
			options: {
				responsive: true,
				legend: {
					position: 'top',
					fullWidth: true
				},
				title: {
					fontSize: 20,
					display: true,
					text: "<?php echo $langStats['tab1']?>"
				},
				animation: {
					animateScale: true,
					animateRotate: true
				}
			}
		};


		var configstep3 = {
			type: 'horizontalBar',			
			data: {
				datasets: [{
					data: <?php echo json_encode($datastep3['total'])?>,
					backgroundColor: [
						window.chartColors.red,
						window.chartColors.orange,
						window.chartColors.yellow,
						window.chartColors.green
					],
					label: ''
				}],
				labels: <?php echo json_encode($datastep3['label'])?>
			}	,
			options: {
        scales: {
            xAxes: [{
                ticks: {
                    suggestedMin: 1,
                    suggestedMax: 100
                }
            }]
        }
    }		
		};


function drawStudentGraph(dt){

	/*
	Example set
	var barChartData = {
			labels: ['CHEM','MIDYIS','El','Alis'],
			datasets: [{
				label: 'Dataset 1',
				backgroundColor: "rgba(255,255,0,.3)",
				stack: 'Stack 0',
				data: [
					45,30,0,0	
				]
			}, {
				label: 'Dataset 2',
				backgroundColor: "rgba(255,0,255,.3)",
				stack: 'Stack 0',
				data: [
					90,23,0,0
				]
			}]

		};
	*/

	var ctx = document.getElementById('specific-stats').getContext('2d');
	var chart = new Chart(ctx, {
		type: 'bar',
		data: dt,
		options: {
			callbacks: {
				label: function (t, d) {
					var xLabel = d.datasets[t.datasetIndex].label;
					var yLabel = t.yLabel >= 1000 ? t.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : t.yLabel;
					return xLabel + ': ' + yLabel;
				}
			},
			legend: {
				display: false,
				position: 'top'
			},
			responsive: true,
			scales: {
				xAxes: [{
					stacked: false,
				}],
				yAxes: [{
					stacked: false
				}]
			}
		},
		plugins: [{
			beforeDraw: function (chart) {
				var labels = chart.data.labels;
			}
		}]
	});
}

$(document).ready(function(){
  var ctx = document.getElementById('generic-stats').getContext('2d');
	window.myDoughnut = new Chart(ctx, config);

	var ctx2 = document.getElementById('step3-stats').getContext('2d');
	window.myDoughnut2 = new Chart(ctx2, configstep3);

	$('#studentSearch').autoComplete({
		resolver: 'custom',
		minLength: 3,
		formatResult: function (item) {			
			return {
				value: item.id,
				text: item.name + ' - ' + item.grade,
				html: [
						$('<div>').html(item.name + ' - ' + item.grade)
					]
			};
		},
		events: {
			search: function (name, callback) {
				// let's do a custom ajax call
				$.post(
					'forms/xhr.action.php',
					{
						'action': 'step3studentnames', 
						'name': name
					}
				).done(function (res) {								
					res = JSON.parse(res);
					callback(res);
				});
			}
		}
	});

	$('#studentSearch').on('autocomplete.select',function (evt, item) {
  	$.post(
			'forms/xhr.action.php',
			{
				'action': 'step3student', 
				'student_id': item.id
			}
		).done(function (res) {
			res = JSON.parse(res);
			drawStudentGraph(res);
		});

	});
})
</script>
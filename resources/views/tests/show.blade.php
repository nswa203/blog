@extends('main')

@section('title','| Tests Show')

@section('stylesheets')
@endsection

@section('content')
	<div class="row">
		<div class="col-md-8">
			<h1><span class="fas fa-vial mr-4"></span>Tests Show {{ $test }}</h1>
			<hr>
						
			<div id="app">
				<folders></folders>
				<images></images>
			</div>
			
			<div>
				<h2>If you can see a scatter graph below then chart.js is working</h2>
				<canvas class="mt-4" id="myChart"></canvas>
			</div>	
		
		</div>	
	</div>	
@endsection

@section('scripts')
	{!! Html::script('js/app.js') !!}
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
	<script>
		var ctx = document.getElementById('myChart').getContext('2d');
		gdata   = @json($gdata);
		grange  = @json($grange);
		console.log(gdata);
		console.log(grange);

		var chart = new Chart(ctx, {
			type: 'scatter',
			data: {
				datasets: [
				{
					label: 'Elevation in metres',
					borderColor: "#3e95cd",
					backgroundColor: "#3ebbcd",
					data: gdata,
					fill: true,
				}]
			},
			options: {
				scales: {
					xAxes:[{
						ticks: {
							max: grange[1],
							maxRotation: 45,
                    		minRotation: 45,
                    		callback: function(value, index, values) {
                    			if      (value==grange[0]) { l = 'Start: ' + value + ' metres'; }
                    			else if (value==grange[1]) { l = 'Finish: '+ value + ' m'; }
                    			else                       { l = value; }  
                    			return l;
                    		}
						}	
					}],
					yAxes: [{
						ticks: {
							suggestedMin: grange[2],
							suggestedMax: grange[3],
							beginAtZero: false
						}
					}]
				},
				title: {
					display: true,
					text: 'Route profile'
				}
			}
		});
	</script>	

	<script>
		var app=new Vue({
			el: '#app',
		});
	</script>	
@endsection
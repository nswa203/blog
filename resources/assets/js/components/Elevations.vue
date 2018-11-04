<style scoped>

</style>

<template>
    <div>
        <canvas id="myChart"></canvas>
        <!-- {{ results }} -->
    </div>
</template>

<script>
    var axios = require("axios");

    export default {
        props: {
    
        },
        data: function() {
            return {
                api_token: this.$root.api_token,
                resource_id: this.$root.resource_id,
                route: this.$root.route,
                results: this.$root.results,
            }
        },
        methods: {
            getElevation: function(e, parms) {
                //console.log('getElevation: '+parms)

                let vm = this;
                if (this.api_token && parms) {    
                    axios.get(this.route, {
                        params: {
                            api_token: vm.api_token,
                            parms: parms,
                            id: e.resource_id
                        }
                    }).then(function (response) {
                        if (response.data) {
                            vm.results = response.data;
                            vm.$emit('results-changed', vm.results);
                            vm.makeElevation(vm.results);
                        }
                    }).catch(function (error) {
                        e.results = 'Error';
                    });
                } else {
                    e.results = 'Bad inpout';
                }
            },
            makeElevation: function (results) {
                //console.log('makeElevation:');
                makeElevationChart(results);
             }
        },
        mounted() {
            //console.log('getElevation.mounted:');
            this.getElevation(this, 'input1');
        }
    };

    // ========================================================================== //
    // Makes Elevation charts using chart.js
    function makeElevationChart(results) {
        //console.log('makeElevationChart:');
        var gdata  = results['data'];               // Elevation v Distance
        var grange = results['range'];              // Range max/min data 
        var gdata2 = results['data2'];              // Climb v distance 

        //console.log(gdata);
        //console.log(grange);

        var ctx = document.getElementById('myChart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'scatter',
            data: {
                datasets: [
                    {
                        label: 'Climb',
                        yAxisID: 'C',
                        borderColor: "#1f6e7a",
                        backgroundColor: "#1f6e7a",
                        data: gdata2,
                        fill: false,
                        pointRadius: 1,
                        pointHoverRadius: 5
                    }, {
                        label: 'Elevation',
                        yAxisID: 'E',
                        borderColor: "#2a92a2",
                        backgroundColor: "#49bed0",
                        data: gdata,
                        fill: true,
                        pointRadius: 1,
                        pointHoverRadius: 5
                    }                    
                ]
            },
            options: {
                scales: {
                    xAxes:[{
                        scaleLabel: {
                            display: true,
                            labelString: 'Distance - miles'
                        },
                        ticks: {
                            max: grange[1],
                            maxRotation: 45,
                            minRotation: 45,
                            callback: function(value, index, values) {
                                var v = value;    
                                if      (value==grange[0]) { v = 'Start: ' + value; }
                                else if (value==grange[1]) { v = 'Finish: '+ value; }
                                return v;
                            }
                        }   
                    }],

                    yAxes: [
                        {
                            id: 'E',
                            display: true,
                            position: 'right',
                            scaleLabel: {
                                display: true,
                                labelString: 'Elevation - metres'
                            },
                            ticks: {
                                suggestedMin: grange[2],
                                suggestedMax: grange[3],
                                beginAtZero: false
                            }
                        }, {
                            id: 'C',
                            display: true,
                            position: 'left',
                            scaleLabel: {
                                display: true,
                                labelString: 'Climb - metres',
                            },
                            ticks: {
                                suggestedMax: grange[4],
                                beginAtZero: true
                            }
                        }
                    ]
                },
                title: {
                    display: true,
                    text: 'Route Profile'
                }
            }
        }); 
    }            

</script>

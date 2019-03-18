<style scoped>

</style>

<template>
    <div>
        <div class="row" id="accordionpm1">
            <div class="col-md-12 myWrap">
                <h1>
                    <span class="h1-suffix">Points of Interest, Climb &amp; Elevation</span>
                    <a><span class="mt-2 pointer-expand fas fa-chevron-circle-down float-right mr-2"
                             data-toggle="collapse" data-target="#collapsepm1">
                    </span></a>                             
                </h1>
                <div id="collapsepm1" class="collapse hide" data-parent="#accordionpm1">             
                    <div class="d-flex justify-content-center">
                        <canvas id="myChart_1"></canvas>
                        <!-- {{ results }} -->
                    </div>
                </div>  
            </div>
        </div>

        <div class="row" id="accordionpm2">
            <div class="col-md-12 myWrap">
                <h1>
                    <span class="h1-suffix">Calories &amp; Elevation</span>
                    <a><span class="mt-1 mb-1 pointer-expand fas fa-chevron-circle-down float-right mr-2"
                             data-toggle="collapse" data-target="#collapsepm2">
                    </span></a>                             
                </h1>
                <div id="collapsepm2" class="collapse hide" data-parent="#accordionpm2">             
                    <div class="d-flex justify-content-center">
                        <canvas id="myChart_2"></canvas>
                        <!-- {{ results }} -->
                    </div>
                </div>  
            </div>
        </div>        
    </div>
</template>

<script>
    // *******************************************************
    // perform "npm run watch" to enact changes to this code 
    // *******************************************************
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
                makeElevationChart_1(results);
                makeElevationChart_2(results);
             }
        },
        mounted() {
            //console.log('getElevation.mounted:');
            this.getElevation(this, 'input1');
        }
    };

    // ========================================================================== //
    // Makes Elevation charts using chart.js
    function makeElevationChart_1(results) {
        //console.log('makeElevationChart:');
        var grange = results['range'];              // Range max/min data 
        var gdata1 = results['data1'];              // Elevation v Distance
        var gdata2 = results['data2'];              // Climb     v Distance
        var gdata3 = results['data3'];              // Waypoints v Distance
        //console.log(gdata1);
        //console.log(grange);

        var ctx = document.getElementById('myChart_1').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'scatter',
            data: {
                datasets: [
                    {
                        label: 'POI',
                        yAxisID: 'E',
                        borderWidth: 2,
                        borderColor: "#1f6e7a",
                        backgroundColor: "white",
                        data: gdata3,
                        fill: false,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        showLine: false
                    }, {
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
                        data: gdata1,
                        fill: true,
                        pointRadius: 1,
                        pointHoverRadius: 5
                    }                    
                ]
            },
            options: {
                tooltips: {                                         // Insert Waypoint label (gdata3.l) into tooltip 
                    callbacks: {
                        footer: function(tooltipItems, data) {
                            var label = false;
                            if (tooltipItems[0].datasetIndex==0) {
                                var i = tooltipItems[0].index;
                                //console.log(data.datasets[0].data[i]);
                                if (data.datasets[0].data[i].l != 'undefined') {
                                    label = data.datasets[0].data[i].l;
                                }
                            }   
                            return label;
                        },
                    },
                    footerFontStyle: 'bold'
                },
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

        // Add vertical cursor - Hook into main event handler
        var parentEventHandler = Chart.Controller.prototype.eventHandler;
        Chart.Controller.prototype.eventHandler = function() {
            var ret = parentEventHandler.apply(this, arguments);
            this.clear();
            this.draw();
            // Draw the vertical line here
            var eventPosition = Chart.helpers.getRelativePosition(arguments[0], this.chart);
            // console.log(this.chart.ctx);
            var h = this.chart.ctx.canvas.height;
            this.chart.ctx.beginPath();
            this.chart.ctx.moveTo(eventPosition.x, 60);
            this.chart.ctx.strokeStyle = "#ff0000";
            this.chart.ctx.lineTo(eventPosition.x, h-85);
            this.chart.ctx.stroke();
            return ret;
        };
    }            

    // ========================================================================== //
    // Makes Elevation charts using chart.js
    function makeElevationChart_2(results) {
        //console.log('makeElevationChart:');
        var grange = results['range'];              // Range max/min data 
        var gdata1 = results['data1'];              // Elevation v Distance
        var gdata2 = results['data2'];              // Climb     v Distance
        var gdata3 = results['data3'];              // Waypoints v Distance
        var gdata4 = results['data4'];              // Calories  v Distance Nick
        var gdata5 = results['data5'];              // Calories  v Distance Dave
        var gdata6 = results['data6'];              // Calories  v Distance Chris
        //console.log(gdata1);
        //console.log(grange);

        var ctx = document.getElementById('myChart_2').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'scatter',
            data: {
                datasets: [
                    {
                        label: 'Nick',
                        yAxisID: 'C1',
                        borderColor: "#b30000",
                        backgroundColor: "#b30000",
                        data: gdata4,
                        fill: false,
                        pointRadius: 1,
                        pointHoverRadius: 5
                    }, {                        
                        label: 'Dave',
                        yAxisID: 'C1',
                        borderColor: "#b35500",
                        backgroundColor: "#b35500",
                        data: gdata5,
                        fill: false,
                        pointRadius: 1,
                        pointHoverRadius: 5
                    }, {                        
                        label: 'Chris',
                        yAxisID: 'C1',
                        borderColor: "#b38800",
                        backgroundColor: "#b38800",
                        data: gdata6,
                        fill: false,
                        pointRadius: 1,
                        pointHoverRadius: 5                        
                    }, {                        
                        label: 'Elevation',
                        yAxisID: 'E',
                        borderColor: "#2a92a2",
                        backgroundColor: "#49bed0",
                        data: gdata1,
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
                            id: 'C1',
                            display: true,
                            position: 'left',
                            scaleLabel: {
                                display: true,
                                labelString: 'kCals',
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

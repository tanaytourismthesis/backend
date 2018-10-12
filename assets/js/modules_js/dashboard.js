var load_visitsclicks_graph = () => {
  $.get(
    `${baseurl}dashboard/getVisitsAndClicks`
  ).done(function(data) {
    var siteVisitGraph = $('.site-visits-graph');
    siteVisitGraph.find('.note').addClass('hidden');
    if (data.response) {
      var config = {
  			type: 'line',
  			data: {
  				labels: data.data.labels,
  				datasets: data.data.datasets
  			},
  			options: {
  				responsive: true,
  				title: {
  					display: false,
  					text: 'Clicks and Visits'
  				},
  				tooltips: {
  					mode: 'index',
  					intersect: false,
  				},
  				hover: {
  					mode: 'nearest',
  					intersect: true
  				},
  				scales: {
  					xAxes: [{
  						display: true,
  						scaleLabel: {
  							display: true,
  							labelString: 'Days'
  						}
  					}],
  					yAxes: [{
  						display: true,
  						scaleLabel: {
  							display: true,
  							labelString: 'Number of Visits/Clicks'
  						}
  					}]
  				}
  			}
  		};
      var ctx = document.getElementById('visitsAndClicksGraph').getContext('2d');
  		window.myLine = new Chart(ctx, config);
    } else {
      siteVisitGraph.find('.note').removeClass('hidden').html('Failed to load graph');
    }
  }).fail(function() {
    siteVisitGraph.find('.note').removeClass('hidden').html('Failed to load graph');
  });
};

$(function(){
  load_visitsclicks_graph();
});

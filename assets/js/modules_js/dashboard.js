var load_visitsclicks_graph = () => {
  $.get(
    `${baseurl}dashboard/getVisitsAndClicks`
  ).done(function(data) {
    var siteVisitGraph = $('.site-visits-graph');
    siteVisitGraph.find('.note').addClass('hidden');
    if (data.response) {
      if (windowWidth < 992) {
        $.each(data.data.labels, function(index, value) {
          data.data.labels[index] = value.substring(0, 5);
        });
      }

      var config = {
  			type: siteVisitGraph.data('graph-type'),
  			data: {
  				labels: data.data.labels,
  				datasets: data.data.datasets
  			},
  			options: {
  				responsive: true,
  				title: {
  					display: (windowWidth >= 768),
  					text: 'Clicks and Visits',
            fontSize: '18'
  				},
          legend: {
            display: (windowWidth >= 768),
            position: 'right'
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
  							display: (windowWidth >= 992),
  							labelString: 'Days'
  						}
  					}],
  					yAxes: [{
  						display: true,
  						scaleLabel: {
  							display: (windowWidth >= 992),
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

var load_popular_pagecontents = (top = 5) => {
  var popularPageList = $('.popular-page-list');
  popularPageList.find('.list-group').html('<i class="fa fa-spinner fa-spin"></i> Loading popular page contents...');
  $.get(
    `${baseurl}dashboard/popular_page_contents`
  ).done(function(data) {
    if (data.response) {
      popularPageList.find('.list-group').html('');
      $.each(data.data, function(index, value) {
        popularPageList.find('.list-group')
          .append(
            `<li class="list-group-item">
              <a href="${clienturl}hca/details/${value['tag']}/${value['slug']}" target="_blank">
                ${value['title'].length > substrLen ? value['title'].substring(0, substrLen) + '...' : value['title']}
              </a>&nbsp;<span class="badge">${value['click_count']}</span>
            </li>`
          )
      });
    } else {
      popularPageList.find('.list-group').html(data.message);
    }
  }).fail(function() {
    popularPageList.find('.list-group').html('Failed to retrieve details');
  });
}

var load_popular_news = (top = 5) => {
  var popularNews = $('.popular-news-list');
  popularNews.find('.list-group').html('<i class="fa fa-spinner fa-spin"></i> Loading popular news...');
  $.get(
    `${baseurl}dashboard/popular_news`
  ).done(function(data) {
    if (data.response) {
      popularNews.find('.list-group').html('');
      $.each(data.data, function(index, value) {
        popularNews.find('.list-group')
          .append(
            `<li class="list-group-item">
              <a href="${clienturl}news/details/${value['news_type_slug']}/${value['news_slug']}" target="_blank">
                ${value['title'].length > substrLen ? value['title'].substring(0, substrLen) + '...' : value['title']}
              </a>&nbsp;<span class="badge">${value['click_count']}</span>
            </li>`
          )
      });
    } else {
      popularNews.find('.list-group').html(data.message);
    }
  }).fail(function() {
    popularNews.find('.list-group').html('Failed to retrieve details.');
  });
}

var load_topcontributors_pagecontent = (top = 5) => {
  var topContribPageContent = $('.top-page-contributors');
  topContribPageContent.find('.list-group').html('<i class="fa fa-spinner fa-spin"></i> Loading top page content contributors...');
  $.get(
    `${baseurl}dashboard/top_contributors_pagecontent`
  ).done(function(data) {
    if (data.response) {
      topContribPageContent.find('.list-group').html('');
      $.each(data.data, function(index, value) {
        topContribPageContent.find('.list-group')
          .append(
            `<li class="list-group-item">
              ${value['first_name']}&nbsp;${value['last_name']}&nbsp;<span class="badge">${value['contrib_count']}</span>
            </li>`
          )
      });
    } else {
      topContribPageContent.find('.list-group').html(data.message);
    }
  }).fail(function() {
    topContribPageContent.find('.list-group').html('Failed to retrieve details.');
  });
}

var load_topcontributors_news = (top = 5) => {
  var topContribNews = $('.top-news-contributors');
  topContribNews.find('.list-group').html('<i class="fa fa-spinner fa-spin"></i> Loading top news contributors...');
  $.get(
    `${baseurl}dashboard/top_contributors_news`
  ).done(function(data) {
    if (data.response) {
      topContribNews.find('.list-group').html('');
      $.each(data.data, function(index, value) {
        topContribNews.find('.list-group')
          .append(
            `<li class="list-group-item">
              ${value['first_name']}&nbsp;${value['last_name']}&nbsp;<span class="badge">${value['contrib_count']}</span>
            </li>`
          )
      });
    } else {
      topContribNews.find('.list-group').html(data.message);
    }
  }).fail(function() {
    topContribNews.find('.list-group').html('Failed to retrieve details.');
  });
}

var load_userstats_graph = () => {
  $.get(
    `${baseurl}dashboard/getUserStats`
  ).done(function(data) {
    var userTypesGraph = $('.user-types-graph');
    userTypesGraph.find('.note').addClass('hidden');
    if (data.response) {
      var config = {
  			type: userTypesGraph.data('graph-type'),
  			data: {
  				labels: data.data.labels,
  				datasets: data.data.datasets
  			},
  			options: {
  				responsive: true,
  				title: {
  					display: (windowWidth >= 768),
  					text: 'User Types Frequency',
            fontSize: '18'
  				}
  			}
  		};
      var ctx = document.getElementById('userTypesGraph').getContext('2d');
  		window.myPie = new Chart(ctx, config);
    } else {
      userTypesGraph.find('.note').removeClass('hidden').html('Failed to load graph');
    }
  }).fail(function() {
    userTypesGraph.find('.note').removeClass('hidden').html('Failed to load graph');
  });
};

$(function(){
  load_visitsclicks_graph();
  load_popular_pagecontents();
  load_popular_news();
  load_topcontributors_pagecontent();
  load_topcontributors_news();
  load_userstats_graph();
});

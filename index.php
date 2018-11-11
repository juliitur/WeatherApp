<?php

$ip = $_SERVER['REMOTE_ADDR'];


if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}

//$ip = "5.32.173.255";
function fetch_curl($url){
    $ch = curl_init($url); //initialize curl
  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //prevent automatic output to screen
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  //in case of MAMP issues
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  //in case of MAMP issues
  
    $data = curl_exec($ch);  //execute our request
  
    curl_close($ch); //close curl
  
    return json_decode($data);
  }
function kelvinToCelsius($temp){
    return $celsius = round($temp - 273.15);
    }

function metersToKilometers($meters){
    return $kilometers = round($meters * 18) / 5;
}

function degreeToDirection($degree){

    if($degree >= 348.75 && $degree <= 11.25) return "N";
    elseif($degree >= 11.26 && $degree <= 33.75) return "NNE";
    elseif($degree >= 33.75 && $degree <= 56.25) return "NE";
    elseif($degree >= 56.25 && $degree <= 78.75) return "ENE";

    elseif($degree >= 78.75 && $degree <= 101.25) return "E";
    elseif($degree >= 101.25 && $degree <= 123.75) return "ESE";
    elseif($degree >= 123.75 && $degree <= 146.25) return "SE";
    elseif($degree >= 146.25 && $degree <= 168.75) return "SSE";
    
    elseif($degree >= 168.75 && $degree <= 191.25) return "S";
    elseif($degree >= 191.25 && $degree <= 213.75) return "SSW";
    elseif($degree >= 213.75 && $degree <= 236.25) return "SW";
    elseif($degree >= 236.25 && $degree <= 258.75) return "WSW";

    elseif($degree >= 258.75 && $degree <= 281.25) return "W";
    elseif($degree >= 281.25 && $degree <= 303.75) return "WNW";
    elseif($degree >= 303.75 && $degree <= 326.25) return "NW";
    elseif($degree >= 326.25 && $degree <= 348.75) return "NNW";

}
  //IP access info
  //$access_key = "?access_key=ae0bccf4bd0897fef0aac018908eed19";
  $base_url = "http://ip-api.com/json/";

  //Process IP info
  $IpInfo = fetch_curl($base_url . $ip);

  // Get location
  $latitude = $IpInfo->lat;
  $longitude = $IpInfo->lon;

  //Weather access info
  $base_url = "api.openweathermap.org/data/2.5/weather?";
  $appid = "5036c792f5c85e67e69f6470bceddf78";
  $forecast_url = "http://api.openweathermap.org/data/2.5/forecast?";
  $forecastInfo = fetch_curl($forecast_url . "lat=". $latitude . "&lon=" . $longitude . "&appid=" . $appid);

  //Process weather info
  $weatherInfo = fetch_curl($base_url . "q=" . $IpInfo->city ."&appid=" . $appid);

  //Calculations:
  $currentTemp = kelvinToCelsius($weatherInfo->main->temp);
  $currentMax = kelvinToCelsius($weatherInfo->main->temp_max);
  $currentMin = kelvinToCelsius($weatherInfo->main->temp_min);
  $windDirection = degreeToDirection(round($weatherInfo->wind->deg, 2));
  $windSpeed = metersToKilometers($weatherInfo->wind->speed);


  //Data for graph:
  $tempInfo = array();
  $dateTime = array();

   //Get date and time of each entry.
  for($i = 0; $i < count($forecastInfo->list); $i++){
      $dateTime[$i] = '"'. substr($forecastInfo->list[$i]->dt_txt, 5) . '"';
      $tempInfo[$i] = kelvinToCelsius($forecastInfo->list[$i]->main->temp);
  }

  $tempInfo = implode(",", $tempInfo);
  $dateTime = implode(",", $dateTime);

  $kelvin = $weatherInfo->main->temp;
  $celsius = $kelvin - 273.15;

  $date = date('D, d F');
  $icon_url = "https://openweathermap.org/img/w/";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp"
    crossorigin="anonymous">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB"
    crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/weather-icons/2.0.9/css/weather-icons.min.css">
  <link href="https://fonts.googleapis.com/css?family=K2D:600|Raleway" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>
  <title>Weather Guide</title>
</head>

<body>
  <!-- First SECTION -->
  <header id="first-section">
    <div class="dark-overlay">
      <div class="home-inner container">
        <h1 class="display-1 text-center"><i class="wi wi-day-sleet-storm f068 text-white"></i>Weather
          <strong>Guide</strong></h1>
        <h5 class="text-center logo"> Our Web Site will found your location by IP address and get the current weather information.</h5>
        <div class="row">
          <div class="col-md-6 py-4">
            <div class="card f3 bg-success card-form l2">
              <div class="card-header f2 text-center">
                <img src="img/location_2.jpeg" class="card-img" alt="">
                <h3 class="py-2 logo">Information about your location:</h3>
              </div>
              <!-- Card with your location -->
              <div class="card-body justify-content-right local">
                <h5>Your IP address: <span class="info"><?php echo $ip; ?></span></h5>
                <h5>Your country name: <span class="info"><?php echo $IpInfo->country; ?></h5>
                <h5>Region code: <span class="info"><?php echo $IpInfo->region; ?></span></h5>
                <h5>City: <span class="info"><?php echo $IpInfo->city; ?></span></h5>
                <h5>Zip: <span class="info"><?php echo $IpInfo->zip; ?></span></h5>
                <h5>Latitude: <span class="info"><?php echo $IpInfo->lat; ?></span></h5>
                <h5>Longitude: <span class="info"><?php echo $IpInfo->lon; ?></span></h5>
                <h5>Time Zone: <span class="info"><?php echo $IpInfo->timezone; ?></span></h5>
                <br>
                <br>
              </div>
              <div class="card-footer f2 f3"></div>
            </div>
          </div>
          <!-- Card with weather -->
          <div class="col-md-6 py-4">
            <div class="card bg-info card-form l2">
              <div class="card-header text-center bg-primary">
                <img src="img/weather.jpeg" class="card-img" alt="">
                <h3 class="py-2 logo">Information about Weather:</h3>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-5 d-flex justify-content-center">
                    <div class="align-self-center">
                    <img src="<?php echo $icon_url . $weatherInfo->weather[0]->icon . ".png" ?>">
                      <h6><?php echo ucwords($weatherInfo->weather[0]->description); ?></h6>
                    </div>
                  </div>
                  <div class="col p-3">
                    <?php echo $date; ?>
                    <h1 class="mt-4 mb-4 p-2 border border-right-0 border-left-0 text-white">

                      <?php printf("%.1f",($weatherInfo->main->temp- 273.15)) ; ?>
                      <sup>o</sup>c
                    </h1>
                    <span>Wind: <?php echo $windDirection . " " . $windSpeed ?> km/h</span>
                    <p>
                      Humidity: <?php echo $weatherInfo->main->humidity ?>% <br>
                      Low of: <?php echo $currentMin; ?> <sup>o</sup>c <br>
                      High of: <?php echo $currentMax; ?> <sup>o</sup>c <br>
                    </p>
                  </div>
                </div>
              </div>
              <div class="card-footer text-center bg-primary">
                <div class="row">
                  <div class="col">
                    <p class="f12"><?php echo (new DateTime($forecastInfo->list[4]->dt_txt))->format('D'); ?></p>
                    <img src="<?php echo $icon_url . $forecastInfo->list[4]->weather[0]->icon . ".png" ?>">
                    <p class="mb-0 mt-2"><?php printf("%.1f", ($forecastInfo->list[4]->main->temp -273.15)) ?>
                    <sup>o</sup>c
                    </p>
                  </div>
                  <div class="col">
                  
                  <p class="f12"><?php echo (new DateTime($forecastInfo->list[12]->dt_txt))->format('D'); ?></p>
                    <img src="<?php echo $icon_url . $forecastInfo->list[12]->weather[0]->icon . ".png" ?>">
                    <p class="mb-0 mt-2"><?php printf("%.1f", ($forecastInfo->list[12]->main->temp -273.15)) ?>
                    <sup>o</sup>c
                    </p>
                  </div>
                  <div class="col">
                    <p class="f12"><?php echo (new DateTime($forecastInfo->list[20]->dt_txt))->format('D'); ?></p>
                    <img src="<?php echo $icon_url . $forecastInfo->list[20]->weather[0]->icon . ".png" ?>">
                    <p class="mb-0 mt-2"><?php printf("%.1f", ($forecastInfo->list[20]->main->temp -273.15)) ?>
                    <sup>o</sup>c
                    </p>
                  </div>
                  <div class="col">
                    <p class="f12"><?php echo (new DateTime($forecastInfo->list[28]->dt_txt))->format('D'); ?></p>
                    <img src="<?php echo $icon_url . $forecastInfo->list[28]->weather[0]->icon . ".png" ?>">
                    <p class="mb-0 mt-2"><?php printf("%.1f", ($forecastInfo->list[28]->main->temp -273.15)) ?>
                    <sup>o</sup>c
                    </p>
                  </div>
                  <div class="col">
                    <p class="f12"><?php echo (new DateTime($forecastInfo->list[36]->dt_txt))->format('D'); ?></p>
                    <img src="<?php echo $icon_url . $forecastInfo->list[36]->weather[0]->icon . ".png" ?>">
                    <p class="mb-0 mt-2"><?php printf("%.1f", ($forecastInfo->list[36]->main->temp -273.15)) ?>
                    <sup>o</sup>c
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
  </header>

  <div class="container">
  <div class="row">
  <div class="col">
  <canvas id="myChart"></canvas>
  </div>
  </div>
  </div>

  <!-- FOOTER -->
  <footer id="main-footer" class="bg-dark">
    <div class="container">
      <div class="row">
        <div class="col text-center py-4 logo">
          <h3>John Abbott College</h3>
          <p>Copyright &copy;
            <span id="year"></span>
          </p>
        </div>
      </div>
    </div>
  </footer>

  <script>
    var ctx = document.getElementById("myChart").getContext('2d');
    var myChart = new Chart(ctx, {
    type: 'line',
    title: {
        text: "Forecast of the next 5 Days!"
    },
    data: {
        labels: [<?php echo $dateTime; ?>],
        datasets: [{
            label: '5 day Forecast (3hr intervals)',
            fill: true,
            borderWidth: 1,
            data: [<?php echo $tempInfo; ?>],
            backgroundColor: [
                'rgba(0, 99, 132, 0.2)',
            ],
            borderColor: [
                'rgba(220,220,220,1)',
            ],
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true,
                }
            }]
        },
        tooltips: {
            callbacks: {
                title: function(tooltipItem, data){
                  return  "Weather information";
                },
                label: function(tooltipItem, data){
                  var xLabel = data.labels[tooltipItem.index]
                  var yLabel = tooltipItem.yLabel;
                  
                  return "On " + xLabel + " the temperature will be: " + yLabel + " Degree celsius";
                }
            }
        }
    }
    });
  </script>

  <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
    crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
    crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T"
    crossorigin="anonymous"></script>

  <script>
    // Get the current year for the copyright
    $('#year').text(new Date().getFullYear());
  </script>
</body>

</html>
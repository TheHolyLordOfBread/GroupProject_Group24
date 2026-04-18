
<html>
<head>
    <meta charset="UTF-8">
    <title></title>
    
</head>
<body>
    <h1>Weather Data</h1>
    <div id="weather"></div>
        <p id="weatherData">asdasdas</p>
<script>


async function fetchData() {

const request = fetch ("https://weather.googleapis.com/v1/forecast/days:lookup?key=AIzaSyA_RFhMBhSY8pwXjZTIZ9hhrF9c6atOmKU&location.latitude=6.0939&location.longitude=100.2751&days=10")
        .then((res) => {
            if (!res.ok) {
                throw new Error("HTTP error " + res.status)
            }
            return res.text();
        })
        .then((data) => {
            document.body.innerHTML += `<br><br>${data}`;
        })
        .catch((err) => {
            console.log(err);
        });







}



fetchData();


</script>
</body>
</html>



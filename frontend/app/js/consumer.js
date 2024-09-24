fetch('../app/frontend_consumer.php')
  .then(response => response.json())
  .then(data => {
    // Process the received JSON data
    console.log(data);
    // Add rendering logic
    document.getElementById('nba-info').innerHTML = 'Player Data: ${data}'
  })
  .catch(error => console.error('Error fetching data:', error));

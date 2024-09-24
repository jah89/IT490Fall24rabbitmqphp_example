fetch('../app/frontend_producer.php', {
    method: 'POST',
    body: JSON.stringify({ query: 'data' })
})
.then(response => response.json())
.then(data => {
    // Use returned data from the PHP consumer script to update the page
    console.log('Message sent:', data);
})
.catch(error => console.error('Error:', error));
document.getElementById('registrationForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const emailAddr = document.getElementById('emailAddr').value;
    const password = document.getElementById('passwd').value;

    fetch('../app/rabbit/register_producer.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ emailAddr: emailAddr, password: password })
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('responseMessage').innerText = data.message;
    })
    .catch(error => console.error('Error:', error));
});
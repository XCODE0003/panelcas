
function sendRequest() {
    fetch('https://mt-panel.guru/api/update-verification')
        .then(response => response.json())
        .then(data => {
            console.log('Получены данные:', data);
        })
        .catch(error => {
            console.error('Ошибка:', error);
        });
}

setInterval(sendRequest, 60000);

sendRequest();

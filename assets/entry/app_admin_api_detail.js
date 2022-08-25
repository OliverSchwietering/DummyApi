//import '../styles/'

const addEndpointHeaderButtons = document.querySelectorAll('.addEndpointHeaderButton');
addEndpointHeaderButtons.forEach((button) => {
    const entityId = button.getAttribute('data-entity-id');
    const form = document.getElementById('addEndpointHeaderModal');
    button.addEventListener('click', (event) => {
        form.querySelector('input[name="dummyApiEndpointId"]').value = entityId;
    });
});
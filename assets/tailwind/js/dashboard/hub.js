const publishForm = document.querySelector('form[name="publish-message"]');

publishForm.addEventListener('submit', async e => {
    e.preventDefault();

    const response = await fetch(publishForm.getAttribute('action'), {
        method: 'POST',
        body: JSON.stringify({
            message: publishForm.querySelector('textarea').value,
            'id': publishForm.querySelector('select').value,
        })
    });

    const data = await response.json();
    showToast(document.getElementById('toast-success'), data.message)
});


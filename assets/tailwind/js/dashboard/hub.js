const publishForm = document.querySelector('form[name="publish-message"]');
const selectors = document.querySelectorAll('.reply');

if (selectors && publishForm) {
    [...selectors].forEach(reply => {
        reply.addEventListener('click', (e) => {
            e.preventDefault();

            const id = reply.getAttribute('data-id');

            publishForm.classList.replace('hidden', 'block');
            publishForm.querySelector('textarea').focus();
            publishForm.querySelector('input[name="recipient"]').setAttribute('value', reply.getAttribute('data-recipient'));
            publishForm.querySelector('input[name="hub"]').setAttribute('value', reply.getAttribute('data-hub'));
            publishForm.querySelector('input[name="id"]').setAttribute('value', id);
            reply.closest(`#dropdownDots-${id}`).previousElementSibling.click();
        });
    });

    publishForm.addEventListener('submit', async e => {
        e.preventDefault();

        const response = await fetch(publishForm.getAttribute('action'), {
            method: 'POST',
            body: JSON.stringify({
                message: publishForm.querySelector('textarea').value,
                recipient: publishForm.querySelector('input[name="recipient"]').value,
                hub: publishForm.querySelector('input[name="hub"]').value,
                id: publishForm.querySelector('input[name="id"]').value,
            })
        });

        publishForm.classList.add('hidden');

        const data = await response.json();
        showToast(document.getElementById('toast-success'), data.message)
    });
}

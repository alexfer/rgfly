const entry = document.getElementById('file-upload'),
    dropzone = document.getElementById('dropzone'),
    upload = document.getElementById('upload'),
    preview = document.getElementById('preview'),
    progressArea = document.querySelector('.progress-area'),
    uploadedArea = document.querySelector('.uploaded-area');

const allowed = ['text/xml', 'text/csv'];
let xhr = new XMLHttpRequest();
const formData = new FormData();

dropzone.addEventListener('dragover', e => {
    e.preventDefault();
    dropzone.classList.add('bg-yellow-50');
});

dropzone.addEventListener('dragleave', e => {
    e.preventDefault();
    dropzone.classList.remove('bg-yellow-50');
});

dropzone.addEventListener('drop', e => {
    e.preventDefault();
    dropzone.classList.remove('bg-yellow-50');
    const file = e.dataTransfer.files[0];
    displayPreview(file);
});


entry.addEventListener('change', e => {
    let file = e.target.files[0];

    if (!allowed.includes(file.type)) {
        upload.classList.add('pointer-events-none');
        dropzone.classList.remove('bg-yellow-50');
        return false;
    }
    displayPreview(file);
    dropzone.classList.add('bg-yellow-50');
    upload.classList.remove('pointer-events-none');
    formData.append('file', file);
});

upload.addEventListener('click', e => {
    xhr.open('POST', dropzone.dataset.url);
    xhr.upload.addEventListener('progress', ({loaded, total}) => {
        let fileLoaded = Math.floor((loaded / total) * 100);
        let fileTotal = Math.floor(total / 1000);
        let fileSize;
        (fileTotal < 1024) ? fileSize = fileTotal + ' KB' : fileSize = (loaded / (1024 * 1024)).toFixed(2) + ' MB';

        let progressHTML = `<div class="w-full bg-gray-200 rounded-full dark:bg-gray-700">` +
            `<div class="bg-blue-600 text-xs font-medium text-blue-100 text-center p-0.5 leading-none rounded-full" style="width: ${fileLoaded}%"> ${fileLoaded}%</div>` +
            `</div>`;

        uploadedArea.classList.add('onprogress');
        progressArea.innerHTML = progressHTML;

        if (loaded === total) {
            progressArea.innerHTML = null;
            let uploadedHTML = `${fileSize}`;
            uploadedArea.classList.remove('onprogress');
            uploadedArea.insertAdjacentHTML('afterbegin', uploadedHTML);
        }
    });

    xhr.send(formData);

    xhr.onload = (e) => {
        const jsonResponse = JSON.parse(xhr.response);
        console.log(jsonResponse.error);
    }
});


const displayPreview = file => {
    if (!allowed.includes(file.type)) {
        upload.classList.add('pointer-events-none');
        return false;
    }
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = () => {
        preview.textContent = file.name;
        preview.classList.remove('hidden');
        upload.classList.remove('pointer-events-none');
    };
}
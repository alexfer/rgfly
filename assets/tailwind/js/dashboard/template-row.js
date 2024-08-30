const templateRow = (table, data) => {
    let c = [];

    const svg = `<svg width="800px" height="800px" viewBox="0 0 1024 1024" fill="#D2122E"  class="flex h-5 w-5"  version="1.1" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M512 897.6c-108 0-209.6-42.4-285.6-118.4-76-76-118.4-177.6-118.4-285.6 0-108 42.4-209.6 118.4-285.6 76-76 177.6-118.4 285.6-118.4 108 0 209.6 42.4 285.6 118.4 157.6 157.6 157.6 413.6 0 571.2-76 76-177.6 118.4-285.6 118.4z m0-760c-95.2 0-184.8 36.8-252 104-67.2 67.2-104 156.8-104 252s36.8 184.8 104 252c67.2 67.2 156.8 104 252 104 95.2 0 184.8-36.8 252-104 139.2-139.2 139.2-364.8 0-504-67.2-67.2-156.8-104-252-104z" fill="" />
                                    <path d="M707.872 329.392L348.096 689.16l-31.68-31.68 359.776-359.768z" fill="" />
                                    <path d="M328 340.8l32-31.2 348 348-32 32z" fill="" />
                                </svg>`;

    const rowClasses = ['bg-white', 'border-b', 'dark:bg-gray-800', 'dark:border-gray-700', 'hover:bg-gray-50', 'dark:hover:bg-gray-600'];
    const thClasses = ['px-6', 'py-3', 'font-semibold', 'text-gray-900', 'whitespace-nowrap', 'dark:text-white'];
    const tdClasses = ['px-6', 'py-3', 'text-center'];
    const handleClasses = ['flex', 'justify-end', 'px-6', 'py-3', 'me-5'];
    const row = table.insertRow(0);

    const link = document.createElement('a');
    link.setAttribute('data-url', data.url);
    link.setAttribute('role', 'button');
    link.classList.add('remove');
    link.innerHTML = svg;

    row.classList.add(...rowClasses);

    c[0] = row.insertCell(0);
    c[0].classList.add(...thClasses);
    c[0].innerHTML = `${data.store}`;

    c[1] = row.insertCell(1);
    c[1].classList.add(...tdClasses);
    c[1].innerHTML = `${data.format}`;

    c[2] = row.insertCell(2);
    c[2].classList.add(...tdClasses);
    c[2].innerHTML = `${data.createdAt}`;

    c[3] = row.insertCell(3);
    c[3].classList.add(...tdClasses);
    c[3].innerHTML = `${data.filename}`;

    c[4] = row.insertCell(4);
    c[4].classList.add(...handleClasses);
    c[4].appendChild(link);
}
export default templateRow;
const cardContainer = document.getElementById('card-container');
const loadMore = document.getElementById('load-more');

if (loadMore !== null) {
    loadMore.addEventListener('click', () => {
        let url = loadMore.dataset.url;
        const rows = parseInt(loadMore.dataset.rows);
        const limit = parseInt(loadMore.dataset.limit);
        const offsetData = parseInt(loadMore.dataset.offset);

        let offset = Math.ceil((rows + offsetData) / limit);

        const options = {
            limit: limit.toString(),
            offset: offset.toString(),
        }
        const query = new URLSearchParams(options);
        url = url + '?' + query.toString()

        fetch(url, {headers: {'Content-Type': 'application/json'}})
            .then(res => res.json())
            .then(data => {
                let template = data.template
                cardContainer.innerHTML += template;
                loadMore.dataset.offset = data.offset;
            });
    })
}
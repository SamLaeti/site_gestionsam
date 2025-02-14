document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="query"]');
    const resultsList = document.getElementById('results-list');

    searchInput.addEventListener('input', function() {
        const query = searchInput.value;
        if (query.length > 0) {
            fetch(`search_dossier.php?query=${query}&ajax=1`)
                .then(response => response.text())
                .then(data => {
                    resultsList.innerHTML = data;
                });
        } else {
            resultsList.innerHTML = '';
        }
    });
});
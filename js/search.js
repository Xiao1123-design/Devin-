async function handleSearch(event) {
    if (event && event.key === 'Enter' || !event) {
        const searchTerm = document.getElementById('globalSearch').value;
        const category = document.getElementById('categoryFilter').value;
        
        window.location.href = `browse.php?search=${encodeURIComponent(searchTerm)}&category=${encodeURIComponent(category)}`;
    }
}

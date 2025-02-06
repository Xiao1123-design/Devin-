function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert-popup ${type}`;
    alertDiv.innerHTML = `
        <div class="alert-content">
            <p>${message}</p>
            <button onclick="this.parentElement.parentElement.remove()">×</button>
        </div>
    `;
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.classList.add('show');
    }, 100);
    
    setTimeout(() => {
        alertDiv.classList.remove('show');
        setTimeout(() => alertDiv.remove(), 300);
    }, 3000);
}

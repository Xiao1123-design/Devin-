function showRatingForm() {
    document.getElementById('ratingModal').style.display = 'block';
}

function closeRatingModal() {
    document.getElementById('ratingModal').style.display = 'none';
}

async function submitRating(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    
    try {
        const response = await fetch('anonymous_rating.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        if (data.success) {
            showAlert(data.message, 'success');
            closeRatingModal();
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert(data.message, 'error');
        }
    } catch (error) {
        showAlert('评价失败，请重试', 'error');
    }
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('ratingModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}

// Close modal when clicking close button
document.querySelector('.close').onclick = closeRatingModal;

<?php
function display_error($error_code) {
    switch($error_code) {
        // Authentication errors
        case 'invalid_credentials':
            return "Invalid username or password";
        case 'user_exists':
            return "Username or email already exists";
        case 'registration_failed':
            return "Failed to create account. Please try again";
            
        // Profile errors
        case 'current_password':
            return "Current password is incorrect";
        case 'password_match':
            return "New passwords do not match";
        case 'update_failed':
            return "Failed to update profile";
        case 'duplicate_credentials':
            return "Username or email already taken";
            
        // Product errors
        case 'not_image':
            return "File uploaded is not an image";
        case 'file_too_large':
            return "File size is too large (max 5MB)";
        case 'invalid_format':
            return "Invalid file format (allowed: jpg, jpeg, png, gif)";
        case 'upload_failed':
            return "Failed to upload image";
            
        // Review errors
        case 'invalid_rating':
            return "Invalid rating value";
        case 'self_review':
            return "You cannot review yourself";
        case 'already_reviewed':
            return "You have already reviewed this seller";
        case 'review_failed':
            return "Failed to submit review";
            
        // Admin errors
        case 'cannot_delete_self':
            return "You cannot delete your own account";
        case 'delete_failed':
            return "Failed to delete user";
        case 'password_required':
            return "Password is required for new users";
            
        default:
            return "An error occurred. Please try again";
    }
}

function display_success($success_code) {
    switch($success_code) {
        case 'registration_complete':
            return "Registration successful! Please login";
        case 'profile_updated':
            return "Profile updated successfully";
        case 'password_updated':
            return "Password updated successfully";
        case 'product_listed':
            return "Product listed successfully";
        case 'request_posted':
            return "Request posted successfully";
        case 'donation_posted':
            return "Donation posted successfully";
        case 'review_posted':
            return "Review submitted successfully";
        case 'user_added':
            return "User added successfully";
        case 'user_updated':
            return "User updated successfully";
        case 'user_deleted':
            return "User deleted successfully";
            
        default:
            return "Operation completed successfully";
    }
}
?>

CREATE TABLE IF NOT EXISTS anonymous_ratings (
    rating_id INT PRIMARY KEY AUTO_INCREMENT,
    rater_id INT NOT NULL,
    rated_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rater_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (rated_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_rating (rater_id, rated_id)
);

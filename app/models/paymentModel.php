<?php

require_once 'db.php';   

function getMonthlyRevenue() {
    global $conn;

    if (!$conn) {
        return 0; // safety fallback
    }

    $sql = "
        SELECT IFNULL(SUM(amount),0) AS monthly_total
        FROM payments
        WHERE payment_status = 'success'
        AND MONTH(paid_at) = MONTH(CURRENT_DATE())
        AND YEAR(paid_at) = YEAR(CURRENT_DATE())
    ";

    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    return $row['monthly_total'];
}

    

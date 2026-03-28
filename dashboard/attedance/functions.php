<?php
/**
 * Get number of days in month
 */
function days_in_month($month, $year) {
    return cal_days_in_month(CAL_GREGORIAN, (int)$month, (int)$year);
}

/**
 * Get attendance record for a student for a given period (year_month)
 * Returns array of statuses (P/A) OR false if no record
 */
function get_attendance_record($pdo, $student_id, $year_month) {
    $stmt = $pdo->prepare("SELECT record FROM attendance_monthly WHERE student_id = ? AND `year_month` = ?");
    $stmt->execute([$student_id, $year_month]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        return str_split($row['record']); // string → array
    }
    return false; // no record yet
}

/**
 * Create a new blank attendance record (all A’s)
 */
function create_attendance_record($pdo, $student_id, $year_month, $daysInMonth) {
    $record = array_fill(0, $daysInMonth, '-'); // default all absent
    $recordString = implode("", $record);

    $stmt = $pdo->prepare("INSERT INTO attendance_monthly (student_id, `year_month`, record, updated_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$student_id, $year_month, $recordString]);

    return $record; // return array for immediate use
}

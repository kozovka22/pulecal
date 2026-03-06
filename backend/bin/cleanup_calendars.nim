import os, db_connector/db_mysql, times, strutils

let user = getEnv("MYSQL_USER", "admin")
let pass = getEnv("MYSQL_PASSWORD", "admin")
let host = getEnv("MYSQL_HOST", "db")
let dbName = getEnv("MYSQL_DATABASE", "main_db")

echo "Connecting to ", host, " as ", user

let db = open(host, user, pass, dbName)

let oneMonthAgo = now() - 1.months
let timestamp = oneMonthAgo.format("yyyy-MM-dd HH:mm:ss")

echo "Checking for calendars deactivated before ", timestamp

let rows = db.getAllRows(sql"SELECT id FROM calendar WHERE active = 0 AND deactivated_at < ?", timestamp)

for row in rows:
    let calId = row[0]
    echo "Deleting calendar ID: ", calId
    
    db.exec(sql"DELETE FROM calendar_event WHERE calendar_id = ?", calId)
    
    db.exec(sql"DELETE FROM calendar_user WHERE calendar_id = ?", calId)
    
    db.exec(sql"DELETE FROM calendar WHERE id = ?", calId)

echo "Deleting orphaned events (not linked to any calendar)..."

db.exec(sql"DELETE FROM event WHERE id NOT IN (SELECT DISTINCT event_id FROM calendar_event)")

db.close()
echo "Cleanup finished."

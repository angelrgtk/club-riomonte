<?php

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Club_Riomonte_Database
{

    private static $table_name;

    public static function init()
    {
        global $wpdb;
        self::$table_name = $wpdb->prefix . 'club_riomonte_members';
    }

    public static function create_table()
    {
        global $wpdb;

        self::init();
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS " . self::$table_name . " (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            gov_id text NOT NULL,
            first_name text NOT NULL,
            last_name text NOT NULL,
            birth_date date NOT NULL,
            email varchar(100) NOT NULL,
            phone text NOT NULL,
            profile_picture int(11),
            is_deleted boolean NOT NULL DEFAULT FALSE,
            expiration_date date NOT NULL,
            last_payment_date date NULL,
            notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public static function get_member($id)
    {
        global $wpdb;
        self::init();

        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM " . self::$table_name . " WHERE id = %d AND is_deleted = 0",
            $id
        ));
    }

    public static function get_member_by_search($search_value)
    {
        global $wpdb;
        self::init();

        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM " . self::$table_name . " WHERE (gov_id = %s OR id = %s) AND is_deleted = 0",
            $search_value,
            $search_value
        ));
    }

    public static function get_all_members($filters = [], $include_deleted = false)
    {
        global $wpdb;
        self::init();

        $where_clause = $include_deleted ? "" : "WHERE is_deleted = 0";

        if (!empty($filters['expiration_date'])) {
            $current_date = date('Y-m-d');
            if ($filters['expiration_date'] === 'expired') {
                $where_clause .= $include_deleted ? " WHERE" : " AND";
                $where_clause .= " expiration_date < '$current_date'";
            } elseif ($filters['expiration_date'] === 'expiring_7_days') {
                $end_date = date('Y-m-d', strtotime('+7 days'));
                $where_clause .= $include_deleted ? " WHERE" : " AND";
                $where_clause .= " expiration_date BETWEEN '$current_date' AND '$end_date'";
            } elseif ($filters['expiration_date'] === 'expiring_30_days') {
                $end_date = date('Y-m-d', strtotime('+30 days'));
                $where_clause .= $include_deleted ? " WHERE" : " AND";
                $where_clause .= " expiration_date BETWEEN '$current_date' AND '$end_date'";
            }
        }

        // Add more filter conditions here as needed

        return $wpdb->get_results(
            "SELECT * FROM " . self::$table_name . " $where_clause ORDER BY created_at DESC"
        );
    }

    public static function create_member($data)
    {
        global $wpdb;
        self::init();

        return $wpdb->insert(self::$table_name, $data);
    }

    public static function update_member($id, $data)
    {
        global $wpdb;
        self::init();

        return $wpdb->update(
            self::$table_name,
            $data,
            array('id' => $id)
        );
    }

    public static function delete_member($id)
    {
        global $wpdb;
        self::init();

        return $wpdb->update(
            self::$table_name,
            array('is_deleted' => 1),
            array('id' => $id)
        );
    }

    public static function get_table_name()
    {
        if (empty(self::$table_name)) {
            self::init();
        }
        return self::$table_name;
    }
}

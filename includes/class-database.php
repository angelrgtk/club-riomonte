<?php

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Club_Riomonte_Database
{

    private static $table_name;
    private static $notes_table_name;

    public static function init()
    {
        global $wpdb;
        self::$table_name = $wpdb->prefix . 'club_riomonte_members';
        self::$notes_table_name = $wpdb->prefix . 'club_riomonte_notes';
    }

    public static function create_table()
    {
        global $wpdb;

        self::init();
        $charset_collate = $wpdb->get_charset_collate();

        // Create members table (removing notes column)
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
            is_public boolean NOT NULL DEFAULT TRUE,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // Create notes table
        $notes_sql = "CREATE TABLE IF NOT EXISTS " . self::$notes_table_name . " (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            member_id mediumint(9) NOT NULL,
            text text NOT NULL,
            date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            FOREIGN KEY (member_id) REFERENCES " . self::$table_name . "(id) ON DELETE CASCADE
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        dbDelta($notes_sql);
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

    public static function get_member_by_public_search($search_value)
    {
        global $wpdb;
        self::init();

        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM " . self::$table_name . " WHERE gov_id = %s AND is_deleted = 0 AND is_public = 1",
            $search_value
        ));
    }

    public static function get_all_members($filters = [], $include_deleted = false)
    {
        global $wpdb;
        self::init();

        $where_conditions = [];

        // Base condition for deleted members
        if (!$include_deleted) {
            $where_conditions[] = "is_deleted = 0";
        }

        // Filter by expiration date
        if (!empty($filters['expiration_date'])) {
            $current_date = date('Y-m-d');
            if ($filters['expiration_date'] === 'expired') {
                $where_conditions[] = "expiration_date < '$current_date'";
            } elseif ($filters['expiration_date'] === 'expiring_7_days') {
                $end_date = date('Y-m-d', strtotime('+7 days'));
                $where_conditions[] = "expiration_date BETWEEN '$current_date' AND '$end_date'";
            } elseif ($filters['expiration_date'] === 'expiring_30_days') {
                $end_date = date('Y-m-d', strtotime('+30 days'));
                $where_conditions[] = "expiration_date BETWEEN '$current_date' AND '$end_date'";
            }
        }

        // Filter by search term
        if (!empty($filters['search_term'])) {
            $search_term = '%' . $wpdb->esc_like($filters['search_term']) . '%';
            $search_condition = $wpdb->prepare(
                "(gov_id LIKE %s OR email LIKE %s OR first_name LIKE %s OR last_name LIKE %s OR CONCAT(first_name, ' ', last_name) LIKE %s)",
                $search_term,
                $search_term,
                $search_term,
                $search_term,
                $search_term
            );
            $where_conditions[] = $search_condition;
        }

        // Filter by privacy (is_public)
        if (isset($filters['is_public']) && $filters['is_public'] !== '') {
            $where_conditions[] = "is_public = " . intval($filters['is_public']);
        }

        // Build WHERE clause
        $where_clause = '';
        if (!empty($where_conditions)) {
            $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
        }

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

    public static function get_notes_table_name()
    {
        if (empty(self::$notes_table_name)) {
            self::init();
        }
        return self::$notes_table_name;
    }

    // Notes CRUD methods
    public static function create_note($member_id, $text)
    {
        global $wpdb;
        self::init();

        return $wpdb->insert(
            self::$notes_table_name,
            array(
                'member_id' => $member_id,
                'text' => $text
            )
        );
    }

    public static function get_member_notes($member_id)
    {
        global $wpdb;
        self::init();

        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM " . self::$notes_table_name . " WHERE member_id = %d ORDER BY date DESC",
            $member_id
        ));
    }

    public static function get_note($note_id)
    {
        global $wpdb;
        self::init();

        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM " . self::$notes_table_name . " WHERE id = %d",
            $note_id
        ));
    }

    public static function update_note($note_id, $text)
    {
        global $wpdb;
        self::init();

        return $wpdb->update(
            self::$notes_table_name,
            array('text' => $text),
            array('id' => $note_id)
        );
    }

    public static function delete_note($note_id)
    {
        global $wpdb;
        self::init();

        return $wpdb->delete(
            self::$notes_table_name,
            array('id' => $note_id)
        );
    }
}

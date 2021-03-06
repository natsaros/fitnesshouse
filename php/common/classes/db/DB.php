<?php

class Db {
    const SETTINGS = 'settings';
    const USERS = 'users';
    const USER_META = 'user_meta';
    const PAGES = 'pages';
    const PAGE_META = 'page_meta';
    const VISITORS = 'visitors';

    const USER_GROUPS = 'user_groups';
    const USER_GROUPS_META = 'user_groups_meta';
    const UGR_ASSOC = 'ugr_assoc';
    const ACCESS_RIGHTS = 'access_rights';
    const ACCESS_RIGHTS_META = 'access_rights_meta';
    const ACR_ASSOC = 'acr_assoc';

    const POSTS = 'posts';
    const POST_META = 'post_details';
    const COMMENTS = 'comments';
    const COMMENT_META = 'comment_meta';
    const PRODUCTS = 'products';
    const PRODUCT_DETAILS = 'product_details';
    const PRODUCT_CATEGORIES = 'product_categories';
    const PROMOTION = 'promotion';

    const LESSONS = 'lessons';
    const EVENTS = 'events';

    const NEWSLETTER_EMAILS = 'newsletter_emails';
    const NEWSLETTER_CAMPAIGNS = 'newsletter_campaigns';

    const DB_ALL = 'all';
    const DB_GLOBAL = 'global';
    const DB_BLOG = 'blog';
    const DB_PRODUCT_CATALOGUE = 'product_catalogue';
    const DB_NEWSLETTER = 'newsletter';

    // The database connection
    protected static $connection;

    private static $instance;

    private $initialized;

    private static $global_tables = array(self::SETTINGS, self::USERS, self::USER_META,
        self::PAGES, self::PAGE_META,
        self::USER_GROUPS, self::USER_GROUPS_META, self::UGR_ASSOC,
        self::ACCESS_RIGHTS, self::ACCESS_RIGHTS_META, self::ACR_ASSOC,
        self::VISITORS, self::LESSONS, self::EVENTS);

    private static $blog_tables = array(self::POSTS, self::POST_META, self::COMMENTS, self::COMMENT_META);

    private static $product_tables = array(self::PRODUCTS, self::PRODUCT_DETAILS, self::PRODUCT_CATEGORIES, self::PROMOTION);

    private static $newsletter_tables = array(self::NEWSLETTER_EMAILS, self::NEWSLETTER_CAMPAIGNS);

    private $prefix;

    public $settings;
    public $users;
    public $visitors;
    public $user_meta;
    public $pages;
    public $page_meta;
    public $posts;
    public $post_meta;
    public $comments;
    public $comment_meta;
    public $products;
    public $product_details;
    public $product_categories;
    public $promotions;

    public $user_groups;
    public $user_groups_meta;
    public $ugr_assoc;
    public $access_rights;
    public $access_rights_meta;
    public $acr_assoc;

    public $lessons;
    public $events;

    public $newsletter_emails;
    public $newsletter_campaigns;

    /**
     * @return Db
     */
    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @return bool|mysqli|string
     * @throws SystemException | Exception
     */
    private static function connect() {
        // Try and connect to the database, if a connection has not been established yet
        if (!isset(self::$connection)) {
            self::$connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        }

        // If connection was not successful, handle the error
        if (self::$connection === false) {
            return mysqli_connect_error();
        }
        return self::$connection;
    }

    /**
     * @param $query
     * @param array $param_types
     * @param array $parameters
     * @return mixed|null
     * @throws SystemException
     */
    public static function deleteStmt($query, array $param_types = array(), array $parameters = array()) {
        return self::queryStmt($query, $param_types, $parameters, false, false, true);
    }

    /**
     * @param $query
     * @param array $param_types
     * @param array $parameters
     * @return mixed|null
     * @throws SystemException
     */
    public static function updateStmt($query, array $param_types, array $parameters) {
        return self::queryStmt($query, $param_types, $parameters, false, true);
    }

    /**
     * @param $query
     * @param array $param_types
     * @param array $parameters
     * @return mixed|null
     * @throws SystemException
     */
    public static function createStmt($query, array $param_types, array $parameters) {
        return self::queryStmt($query, $param_types, $parameters, true);
    }

    /**
     * @param $query
     * @return mixed|null
     * @throws SystemException
     */
    public static function selectStmtSingleNoParams($query) {
        $rows = self::selectStmt($query, array(), array());
        if ($rows == null || !$rows || count($rows) > 1) {
            return null;
        }
        return $rows[0];
    }

    /**
     * @param $query
     * @param array $param_types
     * @param array $parameters
     * @return mixed|null
     * @throws SystemException
     */
    public static function selectStmtSingle($query, array $param_types, array $parameters) {
        $rows = self::selectStmt($query, $param_types, $parameters);
        if ($rows == null || !$rows || count($rows) > 1) {
            return null;
        }
        return $rows[0];
    }

    /**
     * @param $query
     * @return mixed|null
     * @throws SystemException
     */
    public static function selectStmtNoParams($query) {
        $rows = array();
        $mysqli_result = self::queryStmt($query, array(), array());
        if ($mysqli_result) {
            // If query was successful, retrieve all the rows into an array
            while ($row = $mysqli_result->fetch_array(MYSQLI_ASSOC)) {
                $rows[] = $row;
            }
        } else {
            return null;
        }
        return $rows;
    }

    /**
     * @param $query
     * @param array $param_types
     * @param array $parameters
     * @return mixed|null
     * @throws SystemException
     */
    public static function selectStmt($query, array $param_types, array $parameters) {
        $rows = array();
        $mysqli_result = self::queryStmt($query, $param_types, $parameters);
        if ($mysqli_result) {
            // If query was successful, retrieve all the rows into an array
            while ($row = $mysqli_result->fetch_array(MYSQLI_ASSOC)) {
                $rows[] = $row;
            }
        } else {
            return null;
        }
        return $rows;
    }

    /**
     * @param $query
     * @param array $param_types
     * @param array $parameters
     * @param boolean $isCreate
     * @param boolean $isUpdate
     * @param boolean $isDelete
     * @return bool|mysqli_result
     * @throws SystemException
     */
    public static function queryStmt($query, array $param_types, array $parameters, $isCreate = false, $isUpdate = false, $isDelete = false) {
        /* Bind parameters. Types: s = string, i = integer, d = double,  b = blob */

        // Connect to the database
        $connection = self::connect();
        if ($connection == mysqli_connect_error()) {
            throw new SystemException($connection);
        }

        $param_type = '';
        $a_params = array();
        $n = count($param_types);
        if ($n > 0) {
            for ($i = 0; $i < $n; $i++) {
                $param_type .= $param_types[$i];
            }

            /* with call_user_func_array, array params must be passed by reference */
            $a_params[] = &$param_type;

            for ($i = 0; $i < $n; $i++) {
                /* with call_user_func_array, array params must be passed by reference */
                $a_params[] = &$parameters[$i];
            }
        }

        // Query the database
        $stmt = $connection->prepare($query);
        if ($stmt === false) {
            trigger_error('Wrong SQL: ' . $query . ' Error: ' . $connection->errno . ' ' . $connection->error, E_USER_ERROR);
        }
        if ($n > 0) {
            /* use call_user_func_array, as $stmt->bind_param('s', $param); does not accept params array */
            call_user_func_array(array($stmt, 'bind_param'), $a_params);
        }

        $result = $stmt->execute();

        if ($result) {
            // if it is an insert statement return the last inserted id
            if ($isCreate) {
                $mysqli_result = $stmt->insert_id;
            } else {
                $mysqli_result = $isUpdate || $isDelete ? $stmt->affected_rows : $stmt->get_result();
            }

        } else {
            throw new SystemException($connection->error);
        }
        return $mysqli_result;
    }

    /**
     * @param $query
     * @return bool|mysqli_result
     * @throws SystemException
     */
    public static function multi_query($query) {
        // Connect to the database
        $connection = self::connect();
        if ($connection == mysqli_connect_error()) {
            throw new SystemException(self::db_error());
        }
        // Query the database
        $mysqli_result = $connection->multi_query($query);
        if (!$mysqli_result) {
            throw new SystemException($connection->error);
        }
        return $mysqli_result;
    }

    /**
     * @param $value
     * @return string
     * @throws SystemException
     */
    public static function db_quote($value) {
        $connection = self::connect();
        return "'" . $connection->real_escape_string($value) . "'";
    }

    /**
     * @return string
     * @throws SystemException
     */
    public static function db_error() {
        $connection = self::connect();
        return $connection->error;
    }

    /**
     * @param $prefix
     */
    public function setPrefix($prefix) {
        $this->prefix = $prefix;
    }

    /**
     * @param $prefix
     * @param bool $set_table_names
     * @throws SystemException
     */
    public function setCustomPrefix($prefix, $set_table_names = true) {
        if (preg_match('|[^a-z0-9_]|i', $prefix)) {
            throw new SystemException('Invalid database prefix');
        }

        $this->setPrefix($prefix);

        if ($set_table_names) {
            $ALL_TABLES = self::tables(self::DB_ALL);
            foreach ($ALL_TABLES as $table => $table2prefix) {
                $updatedTable = $prefix . $table2prefix;
                switch ($table2prefix) {
                    case self::SETTINGS:
                        $this->setSettings($updatedTable);
                        break;
                    case self::USERS:
                        $this->setUsers($updatedTable);
                        break;
                    case self::USER_META:
                        $this->setUserMeta($updatedTable);
                        break;
                    case self::PAGES:
                        $this->setPages($updatedTable);
                        break;
                    case self::PAGE_META:
                        $this->setPageMeta($updatedTable);
                        break;
                    case self::POSTS:
                        $this->setPosts($updatedTable);
                        break;
                    case self::POST_META:
                        $this->setPostMeta($updatedTable);
                        break;
                    case self::COMMENTS:
                        $this->setComments($updatedTable);
                        break;
                    case self::COMMENT_META:
                        $this->setCommentMeta($updatedTable);
                        break;
                    case self::PRODUCTS:
                        $this->setProducts($updatedTable);
                        break;
                    case self::PRODUCT_DETAILS:
                        $this->setProductDetails($updatedTable);
                        break;
                    case self::PRODUCT_CATEGORIES:
                        $this->setProductCategories($updatedTable);
                        break;
                    case self::PROMOTION:
                        $this->setPromotions($updatedTable);
                        break;
                    case self::USER_GROUPS:
                        $this->setUserGroups($updatedTable);
                        break;
                    case self::USER_GROUPS_META:
                        $this->setUserGroupsMeta($updatedTable);
                        break;
                    case self::UGR_ASSOC:
                        $this->setUgrAssoc($updatedTable);
                        break;
                    case self::ACCESS_RIGHTS:
                        $this->setAccessRights($updatedTable);
                        break;
                    case self::ACCESS_RIGHTS_META:
                        $this->setAccessRightsMeta($updatedTable);
                        break;
                    case self::ACR_ASSOC:
                        $this->setAcrAssoc($updatedTable);
                        break;
                    case self::VISITORS:
                        $this->setVisitors($updatedTable);
                        break;
                    case self::LESSONS:
                        $this->setLessons($updatedTable);
                        break;
                    case self::EVENTS:
                        $this->setEvents($updatedTable);
                        break;
                    case self::NEWSLETTER_EMAILS:
                        $this->setNewsletterEmails($updatedTable);
                        break;
                    case self::NEWSLETTER_CAMPAIGNS:
                        $this->setNewsletterCampaigns($updatedTable);
                        break;
                }
            }
        }
    }

    /**
     * @param string $scope
     * @return array
     */
    private static function tables($scope = self::DB_ALL) {
        switch ($scope) {
            case self::DB_ALL:
                $tables = array_merge(self::$global_tables, self::$blog_tables, self::$product_tables, self::$newsletter_tables);
                break;
            case self::DB_GLOBAL:
                $tables = self::$global_tables;
                break;
            case self::DB_BLOG:
                $tables = self::$blog_tables;
                break;
            case self::DB_PRODUCT_CATALOGUE:
                $tables = self::$product_tables;
                break;
            case self::DB_NEWSLETTER:
                $tables = self::$newsletter_tables;
                break;
            default :
                return array();
        }
        return $tables;
    }

    /**
     * @return string|null
     */
    public static function db_schema_from_file() {
        $sql = file_get_contents(getcwd() . DS . 'conf/init.sql');
        return $sql ? $sql : null;
    }

    /**
     * @param $db DB
     * @return bool
     * @throws SystemException
     */
    public static function isInitialized($db) {
        if ($db->getInitialized() == null || !$db->getInitialized()) {
            $db->setCustomPrefix(TABLE_PREFIX);

            $rows = self::selectStmtNoParams("SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema = '" . DB_NAME . "'");
            if ($rows === false) {
                return false;
            }

            $tableNames = null;
            foreach ($rows as $row) {
                $tableNames[] = $row['TABLE_NAME'];
            }
            $db->setInitialized(isNotEmpty($tableNames) && in_array($db->settings, $tableNames));
        }
        return $db->getInitialized();
    }

    /**
     * @return mixed
     */
    public function getInitialized() {
        return $this->initialized;
    }

    /**
     * @param mixed $initialized
     */
    public function setInitialized($initialized) {
        $this->initialized = $initialized;
    }

    /**
     * @param mixed $settings
     */
    public function setSettings($settings) {
        $this->settings = $settings;
    }

    /**
     * @param mixed $users
     */
    public function setUsers($users) {
        $this->users = $users;
    }

    /**
     * @param mixed $user_meta
     */
    public function setUserMeta($user_meta) {
        $this->user_meta = $user_meta;
    }

    /**
     * @param mixed $comment_meta
     */
    public function setCommentMeta($comment_meta) {
        $this->comment_meta = $comment_meta;
    }

    /**
     * @param mixed $comments
     */
    public function setComments($comments) {
        $this->comments = $comments;
    }

    /**
     * @param mixed $posts
     */
    public function setPosts($posts) {
        $this->posts = $posts;
    }

    /**
     * @param mixed $post_meta
     */
    public function setPostMeta($post_meta) {
        $this->post_meta = $post_meta;
    }

    /**
     * @param mixed $products
     */
    public function setProducts($products) {
        $this->products = $products;
    }

    /**
     * @param mixed $product_details
     */
    public function setProductDetails($product_details) {
        $this->product_details = $product_details;
    }

    /**
     * @param mixed $product_categories
     */
    public function setProductCategories($product_categories) {
        $this->product_categories = $product_categories;
    }

    /**
     * @param mixed $promotions
     */
    public function setPromotions($promotions) {
        $this->promotions = $promotions;
    }

    /**
     * @param mixed $pages
     */
    public function setPages($pages) {
        $this->pages = $pages;
    }

    /**
     * @param mixed $page_meta
     */
    public function setPageMeta($page_meta) {
        $this->page_meta = $page_meta;
    }

    /**
     * @param mixed $user_groups
     */
    public function setUserGroups($user_groups) {
        $this->user_groups = $user_groups;
    }

    /**
     * @param mixed $user_groups_meta
     */
    public function setUserGroupsMeta($user_groups_meta) {
        $this->user_groups_meta = $user_groups_meta;
    }

    /**
     * @param mixed $ugr_assoc
     */
    public function setUgrAssoc($ugr_assoc) {
        $this->ugr_assoc = $ugr_assoc;
    }

    /**
     * @param mixed $access_rights
     */
    public function setAccessRights($access_rights) {
        $this->access_rights = $access_rights;
    }

    /**
     * @param mixed $access_rights_meta
     */
    public function setAccessRightsMeta($access_rights_meta) {
        $this->access_rights_meta = $access_rights_meta;
    }

    /**
     * @param mixed $acr_assoc
     */
    public function setAcrAssoc($acr_assoc) {
        $this->acr_assoc = $acr_assoc;
    }

    /**
     * @param mixed $visitors
     */
    public function setVisitors($visitors) {
        $this->visitors = $visitors;
    }

    /**
     * @param mixed $lessons
     */
    public function setLessons($lessons) {
        $this->lessons = $lessons;
    }

    /**
     * @param mixed $events
     */
    public function setEvents($events) {
        $this->events = $events;
    }

    /**
     * @param mixed $newsletter_emails
     */
    public function setNewsletterEmails($newsletter_emails) {
        $this->newsletter_emails = $newsletter_emails;
    }

    /**
     * @param mixed $newsletter_campaigns
     */
    public function setNewsletterCampaigns($newsletter_campaigns) {
        $this->newsletter_campaigns = $newsletter_campaigns;
    }

}
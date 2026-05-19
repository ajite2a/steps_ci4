<?php
namespace App\Libraries;

use PDO;

class AccessDB
{
    protected $pdo;
    private static $instance = null;

    /**
     * Singleton pattern - reuse single connection per request
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
        // Check if PDO ODBC driver is available
        if (!extension_loaded('pdo_odbc')) {
            throw new \Exception('PDO ODBC extension is not enabled. Please enable it in your php.ini file.');
        }
        
        // Get DSN from environment configuration using CodeIgniter's env() helper
        $dsn = env('database.default.DSN');
        
        // Fallback to default DSN if not found in env
        if (empty($dsn)) {
            $dsn = "odbc:Driver={Microsoft Access Driver (*.mdb, *.accdb)};DBQ=D:\\ci4_access\\db_steps.accdb";
        }
        
        // Remove quotes from DSN if present
        $dsn = trim($dsn, '"\'');
        
        // Attempt connection with retry to mitigate transient ODBC client-task limits
        try {
            $this->connectWithRetry($dsn);
        } catch (\Exception $e) {
            throw new \Exception('Failed to connect to Access Database: ' . $e->getMessage());
        }

        // Ensure connection is closed at script shutdown to release ODBC resources
        register_shutdown_function(function () {
            $this->closeConnection();
        });
    }

    /**
     * Attempt to connect to the DSN with a few retries on transient ODBC errors
     */
    private function connectWithRetry(string $dsn, int $retries = 3, int $delayUs = 200000)
    {
        $attempt = 0;
        $lastException = null;

        while ($attempt < $retries) {
            try {
                $this->pdo = new PDO($dsn);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->pdo->setAttribute(PDO::ATTR_PERSISTENT, false);
                return;
            } catch (\PDOException $e) {
                $lastException = $e;
                $msg = $e->getMessage();
                // If this looks like the Access 'Too many client tasks' transient issue, wait and retry
                if (stripos($msg, 'too many client tasks') !== false || stripos($msg, '-1036') !== false) {
                    usleep($delayUs);
                    $attempt++;
                    continue;
                }
                // Non-transient - rethrow immediately
                throw $e;
            }
        }

        // If we exit the loop, throw the last exception
        if ($lastException) {
            throw $lastException;
        }
    }

    /**
     * Close the database connection
     */
    public function closeConnection()
    {
        $this->pdo = null;
        self::$instance = null;
    }

    /**
     * Destructor - ensures connection is closed when object is destroyed
     */
    public function __destruct()
    {
        $this->pdo = null;
    }

    /**
     * Free a PDO statement/cursor to release ODBC resources
     */
    private function freeStmt(&$stmt)
    {
        if ($stmt) {
            try {
                $stmt->closeCursor();
            } catch (\Throwable $e) {
                // ignore
            }
            $stmt = null;
        }
    }

    public function getUsers()
    {
        $stmt = $this->pdo->query("SELECT * FROM users");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->freeStmt($stmt);
        return $rows;
    }

    public function insertUser($name, $email, $password)
    {
        $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $res = $stmt->execute([$name, $email, $password]);
        $this->freeStmt($stmt);
        return $res;
    }

    public function getUserByEmail($email)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->freeStmt($stmt);
        return $row;
    }

    public function verifyLogin($email, $password)
    {
        $user = $this->getUserByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }

    /**
     * Create a table in the Access database using a simple field map.
     * Minimal implementation: maps common types from migration-like arrays to Access SQL.
     *
     * Example $fields format:
     * [
     *   'id' => ['type'=>'INT','auto_increment'=>true],
     *   'name' => ['type'=>'VARCHAR','constraint'=>255],
     *   'note' => ['type'=>'TEXT','null'=>true],
     * ]
     */
    public function createTable(string $table, array $fields)
    {
        $cols = [];

        foreach ($fields as $name => $def) {
            $type = strtoupper($def['type'] ?? 'TEXT');

            // Auto-increment primary key for Access
            if (!empty($def['auto_increment']) && ($type === 'INT' || $type === 'INTEGER')) {
                $cols[] = "{$name} COUNTER PRIMARY KEY";
                continue;
            }

            $col = "{$name} ";

            if ($type === 'INT' || $type === 'INTEGER') {
                $col .= 'INTEGER';
            } elseif ($type === 'DATETIME') {
                $col .= 'DATETIME';
            } elseif ($type === 'TEXT') {
                // Use MEMO for longer text in Access
                $col .= 'MEMO';
            } elseif ($type === 'VARCHAR') {
                // MS Access doesn't support VARCHAR with length - just use TEXT
                $col .= 'TEXT';
            } else {
                // Fallback to TEXT for any unsupported type
                $col .= 'TEXT';
            }

            if (!empty($def['null'])) {
                $col .= ' NULL';
            } else {
                $col .= ' NOT NULL';
            }

            $cols[] = $col;
        }

        $sql = 'CREATE TABLE ' . $table . ' (' . implode(', ', $cols) . ')';

        return (bool)$this->pdo->exec($sql);
    }

    public function addColumn(string $table, string $column, string $definition = 'TEXT NULL')
    {
        return (bool)$this->pdo->exec("ALTER TABLE {$table} ADD COLUMN {$column} {$definition}");
    }

    /**
     * Drop a table if exists
     */
    public function dropTable(string $table)
    {
        $sql = 'DROP TABLE ' . $table;
        return (bool)$this->pdo->exec($sql);
    }

    // ===== SETTINGS CRUD METHODS =====

    public function getAllSettings()
    {
        $stmt = $this->pdo->query("SELECT * FROM settings ORDER BY setting_key ASC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->freeStmt($stmt);
        return $rows;
    }

    public function getSettingByKey($key)
    {
        $stmt = $this->pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->freeStmt($stmt);
        return $row ? $row['setting_value'] : null;
    }

    public function upsertSetting($key, $value)
    {
        $stmt = $this->pdo->prepare("SELECT id FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->freeStmt($stmt);

        if ($existing) {
            $stmt = $this->pdo->prepare("UPDATE settings SET setting_value = ?, updated_at = NOW() WHERE setting_key = ?");
            $res = $stmt->execute([$value, $key]);
        } else {
            $stmt = $this->pdo->prepare("INSERT INTO settings (setting_key, setting_value, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
            $res = $stmt->execute([$key, $value]);
        }
        $this->freeStmt($stmt);
        return $res;
    }

    public function deleteSetting($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM settings WHERE id = ?");
        $res = $stmt->execute([$id]);
        $this->freeStmt($stmt);
        return $res;
    }

    // ===== SUPPLIER CRUD METHODS =====

    public function getAllSuppliers()
    {
        $stmt = $this->pdo->query("SELECT * FROM suppliers ORDER BY supplier_name ASC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->freeStmt($stmt);
        return $rows;
    }

    public function getSupplierById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM suppliers WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->freeStmt($stmt);
        return $row;
    }

    public function createSupplier($supplier_name, $phone, $gst, $address)
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO suppliers (supplier_name, phone, gst, address, created_at, updated_at) 
             VALUES (?, ?, ?, ?, NOW(), NOW())"
        );
        $res = $stmt->execute([$supplier_name, $phone, $gst, $address]);
        $this->freeStmt($stmt);
        return $res;
    }

    public function updateSupplier($id, $supplier_name, $phone, $gst, $address)
    {
        $stmt = $this->pdo->prepare(
            "UPDATE suppliers SET supplier_name = ?, phone = ?, gst = ?, address = ?, updated_at = NOW() 
             WHERE id = ?"
        );
        $res = $stmt->execute([$supplier_name, $phone, $gst, $address, $id]);
        $this->freeStmt($stmt);
        return $res;
    }

    public function deleteSupplier($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM suppliers WHERE id = :id");
        $res = $stmt->execute([':id' => $id]);
        $this->freeStmt($stmt);
        return $res;
    }

    // ===== COLOR CRUD METHODS =====

    public function getAllColors()
    {
        $stmt = $this->pdo->query("SELECT * FROM colors ORDER BY color_name ASC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->freeStmt($stmt);
        return $rows;
    }

    public function getAllBrands()
    {
        $stmt = $this->pdo->query("SELECT * FROM brands ORDER BY brand_name ASC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->freeStmt($stmt);
        return $rows;
    }

    public function getAllHeels()
    {
        $stmt = $this->pdo->query("SELECT * FROM heels ORDER BY heel_name ASC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->freeStmt($stmt);
        return $rows;
    }

    public function getAllTags()
    {
        $stmt = $this->pdo->query("SELECT * FROM tags ORDER BY tag_name ASC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->freeStmt($stmt);
        return $rows;
    }

    public function getAllCategories()
    {
        $stmt = $this->pdo->query("SELECT * FROM categories ORDER BY category_name ASC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->freeStmt($stmt);
        return $rows;
    }

    public function getCategoryById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->freeStmt($stmt);
        return $row;
    }

    public function getAllProductGroups()
    {
        $stmt = $this->pdo->query("SELECT * FROM product_groups ORDER BY group_name ASC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->freeStmt($stmt);
        return $rows;
    }

    public function getColorById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM colors WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->freeStmt($stmt);
        return $row;
    }

    public function createColor($color_name)
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO colors (color_name, created_at, updated_at) 
             VALUES (:color_name, NOW(), NOW())"
        );
        $res = $stmt->execute([':color_name' => $color_name]);
        $this->freeStmt($stmt);
        return $res;
    }

    public function updateColor($id, $color_name)
    {
        $stmt = $this->pdo->prepare(
            "UPDATE colors SET color_name = :color_name, updated_at = NOW() 
             WHERE id = :id"
        );
        $res = $stmt->execute([':id' => $id, ':color_name' => $color_name]);
        $this->freeStmt($stmt);
        return $res;
    }

    public function deleteColor($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM colors WHERE id = :id");
        $res = $stmt->execute([':id' => $id]);
        $this->freeStmt($stmt);
        return $res;
    }

    /**
     * Check if color name already exists
     */
    public function colorNameExists($color_name, $excludeId = null)
    {
        $where = "(color_name = ?)";
        $params = [$color_name];
        
        if ($excludeId !== null) {
            $where .= " AND (id <> ?)";
            $params[] = $excludeId;
        }
        
        $sql = "SELECT COUNT(*) as count FROM colors WHERE " . $where;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->freeStmt($stmt);

        return $result['count'] > 0;
    }

    // ===== SIZE MASTER + SIZES CRUD =====

    public function getAllSizeMasters()
    {
        $stmt = $this->pdo->query("SELECT * FROM size_masters ORDER BY master_name ASC");
        $masters = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->freeStmt($stmt);

        // Attach sizes for each master to avoid undefined 'sizes' key in views
        $sizeStmt = $this->pdo->prepare("SELECT * FROM sizes WHERE size_master_id = ? ORDER BY size_value ASC");
        foreach ($masters as &$m) {
            $sizeStmt->execute([$m['id']]);
            $m['sizes'] = $sizeStmt->fetchAll(PDO::FETCH_ASSOC);
            $this->freeStmt($sizeStmt);
            // re-prepare because freeStmt set it to null
            $sizeStmt = $this->pdo->prepare("SELECT * FROM sizes WHERE size_master_id = ? ORDER BY size_value ASC");
        }

        // final cleanup
        $this->freeStmt($sizeStmt);

        return $masters;
    }

    public function getAllProductNames()
    {
        $stmt = $this->pdo->query("SELECT DISTINCT master_name FROM size_masters ORDER BY master_name ASC");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->freeStmt($stmt);

        $names = [];
        foreach ($results as $row) {
            $names[] = $row['master_name'];
        }

        return $names;
    }

    public function getSizeMasterById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM size_masters WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $master = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->freeStmt($stmt);

        if ($master) {
            $stmt2 = $this->pdo->prepare("SELECT * FROM sizes WHERE size_master_id = ? ORDER BY size_value ASC");
            $stmt2->execute([$id]);
            $master['sizes'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            $this->freeStmt($stmt2);
        }

        return $master;
    }

    public function getSizesByProductName($productName)
    {
        // Get the size master by product name
        $stmt = $this->pdo->prepare("SELECT id FROM size_masters WHERE master_name = ?");
        $stmt->execute([$productName]);
        $master = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->freeStmt($stmt);

        if (!$master) {
            return [];
        }

        // Get all sizes for this master
        $sizeStmt = $this->pdo->prepare("SELECT * FROM sizes WHERE size_master_id = ? ORDER BY size_value ASC");
        $sizeStmt->execute([$master['id']]);
        $sizes = $sizeStmt->fetchAll(PDO::FETCH_ASSOC);
        $this->freeStmt($sizeStmt);

        return $sizes;
    }

    public function createSizeMaster($master_name, array $sizes = [])
    {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare("INSERT INTO size_masters (master_name, created_at, updated_at) VALUES (?, NOW(), NOW())");
            $stmt->execute([$master_name]);
            $this->freeStmt($stmt);

            // get last insert id - for Access this may not work reliably; select max(id)
            $lastIdStmt = $this->pdo->query("SELECT MAX(id) AS last_id FROM size_masters");
            $lastIdRow = $lastIdStmt->fetch(PDO::FETCH_ASSOC);
            $lastId = $lastIdRow ? $lastIdRow['last_id'] : null;
            $this->freeStmt($lastIdStmt);

            $insertSizeStmt = $this->pdo->prepare("INSERT INTO sizes (size_master_id, size_value, new_size, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
            foreach ($sizes as $size) {
                $sizeValue = is_array($size) ? $size['size_value'] : $size;
                $newSize = is_array($size) ? ($size['new_size'] ?? '') : '';
                $insertSizeStmt->execute([$lastId, $sizeValue, $newSize]);
            }
            $this->freeStmt($insertSizeStmt);

            $this->pdo->commit();
            return true;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function updateSizeMaster($id, $master_name, array $sizes = [])
    {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare("UPDATE size_masters SET master_name = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$master_name, $id]);
            $this->freeStmt($stmt);

            // Delete existing sizes and re-insert
            $del = $this->pdo->prepare("DELETE FROM sizes WHERE size_master_id = ?");
            $del->execute([$id]);
            $this->freeStmt($del);

            $insertSizeStmt = $this->pdo->prepare("INSERT INTO sizes (size_master_id, size_value, new_size, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
            foreach ($sizes as $size) {
                $sizeValue = is_array($size) ? $size['size_value'] : $size;
                $newSize = is_array($size) ? ($size['new_size'] ?? '') : '';
                $insertSizeStmt->execute([$id, $sizeValue, $newSize]);
            }
            $this->freeStmt($insertSizeStmt);

            $this->pdo->commit();
            return true;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function deleteSizeMaster($id)
    {
        $this->pdo->beginTransaction();
        try {
            $delSizes = $this->pdo->prepare("DELETE FROM sizes WHERE size_master_id = ?");
            $delSizes->execute([$id]);

            $delMaster = $this->pdo->prepare("DELETE FROM size_masters WHERE id = ?");
            $delMaster->execute([$id]);

            $this->pdo->commit();
            return true;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function sizeMasterNameExists($master_name, $excludeId = null)
    {
        // Build query parts separately to work around ODBC parameter issues
        $where = "(master_name = ?)";
        $params = [$master_name];
        
        if ($excludeId !== null) {
            $where .= " AND (id <> ?)";
            $params[] = $excludeId;
        }
        
        $sql = "SELECT COUNT(*) as count FROM size_masters WHERE " . $where;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->freeStmt($stmt);
        return $result['count'] > 0;
    }

    // ===== ITEM/PRODUCT CRUD METHODS =====

    public function getAllItems(array $filters = [])
    {
        $stmt = $this->pdo->query("SELECT * FROM items ORDER BY product_code ASC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->freeStmt($stmt);
        
        // Get supplier and color names separately
        if (!empty($rows)) {
            foreach ($rows as &$row) {
                // Get supplier name if supplier_id exists
                if (!empty($row['supplier_id'])) {
                    $supplier = $this->getSupplierById($row['supplier_id']);
                    $row['supplier_name'] = $supplier ? $supplier['supplier_name'] : null;
                } else {
                    $row['supplier_name'] = null;
                }
                
                // Get color name if color_id exists
                if (!empty($row['color_id'])) {
                    $color = $this->getColorById($row['color_id']);
                    $row['color_name'] = $color ? $color['color_name'] : null;
                } else {
                    $row['color_name'] = null;
                }
            }
            unset($row);
        }

        if (empty(array_filter($filters, static fn ($value) => $value !== null && $value !== ''))) {
            return $rows;
        }

        return array_values(array_filter($rows, static function ($row) use ($filters) {
            $valueOf = static function (string $key) use ($row): string {
                return trim((string) ($row[$key] ?? ''));
            };

            if (!empty($filters['color_id']) && $valueOf('color_id') !== trim((string) $filters['color_id'])) {
                return false;
            }

            if (!empty($filters['product_group']) && $valueOf('product_group') !== trim((string) $filters['product_group'])) {
                return false;
            }

            if (!empty($filters['supplier_id']) && $valueOf('supplier_id') !== trim((string) $filters['supplier_id'])) {
                return false;
            }

            if (!empty($filters['tags']) && $valueOf('tags') !== trim((string) $filters['tags'])) {
                return false;
            }

            if (!empty($filters['article']) && strcasecmp($valueOf('article'), trim((string) $filters['article'])) !== 0) {
                return false;
            }

            if (!empty($filters['brand']) && $valueOf('brand') !== trim((string) $filters['brand'])) {
                return false;
            }

            $createdAt = $row['created_at'] ?? null;
            if (!empty($filters['created_from'])) {
                $createdTimestamp = $createdAt ? strtotime((string) $createdAt) : false;
                $fromTimestamp = strtotime(trim((string) $filters['created_from']) . ' 00:00:00');
                if ($createdTimestamp === false || $createdTimestamp < $fromTimestamp) {
                    return false;
                }
            }

            if (!empty($filters['created_to'])) {
                $createdTimestamp = $createdAt ? strtotime((string) $createdAt) : false;
                $toTimestamp = strtotime(trim((string) $filters['created_to']) . ' 23:59:59');
                if ($createdTimestamp === false || $createdTimestamp > $toTimestamp) {
                    return false;
                }
            }

            $sizeValue = $valueOf('size_from');
            if (!empty($filters['size_from']) && ($sizeValue === '' || strnatcasecmp($sizeValue, trim((string) $filters['size_from'])) < 0)) {
                return false;
            }

            if (!empty($filters['size_to']) && ($sizeValue === '' || strnatcasecmp($sizeValue, trim((string) $filters['size_to'])) > 0)) {
                return false;
            }

            return true;
        }));
    }

    public function getItemById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM items WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->freeStmt($stmt);
        
        if ($row) {
            // Get supplier name if supplier_id exists
            if (!empty($row['supplier_id'])) {
                $supplier = $this->getSupplierById($row['supplier_id']);
                $row['supplier_name'] = $supplier ? $supplier['supplier_name'] : null;
            } else {
                $row['supplier_name'] = null;
            }
            
            // Get color name if color_id exists
            if (!empty($row['color_id'])) {
                $color = $this->getColorById($row['color_id']);
                $row['color_name'] = $color ? $color['color_name'] : null;
            } else {
                $row['color_name'] = null;
            }

            // Get category name if category id exists
            if (!empty($row['category'])) {
                $category = $this->getCategoryById($row['category']);
                $row['category_name'] = $category ? $category['category_name'] : null;
            } else {
                $row['category_name'] = null;
            }
        }

        return $row;
    }

    public function createItem(
        $product_code, $product_name, $date, $supplier_id, $color_id,
        $article, $product_group, $brand, $heels, $tags, $category,
        $purchase_rate, $gst, $mrp, $display_price, $purchase_code, $from_size, $img_code
    ) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO items (
                product_code, product_name, item_date, supplier_id, color_id, article,
                product_group, brand, heels, tags, category, purchase_rate,
                gst, mrp, display_price, purchase_code, size_from, img_code, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())"
        );
        $res = $stmt->execute([
            $product_code, $product_name, $date, $supplier_id, $color_id, $article,
            $product_group, $brand, $heels, $tags, $category, $purchase_rate,
            $gst, $mrp, $display_price, $purchase_code, $from_size, $img_code
        ]);
        $this->freeStmt($stmt);
        return $res;
    }

    public function updateItem(
        $id, $product_code, $product_name, $date, $supplier_id, $color_id,
        $article, $product_group, $brand, $heels, $tags, $category,
        $purchase_rate, $gst, $mrp, $display_price, $purchase_code, $from_size, $img_code
    ) {
        $stmt = $this->pdo->prepare(
            "UPDATE items SET
                product_code = ?, product_name = ?, item_date = ?, supplier_id = ?, color_id = ?,
                article = ?, product_group = ?, brand = ?, heels = ?, tags = ?, category = ?,
                purchase_rate = ?, gst = ?, mrp = ?, display_price = ?, purchase_code = ?, size_from = ?,
                img_code = ?, updated_at = NOW()
             WHERE id = ?"
        );
        $res = $stmt->execute([
            $product_code, $product_name, $date, $supplier_id, $color_id, $article,
            $product_group, $brand, $heels, $tags, $category, $purchase_rate,
            $gst, $mrp, $display_price, $purchase_code, $from_size, $img_code, $id
        ]);
        $this->freeStmt($stmt);
        return $res;
    }

    public function updateItemsByArticleWiseMatch(
        $matchArticle,
        $matchProductName,
        $matchSupplierId,
        $date,
        $color_id,
        $article,
        $product_group,
        $brand,
        $heels,
        $tags,
        $category,
        $purchase_rate,
        $gst,
        $mrp,
        $display_price,
        $purchase_code
    ) {
        $whereParts = [];
        $whereParams = [];

        $whereParts[] = "product_name = ?";
        $whereParams[] = $matchProductName;

        $whereParts[] = "supplier_id = ?";
        $whereParams[] = $matchSupplierId;

        if ($matchArticle === null || trim((string) $matchArticle) === '') {
            $whereParts[] = "(article IS NULL OR article = '')";
        } else {
            $whereParts[] = "article = ?";
            $whereParams[] = $matchArticle;
        }

        $whereSql = implode(' AND ', $whereParts);

        $countStmt = $this->pdo->prepare("SELECT COUNT(*) AS total FROM items WHERE $whereSql");
        $countStmt->execute($whereParams);
        $countRow = $countStmt->fetch(PDO::FETCH_ASSOC);
        $this->freeStmt($countStmt);
        $matched = (int) ($countRow['total'] ?? 0);

        if ($matched <= 0) {
            return ['success' => false, 'matched' => 0];
        }

        $updateSql = "UPDATE items SET
            item_date = ?,
            color_id = ?,
            article = ?,
            product_group = ?,
            brand = ?,
            heels = ?,
            tags = ?,
            category = ?,
            purchase_rate = ?,
            gst = ?,
            mrp = ?,
            display_price = ?,
            purchase_code = ?,
            updated_at = NOW()
         WHERE $whereSql";

        $updateParams = [
            $date,
            $color_id,
            $article,
            $product_group,
            $brand,
            $heels,
            $tags,
            $category,
            $purchase_rate,
            $gst,
            $mrp,
            $display_price,
            $purchase_code
        ];

        $stmt = $this->pdo->prepare($updateSql);
        $res = $stmt->execute(array_merge($updateParams, $whereParams));
        $this->freeStmt($stmt);

        return ['success' => (bool) $res, 'matched' => $matched];
    }

    public function deleteItem($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM items WHERE id = ?");
        $res = $stmt->execute([$id]);
        $this->freeStmt($stmt);
        return $res;
    }

    /**
     * Check if product code already exists
     */
    public function productCodeExists($product_code, $excludeId = null)
    {
        $where = "(product_code = ?)";
        $params = [$product_code];
        
        if ($excludeId !== null) {
            $where .= " AND (id <> ?)";
            $params[] = $excludeId;
        }
        
        $sql = "SELECT COUNT(*) as count FROM items WHERE " . $where;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->freeStmt($stmt);

        return $result['count'] > 0;
    }

    /**
     * Private helper function to add or get an item from any table
     * Eliminates code repetition for all add* methods
     */
    private function addOrGetItem($table, $column, $value, $itemType)
    {
        try {
            // Check if item already exists
            $selectQuery = "SELECT id FROM $table WHERE $column = ?";
            $stmt = $this->pdo->prepare($selectQuery);
            $stmt->execute([$value]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->freeStmt($stmt);

            if ($existing) {
                return ['id' => $existing['id'], 'name' => $value];
            }

            // Insert new item
            $insertQuery = "INSERT INTO $table ($column) VALUES (?)";
            $stmt = $this->pdo->prepare($insertQuery);
            $stmt->execute([$value]);
            $this->freeStmt($stmt);

            // ODBC driver doesn't support lastInsertId(), so fetch the record we just inserted
            $stmt = $this->pdo->prepare("SELECT id FROM $table WHERE $column = ?");
            $stmt->execute([$value]);
            $newRecord = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->freeStmt($stmt);

            if ($newRecord) {
                return ['id' => $newRecord['id'], 'name' => $value];
            }

            // Fallback: return without ID (should rarely happen)
            return ['id' => null, 'name' => $value];
        } catch (\Exception $e) {
            throw new \Exception('Failed to add ' . $itemType . ': ' . $e->getMessage());
        }
    }

    /**
     * Add a new color to the colors table
     */
    public function addColor($colorName)
    {
        return $this->addOrGetItem('colors', 'color_name', $colorName, 'color');
    }

    /**
     * Add a new brand
     */
    public function addBrand($brandName)
    {
        return $this->addOrGetItem('brands', 'brand_name', $brandName, 'brand');
    }

    /**
     * Add a new heels type
     */
    public function addHeels($heelsName)
    {
        return $this->addOrGetItem('heels', 'heel_name', $heelsName, 'heels');
    }

    /**
     * Add new tags
     */
    public function addTags($tagsName)
    {
        return $this->addOrGetItem('tags', 'tag_name', $tagsName, 'tags');
    }

    /**
     * Add a new category
     */
    public function addCategory($categoryName)
    {
        return $this->addOrGetItem('categories', 'category_name', $categoryName, 'category');
    }

    /**
     * Add a new product group
     */
    public function addProductGroup($productGroupName)
    {
        return $this->addOrGetItem('product_groups', 'group_name', $productGroupName, 'product group');
    }

    /**
     * Get items by article
     */
    public function getItemsByArticle($article)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM items WHERE article = :article");
        $stmt->execute([':article' => $article]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->freeStmt($stmt);
        
        // Get supplier and color names separately
        if (!empty($rows)) {
            foreach ($rows as &$row) {
                // Get supplier name if supplier_id exists
                if (!empty($row['supplier_id'])) {
                    $supplier = $this->getSupplierById($row['supplier_id']);
                    $row['supplier_name'] = $supplier ? $supplier['supplier_name'] : null;
                } else {
                    $row['supplier_name'] = null;
                }
                
                // Get color name if color_id exists
                if (!empty($row['color_id'])) {
                    $color = $this->getColorById($row['color_id']);
                    $row['color_name'] = $color ? $color['color_name'] : null;
                } else {
                    $row['color_name'] = null;
                }
            }
        }
        
        return $rows;
    }
}
?>
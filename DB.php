<?php


class DB {

    /**
     * @var PDO $db
     */
    static $db = null;

    static function getDb(){
        if (is_null(self::$db)) {
            self::$db = new PDO('pgsql:host=localhost;port=5432;dbname=shop_telegram_bot;', 'postgres', 'AsDfGhJkL;"');
        }
        return self::$db;
    }

    public function getUser($user_id){
        $sql = "SELECT * FROM hr_users WHERE user_id = :user_id";
        $stmt = self::getDb()->prepare($sql);
        $stmt->bindValue(':user_id', $user_id);
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($res)) {
            return $res[0];
        }
        return null;
    }

    public function getResume($user_id){
        $sql = "SELECT * FROM hr_resume WHERE user_id = :user_id AND status = 1";
        $stmt = self::getDb()->prepare($sql);
        $stmt->bindValue(':user_id', $user_id);
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($res)) {
            return $res[0];
        }
        return null;
    }

    public function saveColumnResume($user_id, $col, $val, $status = 1){
        $sql = "SELECT * FROM hr_resume WHERE user_id = :user_id AND status = 1";
        $stmt = self::getDb()->prepare($sql);
        $stmt->bindValue(':user_id', $user_id);
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($res)) {
            $sql = "UPDATE hr_resume SET ".$col." = :val, status = :status WHERE user_id = :user_id AND status = 1";
            $stmt = self::getDb()->prepare($sql);
            $stmt->bindValue(':user_id', $user_id);
            $stmt->bindValue(':val', $val);
            $stmt->bindValue(':status', $status);
            return $stmt->execute();
        }
        $sql = "INSERT INTO hr_resume (user_id, ".$col.", status) VALUES (:user_id, :val, :status)";
        $stmt = self::getDb()->prepare($sql);
        $stmt->bindValue(':user_id', $user_id);
        $stmt->bindValue(':val', $val);
        $stmt->bindValue(':status', $status);
        return $stmt->execute();
    }

    public function saveUser($user_id, $username = null, $step = null, $created_at = null){
        $sql = "INSERT INTO hr_users (user_id, username, step, created_at) VALUES (:user_id, :username, :step, :created_at)";
        $stmt = self::getDb()->prepare($sql);
        $stmt->bindValue(':user_id', $user_id);
        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':step', $step);
        $stmt->bindValue(':created_at', $created_at);
        $res = $stmt->execute();
        if (!empty($res)) {
            return $res[0];
        }
        return null;
    }
    public function updateColumn($user_id, $table, $col, $val){
        $sql = "UPDATE ".$table." SET ".$col." = :val WHERE user_id = :user_id";
        $stmt = self::getDb()->prepare($sql);
        $stmt->bindValue(':user_id', $user_id);
        $stmt->bindValue(':val', $val);
        return $stmt->execute();
    }
}
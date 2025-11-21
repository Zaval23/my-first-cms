<?php

/**
 * Класс для обработки пользователей
 */
class User
{
    // Свойства
    /**
    * @var int ID из базы данных
    */
    public $id = null;

    /**
    * @var string логин
    */
    public $login = null;

     /**
    * @var string пароль
    */
    public $password = null;

     /**
    * @var int активен ли пользователь. по умолчанию активен
    */
    public $is_active = 1;
    
    /**
     * @var string Дата создания
     */
    public $created_at = null;

    /**
     * @var string Дата обновления
     */
    public $updated_at = null;
    
    /**
     * Создаст объект
     * 
     * @param array $data массив значений (столбцов) строки таблицы пользователей
     */
    public function __construct($data=array())
    {
        
      if (isset($data['id'])) {
          $this->id = (int) $data['id'];
      }
      
        if (isset($data['login'])) {
            $this->login = $data['login'];
        }
        
        if (isset($data['password'])) {
            $this->password = $data['password'];
        }

      if (isset($data['is_active'])) {
          $this->is_active = (int)$data['is_active'];  
      }

      if (isset($data['created_at'])) {
          $this->created_at = $data['created_at'];
      }

      if (isset($data['updated_at'])) {
          $this->updated_at = $data['updated_at'];
      }
    }


    /**
    * Устанавливаем свойства с помощью значений формы редактирования записи в заданном массиве
    *
    * @param assoc Значения записи формы
    */
    public function storeFormValues ( $params ) {

      // Сохраняем все параметры
      $this->__construct( $params );

      if ( isset($params['is_active']) ) {
        $this->is_active = 1;
      } else {
        $this->is_active = 0;
      }  

        if (isset($params['password']) && !empty($params['password'])) {
            $this->password = password_hash($params['password'], PASSWORD_DEFAULT);
        }
    }


    /**
    * Возвращаем объект пользователя соответствующий заданному ID
    *
    * @param int ID пользователя
    * @return User|false Объект пользователя или false, если запись не найдена или возникли проблемы
    */
    public static function getById($id) {
        $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
        $sql = "SELECT * FROM users WHERE id = :id";
        $st = $conn->prepare($sql);
        $st->bindValue(":id", $id, PDO::PARAM_INT);
        $st->execute();

        $row = $st->fetch();
        $conn = null;
        
        if ($row) { 
            return new User($row);
        }
    }

    /**
    * Возвращаем объект пользователя по логину
    *
    * @param string login пользователя
    * @return User|false Объект пользователя или false, если запись не найдена или возникли проблемы
    */
    public static function getByLogin($login) {
        $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
        $sql = "SELECT * FROM users WHERE login = :login";
        $st = $conn->prepare($sql);
        $st->bindValue(":login", $login, PDO::PARAM_STR);
        $st->execute();

        $row = $st->fetch();
        $conn = null;
        
        if ($row) { 
            return new User($row);
        }
        return false;
    }

     /**
     * Возвращает всех пользователей
     */
     public static function getList($numRows=1000000, $order="login ASC")
    {
        $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        $sql = "SELECT * FROM users ORDER BY $order LIMIT :numRows";
        
        $st = $conn->prepare($sql);
        $st->bindValue(":numRows", $numRows, PDO::PARAM_INT);
        $st->execute();
        
        $list = array();
        while ($row = $st->fetch()) {
            $user = new User($row);
            $list[] = $user;
        }

        $sql = "SELECT COUNT(*) AS totalRows FROM users";
        $st = $conn->prepare($sql);
        $st->execute();
        $totalRows = $st->fetch();
        $conn = null;
        
        return array(
            "results" => $list,
            "totalRows" => $totalRows[0]
        );
    }

    
    /**
    * Вставляем текущий объект в базу данных, устанавливаем его ID
    */
    public function insert()
    {
        if (!is_null($this->id)) {
            trigger_error("User::insert(): Attempt to insert a User object that already has its ID property set.", E_USER_ERROR);
        }

        $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        $sql = "INSERT INTO users (login, password, is_active) VALUES (:login, :password, :is_active)";
        $st = $conn->prepare($sql);
        $st->bindValue(":login", $this->login, PDO::PARAM_STR);
        $st->bindValue(":password", $this->password, PDO::PARAM_STR);
        $st->bindValue(":is_active", $this->is_active, PDO::PARAM_INT);
        $st->execute();
        $this->id = $conn->lastInsertId();
        $conn = null;
    }

    /**
    * Обновляем текущий объект в базе данных
    */
    public function update() {

      // Есть ли у объекта ID?
      if ( is_null( $this->id ) ) trigger_error ( "User::update(): "
              . "Attempt to update an User object "
              . "that does not have its ID property set.", E_USER_ERROR );

      $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
      if (empty($this->password)) {
        $sql = "UPDATE users SET login = :login, is_active = :is_active WHERE id = :id";
        $st = $conn->prepare($sql);
        $st->bindValue(":login", $this->login, PDO::PARAM_STR);
        $st->bindValue(":is_active", $this->is_active, PDO::PARAM_INT);
        $st->bindValue(":id", $this->id, PDO::PARAM_INT);
    } else {
        $sql = "UPDATE users SET login = :login, password = :password, is_active = :is_active WHERE id = :id";
        $st = $conn->prepare($sql);
        $st->bindValue(":login", $this->login, PDO::PARAM_STR);
        $st->bindValue(":password", $this->password, PDO::PARAM_STR);
        $st->bindValue(":is_active", $this->is_active, PDO::PARAM_INT);
        $st->bindValue(":id", $this->id, PDO::PARAM_INT);
    }
      $st->execute();
      $conn = null;
    }


    /**
    * Удаляем текущий объект из базы данных
    */
    public function delete() {

      // Есть ли у объекта ID?
      if ( is_null( $this->id ) ) trigger_error ( "User::delete(): Attempt to delete an User object that does not have its ID property set.", E_USER_ERROR );

      // Удаляем пользователя
      $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
      $st = $conn->prepare ( "DELETE FROM users WHERE id = :id LIMIT 1" );
      $st->bindValue( ":id", $this->id, PDO::PARAM_INT );
      $st->execute();
      $conn = null;
    }

    /**
     * Проверяет пароль
     */
    public function checkPassword($password)
    {
        return password_verify($password, $this->password);
    }

}
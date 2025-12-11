<?php

session_start();
require("config.php");


$action = isset($_GET['action']) ? $_GET['action'] : "";
$username = isset($_SESSION['username']) ? $_SESSION['username'] : "";

if ($action != "login" && $action != "logout" && !$username) {
    login();
    exit;
}

switch ($action) {
    case 'login':
        login();
        break;
    case 'logout':
        logout();
        break;
    case 'newArticle':
        newArticle();
        break;
    case 'editArticle':
        editArticle();
        break;
    case 'deleteArticle':
        deleteArticle();
        break;
    case 'listCategories':
        listCategories();
        break;
    case 'newCategory':
        newCategory();
        break;
    case 'editCategory':
        editCategory();
        break;
    case 'deleteCategory':
        deleteCategory();
        break;
    case 'updateArticleVisibility':
        updateArticleVisibility();
        break;
    case 'listUsers':
        listUsers();
        break;
    case 'newUser':
        newUser();
        break;
    case 'editUser':
        editUser();
        break;
    case 'deleteUser':
        deleteUser();
        break;
    case 'listSubcategories':
        listSubcategories();
        break;
    case 'newSubcategory':
        newSubcategory();
        break;
    case 'editSubcategory':
        editSubcategory();
        break;
    case 'deleteSubcategory':
        deleteSubcategory();
        break;
    default:
        listArticles();
}

/**
 * Авторизация пользователя 
 */
function login() {
    
    $results = array();
    $results['pageTitle'] = "Admin Login | Widget News";

    if (isset($_POST['login'])) {

        // Пользователь получает форму входа: попытка авторизировать пользователя

        $username = $_POST['username'];
        $password = $_POST['password'];

        // если админ

        if ($username == ADMIN_USERNAME && $password == ADMIN_PASSWORD) {
          $_SESSION['username'] = ADMIN_USERNAME;
          if (isset($_POST['ajax'])) {
                echo json_encode(['success' => true, 'message' => 'Успешный вход!', 'redirect' => 'admin.php']);
                exit;
        }
          header( "Location: admin.php");
          return;

        } else {
            $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
            $sql = "SELECT * FROM users WHERE login = :username AND is_active = 1";
            $st = $conn->prepare($sql);
            $st->bindValue(":username", $username, PDO::PARAM_STR);
            $st->execute();
            
            $user = $st->fetch(PDO::FETCH_ASSOC);
            $conn = null;

            if ($user && password_verify($password, $user['password'])) {
                // ВСЁ ОК
                $_SESSION['username'] = $user['login'];
                $_SESSION['user_id'] = $user['id'];
                if (isset($_POST['ajax'])) {
                    echo json_encode(['success' => true, 'message' => 'Успешный вход!', 'redirect' => 'admin.php']);
                    exit;
                }
                header("Location: admin.php");
                return;
            } elseif ($user && !password_verify($password, $user['password'])) {
                $results['errorMessage'] = "Неправильный пароль.";
            } else {
                $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
                $sql = "SELECT * FROM users WHERE login = :username AND is_active = 0";
                $st = $conn->prepare($sql);
                $st->bindValue(":username", $username, PDO::PARAM_STR);
                $st->execute();
                
                $inactiveUser = $st->fetch(PDO::FETCH_ASSOC);
                $conn = null;

                if ($inactiveUser && password_verify($password, $inactiveUser['password'])) {
                    // найден но неактивен
                    $results['errorMessage'] = "Ваш аккаунт деактивирован";
                } else {
                    // не найден или неверные данные
                    $results['errorMessage'] = "Неправильный логин или пароль";
                }
            }
        }
        if (isset($_POST['ajax'])) {
            echo json_encode(['success' => false, 'message' => $results['errorMessage']]);
            exit;
        }
        require(TEMPLATE_PATH . "/admin/loginForm.php");

    } else {
        if (isset($_POST['ajax'])) {
            echo json_encode(['success' => false, 'message' => $results['errorMessage']]);
            exit;
        }
        require(TEMPLATE_PATH . "/admin/loginForm.php");
    }

}


function logout() {

    unset( $_SESSION['username'] );
    header( "Location: admin.php" );
}


function newArticle() {

    $results = array();
    $results['pageTitle'] = "New Article";
    $results['formAction'] = "newArticle";

    if ( isset( $_POST['saveChanges'] ) ) {
        // Валидация совместимости категории и подкатегории
        $errors = array();
        $categoryId = isset($_POST['categoryId']) ? (int)$_POST['categoryId'] : null;
        $subcategoryId = isset($_POST['subcategoryId']) && $_POST['subcategoryId'] ? (int)$_POST['subcategoryId'] : null;
        
        if ($subcategoryId && $categoryId) {
            $subcategory = Subcategory::getById($subcategoryId);
            if (!$subcategory || $subcategory->categoryId != $categoryId) {
                $errors[] = "Ошибка: Выбранная категория не соответствует подкатегории!";
            }
        }
        
        if (!empty($errors)) {
            // Возвращаем форму с ошибками и данными
            $results['errors'] = $errors;
            $results['article'] = new Article();
            $results['article']->storeFormValues($_POST);
            
            $data = Category::getList();
            $results['categories'] = $data['results'];
            
            // Группируем подкатегории по категориям
            $subcategoryData = Subcategory::getList();
            $results['subcategoriesByCategory'] = array();
            foreach ($subcategoryData['results'] as $subcategory) {
                if (!isset($results['subcategoriesByCategory'][$subcategory->categoryId])) {
                    $results['subcategoriesByCategory'][$subcategory->categoryId] = array();
                }
                $results['subcategoriesByCategory'][$subcategory->categoryId][] = $subcategory;
            }
            
            require( TEMPLATE_PATH . "/admin/editArticle.php" );
            return;
        }
        
        // Пользователь получает форму редактирования статьи: сохраняем новую статью
        $article = new Article();
        $article->storeFormValues( $_POST );
        $article->insert();
        header( "Location: admin.php?status=changesSaved" );

    } elseif ( isset( $_POST['cancel'] ) ) {

        // Пользователь сбросил результаты редактирования: возвращаемся к списку статей
        header( "Location: admin.php" );
    } else {

        // Пользователь еще не получил форму редактирования: выводим форму
        $results['article'] = new Article;
        $data = Category::getList();
        $results['categories'] = $data['results'];
        
        // Группируем подкатегории по категориям
        $subcategoryData = Subcategory::getList();
        $results['subcategoriesByCategory'] = array();
        foreach ($subcategoryData['results'] as $subcategory) {
            if (!isset($results['subcategoriesByCategory'][$subcategory->categoryId])) {
                $results['subcategoriesByCategory'][$subcategory->categoryId] = array();
            }
            $results['subcategoriesByCategory'][$subcategory->categoryId][] = $subcategory;
        }
        
        require( TEMPLATE_PATH . "/admin/editArticle.php" );
    }
}

/**
 * Обновление видимости статьи
 */
function updateArticleVisibility() {
    if (!isset($_POST['articleId']) || !$_POST['articleId']) {
        header("Location: admin.php?action=listArticles");
        return;
    }

    $article = Article::getById((int)$_POST['articleId']);
    if (!$article) {
        header("Location: admin.php?action=listArticles");
        return;
    }

    $article->is_visible = isset($_POST['is_visible']) ? 1 : 0;
    $article->update();

    header("Location: admin.php?action=listArticles");
    exit;
}

/**
 * Редактирование статьи
 * 
 * @return null
 */
function editArticle() {

    $results = array();
    $results['pageTitle'] = "Edit Article";
    $results['formAction'] = "editArticle";

    if (isset($_POST['saveChanges'])) {

        // Пользователь получил форму редактирования статьи: сохраняем изменения
        if ( !$article = Article::getById( (int)$_POST['articleId'] ) ) {
            header( "Location: admin.php?error=articleNotFound" );
            return;
        }

        // Валидация совместимости категории и подкатегории
        $errors = array();
        $categoryId = isset($_POST['categoryId']) ? (int)$_POST['categoryId'] : null;
        $subcategoryId = isset($_POST['subcategoryId']) && $_POST['subcategoryId'] ? (int)$_POST['subcategoryId'] : null;
        
        if ($subcategoryId && $categoryId) {
            $subcategory = Subcategory::getById($subcategoryId);
            if (!$subcategory || $subcategory->categoryId != $categoryId) {
                $errors[] = "Ошибка: Выбранная категория не соответствует подкатегории!";
            }
        }
        
        if (!empty($errors)) {
            // Возвращаем форму с ошибками и данными
            $results['errors'] = $errors;
            $article->storeFormValues($_POST);
            $results['article'] = $article;
            
            $data = Category::getList();
            $results['categories'] = $data['results'];
            
            // Группируем подкатегории по категориям
            $subcategoryData = Subcategory::getList();
            $results['subcategoriesByCategory'] = array();
            foreach ($subcategoryData['results'] as $subcategory) {
                if (!isset($results['subcategoriesByCategory'][$subcategory->categoryId])) {
                    $results['subcategoriesByCategory'][$subcategory->categoryId] = array();
                }
                $results['subcategoriesByCategory'][$subcategory->categoryId][] = $subcategory;
            }
            
            require(TEMPLATE_PATH . "/admin/editArticle.php");
            return;
        }

        $article->storeFormValues( $_POST );
        $article->update();
        header( "Location: admin.php?status=changesSaved" );

    } elseif ( isset( $_POST['cancel'] ) ) {

        // Пользователь отказался от результатов редактирования: возвращаемся к списку статей
        header( "Location: admin.php" );
    } else {

        // Пользователь еще не получил форму редактирования: выводим форму
        $results['article'] = Article::getById((int)$_GET['articleId']);
        $data = Category::getList();
        $results['categories'] = $data['results'];
        
        // Группируем подкатегории по категориям
        $subcategoryData = Subcategory::getList();
        $results['subcategoriesByCategory'] = array();
        foreach ($subcategoryData['results'] as $subcategory) {
            if (!isset($results['subcategoriesByCategory'][$subcategory->categoryId])) {
                $results['subcategoriesByCategory'][$subcategory->categoryId] = array();
            }
            $results['subcategoriesByCategory'][$subcategory->categoryId][] = $subcategory;
        }
        
        require(TEMPLATE_PATH . "/admin/editArticle.php");
    }

}


function deleteArticle() {

    if ( !$article = Article::getById( (int)$_GET['articleId'] ) ) {
        header( "Location: admin.php?error=articleNotFound" );
        return;
    }

    $article->delete();
    header( "Location: admin.php?status=articleDeleted" );
}


function listArticles() {

    $results = array();
    
    $data = Article::getListForAdmin();
    $results['articles'] = $data['results'];
    $results['totalRows'] = $data['totalRows'];
    
    $data = Category::getList();
    $results['categories'] = array();
    foreach ($data['results'] as $category) { 
        $results['categories'][$category->id] = $category;
    }
    
    $results['pageTitle'] = "Все статьи";

    if (isset($_GET['error'])) { // вывод сообщения об ошибке (если есть)
        if ($_GET['error'] == "articleNotFound") 
            $results['errorMessage'] = "Error: Article not found.";
    }

    if (isset($_GET['status'])) { // вывод сообщения (если есть)
        if ($_GET['status'] == "changesSaved") {
            $results['statusMessage'] = "Your changes have been saved.";
        }
        if ($_GET['status'] == "articleDeleted")  {
            $results['statusMessage'] = "Article deleted.";
        }
    }

    require(TEMPLATE_PATH . "/admin/listArticles.php" );
}

function listCategories() {

    $results = array();
    $data = Category::getList();
    $results['categories'] = $data['results'];
    $results['totalRows'] = $data['totalRows'];
    $results['pageTitle'] = "Article Categories";

    if ( isset( $_GET['error'] ) ) {
        if ( $_GET['error'] == "categoryNotFound" ) $results['errorMessage'] = "Error: Category not found.";
        if ( $_GET['error'] == "categoryContainsArticles" ) $results['errorMessage'] = "Error: Category contains articles. Delete the articles, or assign them to another category, before deleting this category.";
    }

    if ( isset( $_GET['status'] ) ) {
        if ( $_GET['status'] == "changesSaved" ) $results['statusMessage'] = "Your changes have been saved.";
        if ( $_GET['status'] == "categoryDeleted" ) $results['statusMessage'] = "Category deleted.";
    }

    require( TEMPLATE_PATH . "/admin/listCategories.php" );
}
	  
	  
function newCategory() {

    $results = array();
    $results['pageTitle'] = "New Article Category";
    $results['formAction'] = "newCategory";

    if ( isset( $_POST['saveChanges'] ) ) {

        // User has posted the category edit form: save the new category
        $category = new Category;
        $category->storeFormValues( $_POST );
        $category->insert();
        header( "Location: admin.php?action=listCategories&status=changesSaved" );

    } elseif ( isset( $_POST['cancel'] ) ) {

        // User has cancelled their edits: return to the category list
        header( "Location: admin.php?action=listCategories" );
    } else {

        // User has not posted the category edit form yet: display the form
        $results['category'] = new Category;
        require( TEMPLATE_PATH . "/admin/editCategory.php" );
    }

}


function editCategory() {

    $results = array();
    $results['pageTitle'] = "Edit Article Category";
    $results['formAction'] = "editCategory";

    if ( isset( $_POST['saveChanges'] ) ) {

        // User has posted the category edit form: save the category changes

        if ( !$category = Category::getById( (int)$_POST['categoryId'] ) ) {
          header( "Location: admin.php?action=listCategories&error=categoryNotFound" );
          return;
        }

        $category->storeFormValues( $_POST );
        $category->update();
        header( "Location: admin.php?action=listCategories&status=changesSaved" );

    } elseif ( isset( $_POST['cancel'] ) ) {

        // User has cancelled their edits: return to the category list
        header( "Location: admin.php?action=listCategories" );
    } else {

        // User has not posted the category edit form yet: display the form
        $results['category'] = Category::getById( (int)$_GET['categoryId'] );
        require( TEMPLATE_PATH . "/admin/editCategory.php" );
    }

}


function deleteCategory() {

    if ( !$category = Category::getById( (int)$_GET['categoryId'] ) ) {
        header( "Location: admin.php?action=listCategories&error=categoryNotFound" );
        return;
    }

    $articles = Article::getList( 1000000, $category->id );

    if ( $articles['totalRows'] > 0 ) {
        header( "Location: admin.php?action=listCategories&error=categoryContainsArticles" );
        return;
    }

    $category->delete();
    header( "Location: admin.php?action=listCategories&status=categoryDeleted" );
}

function listUsers() {

    $results = array();
    
    $data = User::getList();
    $results['users'] = $data['results'];
    $results['totalRows'] = $data['totalRows'];
    $results['pageTitle'] = "Все пользователи";
    
    if (isset($_GET['error'])) { // вывод сообщения об ошибке (если есть)
        if ($_GET['error'] == "userNotFound") 
            $results['errorMessage'] = "Error: User not found.";
    }

    if (isset($_GET['status'])) { // вывод сообщения (если есть)
        if ($_GET['status'] == "changesSaved") {
            $results['statusMessage'] = "Your changes have been saved.";
        }
        if ($_GET['status'] == "userDeleted")  {
            $results['statusMessage'] = "User deleted.";
        }
    }

    require(TEMPLATE_PATH . "/admin/listUsers.php");
}


function newUser() {
    $results = array();
    $results['pageTitle'] = "New User";
    $results['formAction'] = "newUser";

    if ( isset( $_POST['saveChanges'] ) ) {
        $user = new User;
        $user->storeFormValues( $_POST );
        $user->insert();
        header( "Location: admin.php?action=listUsers&status=changesSaved" );
    } elseif ( isset( $_POST['cancel'] ) ) {
        header( "Location: admin.php?action=listUsers" );
    } else {
        // ВАЖНО: должно быть editUser.php, а не listUsers.php
        $results['user'] = new User;
        require( TEMPLATE_PATH . "/admin/editUser.php" );
    }
}

function editUser() {

    $results = array();
    $results['pageTitle'] = "Edit User";
    $results['formAction'] = "editUser";

    if ( isset( $_POST['saveChanges'] ) ) {

        if ( !$user = User::getById( (int)$_POST['userId'] ) ) {
          header( "Location: admin.php?action=listUsers&error=userNotFound" );
          return;
        }

        $user->storeFormValues( $_POST );
        $user->update();
        header( "Location: admin.php?action=listUsers&status=changesSaved" );

    } elseif ( isset( $_POST['cancel'] ) ) {

        header( "Location: admin.php?action=listUsers" );
    } else {

        $results['user'] = User::getById( (int)$_GET['userId'] );
        require( TEMPLATE_PATH . "/admin/editUser.php" );
    }

}


function deleteUser() {

    if (!$user = User::getById((int)$_GET['userId'])) {
        header("Location: admin.php?action=listUsers&error=userNotFound");
        return;
    }

    $user->delete();
    header("Location: admin.php?action=listUsers&status=userDeleted");
}

function listSubcategories() {

    $results = array();
    $data = Subcategory::getList();
    $results['subcategories'] = $data['results'];
    $results['totalRows'] = $data['totalRows'];
    
    // Получаем категории для отображения
    $categoryData = Category::getList();
    $results['categories'] = array();
    foreach ($categoryData['results'] as $category) {
        $results['categories'][$category->id] = $category;
    }
    
    $results['pageTitle'] = "Все подкатегории";

    if (isset($_GET['error'])) {
        if ($_GET['error'] == "subcategoryNotFound") 
            $results['errorMessage'] = "Ошибка: Подкатегория не найдена.";
        if ($_GET['error'] == "subcategoryContainsArticles") 
            $results['errorMessage'] = "Ошибка: Подкатегория содержит статьи. Удалите статьи или назначьте им другую подкатегорию перед удалением этой подкатегории.";
    }

    if (isset($_GET['status'])) {
        if ($_GET['status'] == "changesSaved") {
            $results['statusMessage'] = "Изменения сохранены.";
        }
        if ($_GET['status'] == "subcategoryDeleted")  {
            $results['statusMessage'] = "Подкатегория удалена.";
        }
    }

    require(TEMPLATE_PATH . "/admin/listSubcategories.php");
}

function newSubcategory() {
    $results = array();
    $results['pageTitle'] = "Новая подкатегория";
    $results['formAction'] = "newSubcategory";

    if ( isset( $_POST['saveChanges'] ) ) {
        $subcategory = new Subcategory;
        $subcategory->storeFormValues( $_POST );
        $subcategory->insert();
        header( "Location: admin.php?action=listSubcategories&status=changesSaved" );
    } elseif ( isset( $_POST['cancel'] ) ) {
        header( "Location: admin.php?action=listSubcategories" );
    } else {
        $results['subcategory'] = new Subcategory;
        $data = Category::getList();
        $results['categories'] = $data['results'];
        require( TEMPLATE_PATH . "/admin/editSubcategory.php" );
    }
}

function editSubcategory() {

    $results = array();
    $results['pageTitle'] = "Редактировать подкатегорию";
    $results['formAction'] = "editSubcategory";

    if ( isset( $_POST['saveChanges'] ) ) {

        if ( !$subcategory = Subcategory::getById( (int)$_POST['subcategoryId'] ) ) {
          header( "Location: admin.php?action=listSubcategories&error=subcategoryNotFound" );
          return;
        }

        $subcategory->storeFormValues( $_POST );
        $subcategory->update();
        header( "Location: admin.php?action=listSubcategories&status=changesSaved" );

    } elseif ( isset( $_POST['cancel'] ) ) {

        header( "Location: admin.php?action=listSubcategories" );
    } else {

        $results['subcategory'] = Subcategory::getById( (int)$_GET['subcategoryId'] );
        $data = Category::getList();
        $results['categories'] = $data['results'];
        require( TEMPLATE_PATH . "/admin/editSubcategory.php" );
    }

}

function deleteSubcategory() {

    if ( !$subcategory = Subcategory::getById( (int)$_GET['subcategoryId'] ) ) {
        header( "Location: admin.php?action=listSubcategories&error=subcategoryNotFound" );
        return;
    }

    $articles = Article::getListForAdmin( 1000000, null, $subcategory->id );

    if ( $articles['totalRows'] > 0 ) {
        header( "Location: admin.php?action=listSubcategories&error=subcategoryContainsArticles" );
        return;
    }

    $subcategory->delete();
    header( "Location: admin.php?action=listSubcategories&status=subcategoryDeleted" );
}
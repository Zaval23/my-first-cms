<div id="adminHeader">
    <p>You are logged in as <b><?php echo htmlspecialchars($_SESSION['username']) ?></b>. 
        <a href="admin.php">Edit Articles</a> 
        <a href="admin.php?action=listCategories">Edit Categories</a>
        <a href="admin.php?action=listUsers">Edit Users</a> 
        <a href="admin.php?action=logout">Log Out</a>
    </p>
    
    <!-- Кнопки для быстрого доступа -->
    <div style="margin-top: 10px;">
        <a href="admin.php?action=newUser" style="background: #4CAF50; color: white; padding: 8px 12px; text-decoration: none; border-radius: 3px; display: inline-block; margin-right: 10px;">
            ➕ Добавить пользователя
        </a>
        <a href="admin.php?action=listUsers" style="background: #2196F3; color: white; padding: 8px 12px; text-decoration: none; border-radius: 3px; display: inline-block; margin-right: 10px;">
            👥 Все пользователи
        </a>
        <a href="admin.php?action=newArticle" style="background: #FF9800; color: white; padding: 8px 12px; text-decoration: none; border-radius: 3px; display: inline-block;">
            📝 Добавить статью
        </a>
    </div>
</div>